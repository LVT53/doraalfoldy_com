<?php

use App\Enums\AppointmentStatus;
use App\Enums\TransactionStatus;
use App\Enums\VoucherType;
use App\Models\Appointment;
use App\Models\Setting;
use App\Models\Transaction;
use App\Models\Voucher;
use Illuminate\Support\Facades\Http;

describe('Barion Webhook', function () {
    beforeEach(function () {
        Setting::set('barion_pos_key', 'test-pos-key-12345');
        Setting::set('barion_sandbox', true);
        Http::preventStrayRequests();
    });

    it('updates transaction status on successful webhook', function () {
        // Arrange
        $appointment = Appointment::factory()->create([
            'status' => AppointmentStatus::PENDING,
            'price_at_booking' => 10000.00,
            'deposit_at_booking' => 3000.00,
        ]);

        $transaction = Transaction::factory()->create([
            'payable_type' => Appointment::class,
            'payable_id' => $appointment->id,
            'payment_id' => 'test-payment-id-123',
            'status' => TransactionStatus::PENDING,
            'amount' => 3000.00,
            'barion_status' => 'Prepared',
        ]);

        // Mock Barion API response
        Http::fake([
            'https://api.test.barion.com/v2/Payment/GetPaymentState*' => Http::response([
                'PaymentId' => 'test-payment-id-123',
                'Status' => 'Succeeded',
                'Errors' => [],
            ], 200),
        ]);

        // Act
        $response = $this->postJson('/api/barion/callback', [
            'PaymentId' => 'test-payment-id-123',
        ]);

        // Assert
        $response->assertSuccessful();

        $transaction->refresh();
        expect($transaction->status)->toBe(TransactionStatus::COMPLETED);
        expect($transaction->barion_status)->toBe('Succeeded');

        $appointment->refresh();
        expect($appointment->status)->toBe(AppointmentStatus::CONFIRMED);
    });

    it('updates appointment status to confirmed on success', function () {
        // Arrange
        $appointment = Appointment::factory()->create([
            'status' => AppointmentStatus::PENDING,
        ]);

        Transaction::factory()->create([
            'payable_type' => Appointment::class,
            'payable_id' => $appointment->id,
            'payment_id' => 'success-payment-id',
            'status' => TransactionStatus::PENDING,
        ]);

        Http::fake([
            'https://api.test.barion.com/v2/Payment/GetPaymentState*' => Http::response([
                'PaymentId' => 'success-payment-id',
                'Status' => 'Succeeded',
                'Errors' => [],
            ], 200),
        ]);

        // Act
        $this->postJson('/api/barion/callback', [
            'PaymentId' => 'success-payment-id',
        ]);

        // Assert
        expect($appointment->fresh()->status)->toBe(AppointmentStatus::CONFIRMED);
    });

    it('updates appointment status to cancelled on failure', function () {
        // Arrange
        $appointment = Appointment::factory()->create([
            'status' => AppointmentStatus::PENDING,
        ]);

        Transaction::factory()->create([
            'payable_type' => Appointment::class,
            'payable_id' => $appointment->id,
            'payment_id' => 'failed-payment-id',
            'status' => TransactionStatus::PENDING,
        ]);

        Http::fake([
            'https://api.test.barion.com/v2/Payment/GetPaymentState*' => Http::response([
                'PaymentId' => 'failed-payment-id',
                'Status' => 'Failed',
                'Errors' => [],
            ], 200),
        ]);

        // Act
        $this->postJson('/api/barion/callback', [
            'PaymentId' => 'failed-payment-id',
        ]);

        // Assert
        expect($appointment->fresh()->status)->toBe(AppointmentStatus::CANCELLED);
    });

    it('is idempotent - calling twice does not double-process', function () {
        // Arrange
        $appointment = Appointment::factory()->create([
            'status' => AppointmentStatus::PENDING,
        ]);

        Transaction::factory()->create([
            'payable_type' => Appointment::class,
            'payable_id' => $appointment->id,
            'payment_id' => 'idempotent-payment-id',
            'status' => TransactionStatus::PENDING,
        ]);

        Http::fake([
            'https://api.test.barion.com/v2/Payment/GetPaymentState*' => Http::response([
                'PaymentId' => 'idempotent-payment-id',
                'Status' => 'Succeeded',
                'Errors' => [],
            ], 200),
        ]);

        // Act - Call webhook twice
        $this->postJson('/api/barion/callback', [
            'PaymentId' => 'idempotent-payment-id',
        ]);

        $firstCallStatus = $appointment->fresh()->status;

        $this->postJson('/api/barion/callback', [
            'PaymentId' => 'idempotent-payment-id',
        ]);

        // Assert - Status should remain the same
        expect($appointment->fresh()->status)->toBe($firstCallStatus);
        expect($appointment->fresh()->status)->toBe(AppointmentStatus::CONFIRMED);
    });

    it('returns 500 for unknown PaymentId', function () {
        // Act
        $response = $this->postJson('/api/barion/callback', [
            'PaymentId' => 'unknown-payment-id',
        ]);

        // Assert
        $response->assertStatus(500);
    });

    it('returns 400 when PaymentId is missing', function () {
        // Act
        $response = $this->postJson('/api/barion/callback', []);

        // Assert
        $response->assertStatus(400);
    });

    it('consumes percentage voucher after successful payment', function () {
        // Arrange
        $voucher = Voucher::factory()->create([
            'code' => 'PERCENT10',
            'type' => VoucherType::PERCENTAGE,
            'value' => 10,
            'used_at' => null,
        ]);

        $appointment = Appointment::factory()->create([
            'status' => AppointmentStatus::PENDING,
            'voucher_id' => $voucher->id,
            'voucher_discount' => 1000.00,
        ]);

        Transaction::factory()->create([
            'payable_type' => Appointment::class,
            'payable_id' => $appointment->id,
            'payment_id' => 'voucher-payment-id',
            'status' => TransactionStatus::PENDING,
        ]);

        Http::fake([
            'https://api.test.barion.com/v2/Payment/GetPaymentState*' => Http::response([
                'PaymentId' => 'voucher-payment-id',
                'Status' => 'Succeeded',
                'Errors' => [],
            ], 200),
        ]);

        // Act
        $this->postJson('/api/barion/callback', [
            'PaymentId' => 'voucher-payment-id',
        ]);

        // Assert
        expect($voucher->fresh()->used_at)->not->toBeNull();
    });

    it('reduces gift card balance after successful payment', function () {
        // Arrange
        $voucher = Voucher::factory()->create([
            'code' => 'GIFT5000',
            'type' => VoucherType::GIFT_CARD,
            'value' => 5000,
            'balance' => 5000,
        ]);

        $appointment = Appointment::factory()->create([
            'status' => AppointmentStatus::PENDING,
            'voucher_id' => $voucher->id,
            'voucher_discount' => 3000.00,
        ]);

        Transaction::factory()->create([
            'payable_type' => Appointment::class,
            'payable_id' => $appointment->id,
            'payment_id' => 'giftcard-payment-id',
            'status' => TransactionStatus::PENDING,
        ]);

        Http::fake([
            'https://api.test.barion.com/v2/Payment/GetPaymentState*' => Http::response([
                'PaymentId' => 'giftcard-payment-id',
                'Status' => 'Succeeded',
                'Errors' => [],
            ], 200),
        ]);

        // Act
        $this->postJson('/api/barion/callback', [
            'PaymentId' => 'giftcard-payment-id',
        ]);

        // Assert
        expect((float) $voucher->fresh()->balance)->toBe(2000.00);
    });

    it('creates magic tokens after successful payment', function () {
        // Arrange
        $appointment = Appointment::factory()->create([
            'status' => AppointmentStatus::PENDING,
        ]);

        Transaction::factory()->create([
            'payable_type' => Appointment::class,
            'payable_id' => $appointment->id,
            'payment_id' => 'tokens-payment-id',
            'status' => TransactionStatus::PENDING,
        ]);

        Http::fake([
            'https://api.test.barion.com/v2/Payment/GetPaymentState*' => Http::response([
                'PaymentId' => 'tokens-payment-id',
                'Status' => 'Succeeded',
                'Errors' => [],
            ], 200),
        ]);

        // Act
        $this->postJson('/api/barion/callback', [
            'PaymentId' => 'tokens-payment-id',
        ]);

        // Assert
        $cancelToken = \App\Models\BookingToken::where('appointment_id', $appointment->id)
            ->where('type', \App\Enums\BookingTokenType::CANCEL)
            ->first();

        $rescheduleToken = \App\Models\BookingToken::where('appointment_id', $appointment->id)
            ->where('type', \App\Enums\BookingTokenType::RESCHEDULE)
            ->first();

        expect($cancelToken)->not->toBeNull();
        expect($rescheduleToken)->not->toBeNull();
    });

    it('handles Barion API errors gracefully', function () {
        // Arrange
        $appointment = Appointment::factory()->create([
            'status' => AppointmentStatus::PENDING,
        ]);

        Transaction::factory()->create([
            'payable_type' => Appointment::class,
            'payable_id' => $appointment->id,
            'payment_id' => 'error-payment-id',
            'status' => TransactionStatus::PENDING,
        ]);

        Http::fake([
            'https://api.test.barion.com/v2/Payment/GetPaymentState*' => Http::response([
                'Errors' => [
                    ['Description' => 'Invalid payment ID'],
                ],
            ], 200),
        ]);

        // Act
        $response = $this->postJson('/api/barion/callback', [
            'PaymentId' => 'error-payment-id',
        ]);

        // Assert
        $response->assertStatus(500);
        expect($appointment->fresh()->status)->toBe(AppointmentStatus::PENDING);
    });

    it('handles Barion API failure', function () {
        // Arrange
        $appointment = Appointment::factory()->create([
            'status' => AppointmentStatus::PENDING,
        ]);

        Transaction::factory()->create([
            'payable_type' => Appointment::class,
            'payable_id' => $appointment->id,
            'payment_id' => 'api-fail-payment-id',
            'status' => TransactionStatus::PENDING,
        ]);

        Http::fake([
            'https://api.test.barion.com/v2/Payment/GetPaymentState*' => Http::response(null, 500),
        ]);

        // Act
        $response = $this->postJson('/api/barion/callback', [
            'PaymentId' => 'api-fail-payment-id',
        ]);

        // Assert
        $response->assertStatus(500);
        expect($appointment->fresh()->status)->toBe(AppointmentStatus::PENDING);
    });
});
