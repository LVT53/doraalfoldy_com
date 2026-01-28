<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\AppointmentStatus;
use App\Enums\BookingTokenType;
use App\Enums\TransactionStatus;
use App\Models\Appointment;
use App\Models\BookingToken;
use App\Models\Setting;
use App\Models\Transaction;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class BarionService
{
    private string $posKey;

    public function __construct()
    {
        $this->posKey = Setting::get('barion_pos_key');

        if (empty($this->posKey)) {
            throw new \RuntimeException('Barion POS Key is not configured');
        }
    }

    /**
     * Get the base URL for Barion API based on sandbox setting.
     */
    public function getBaseUrl(): string
    {
        $isSandbox = Setting::get('barion_sandbox', true);

        return $isSandbox
            ? 'https://api.test.barion.com/v2'
            : 'https://api.barion.com/v2';
    }

    /**
     * Create a payment in Barion and return the Gateway URL.
     */
    public function createPayment(Appointment $appointment): string
    {
        $service = $appointment->service;
        $amountToPay = $this->calculatePaymentAmount($appointment);

        $paymentRequest = [
            'POSKey' => $this->posKey,
            'PaymentType' => 'Immediate',
            'ReservationPeriod' => '00:15:00',
            'GuestCheckOut' => true,
            'FundingSources' => ['All'],
            'PaymentRequestId' => 'booking_'.$appointment->id.'_'.Str::random(8),
            'PayerHint' => $appointment->user_email,
            'Transactions' => [
                [
                    'POSTransactionId' => 'TRANS_'.$appointment->id,
                    'Payee' => Setting::get('barion_payee_email', $appointment->user_email),
                    'Total' => $amountToPay,
                    'Currency' => 'HUF',
                    'Comment' => 'Foglalás: '.$service->name,
                    'Items' => [
                        [
                            'Name' => $service->name,
                            'Description' => $service->description ?? 'Szolgáltatás foglalás',
                            'Quantity' => 1,
                            'Unit' => 'db',
                            'UnitPrice' => $amountToPay,
                            'ItemTotal' => $amountToPay,
                            'SKU' => 'SERVICE_'.$service->id,
                        ],
                    ],
                ],
            ],
            'RedirectUrl' => route('booking.payment.success'),
            'CallbackUrl' => route('barion.callback'),
        ];

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->post($this->getBaseUrl().'/Payment/Start', $paymentRequest);

        if (! $response->successful()) {
            throw new \RuntimeException('Barion payment creation failed: '.$response->body());
        }

        $data = $response->json();

        if ($data['Errors'] ?? []) {
            $errorMessage = collect($data['Errors'])
                ->pluck('Description')
                ->implode(', ');
            throw new \RuntimeException('Barion payment creation failed: '.$errorMessage);
        }

        // Create transaction record
        Transaction::create([
            'payment_id' => $data['PaymentId'],
            'status' => TransactionStatus::PENDING,
            'amount' => $amountToPay,
            'payable_type' => Appointment::class,
            'payable_id' => $appointment->id,
            'barion_status' => 'Prepared',
        ]);

        return $data['GatewayUrl'];
    }

    /**
     * Calculate the amount to pay for the appointment.
     */
    private function calculatePaymentAmount(Appointment $appointment): float
    {
        $total = (float) $appointment->price_at_booking;
        $discount = (float) $appointment->voucher_discount;
        $discountedTotal = max(0, $total - $discount);

        // Pay deposit if available, otherwise full amount
        $deposit = (float) $appointment->deposit_at_booking;

        return $deposit > 0 ? min($deposit, $discountedTotal) : $discountedTotal;
    }

    /**
     * Handle Barion callback/webhook.
     */
    public function handleCallback(string $paymentId): bool
    {
        $transaction = Transaction::where('payment_id', $paymentId)->first();

        if (! $transaction) {
            \Log::warning('Barion callback: Transaction not found for PaymentId: '.$paymentId);

            return false;
        }

        $barionStatus = $this->getPaymentStatus($paymentId);

        if ($barionStatus === null) {
            return false;
        }

        $status = $this->mapTransactionStatus($barionStatus);

        // Update transaction
        $transaction->update([
            'status' => $status,
            'barion_status' => $barionStatus,
        ]);

        // Update appointment status based on payment result
        $appointment = $transaction->payable;

        if (! $appointment instanceof Appointment) {
            return false;
        }

        if ($status === TransactionStatus::COMPLETED) {
            $appointment->update(['status' => AppointmentStatus::CONFIRMED]);
            $this->consumeVoucher($appointment);
            $this->createMagicTokens($appointment);
        } elseif ($status === TransactionStatus::FAILED) {
            $appointment->update(['status' => AppointmentStatus::CANCELLED]);
        }

        return true;
    }

    /**
     * Get payment status from Barion API.
     */
    public function getPaymentStatus(string $paymentId): ?string
    {
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->get($this->getBaseUrl().'/Payment/GetPaymentState', [
            'POSKey' => $this->posKey,
            'PaymentId' => $paymentId,
        ]);

        if (! $response->successful()) {
            \Log::error('Barion status check failed: '.$response->body());

            return null;
        }

        $data = $response->json();

        if ($data['Errors'] ?? []) {
            \Log::error('Barion status check returned errors: '.json_encode($data['Errors']));

            return null;
        }

        return $data['Status'] ?? null;
    }

    /**
     * Map Barion status to our TransactionStatus enum.
     */
    public function mapTransactionStatus(string $barionStatus): TransactionStatus
    {
        return match ($barionStatus) {
            'Succeeded' => TransactionStatus::COMPLETED,
            'Prepared', 'Started', 'InProgress', 'Reserved' => TransactionStatus::PENDING,
            'Canceled', 'Expired', 'Failed' => TransactionStatus::FAILED,
            'Refunded', 'RefundedAndReturned' => TransactionStatus::REFUNDED,
            default => TransactionStatus::PENDING,
        };
    }

    /**
     * Consume voucher after successful payment.
     */
    public function consumeVoucher(Appointment $appointment): void
    {
        $voucher = $appointment->voucher;

        if (! $voucher) {
            return;
        }

        // For gift cards, reduce balance
        if ($voucher->type->value === 'gift_card') {
            $discount = (float) $appointment->voucher_discount;
            $newBalance = max(0, (float) $voucher->balance - $discount);
            $voucher->update(['balance' => $newBalance]);
        } else {
            // For percentage/fixed vouchers, mark as used
            $voucher->update(['used_at' => now()]);
        }
    }

    /**
     * Create magic tokens for cancel and reschedule.
     */
    public function createMagicTokens(Appointment $appointment): void
    {
        $tokenExpiry = now()->addDays(7);

        // Create cancel token
        BookingToken::create([
            'appointment_id' => $appointment->id,
            'token' => Str::random(64),
            'type' => BookingTokenType::CANCEL,
            'expires_at' => $tokenExpiry,
        ]);

        // Create reschedule token
        BookingToken::create([
            'appointment_id' => $appointment->id,
            'token' => Str::random(64),
            'type' => BookingTokenType::RESCHEDULE,
            'expires_at' => $tokenExpiry,
        ]);
    }
}
