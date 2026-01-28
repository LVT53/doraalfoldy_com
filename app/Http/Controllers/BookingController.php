<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\TransactionStatus;
use App\Models\Appointment;
use App\Services\BarionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BookingController extends Controller
{
    public function __construct(private BarionService $barionService) {}

    public function paymentSuccess(Request $request): View
    {
        $appointmentId = session('pending_appointment_id');
        $paymentId = $request->query('PaymentId');

        if (! $appointmentId && ! $paymentId) {
            return view('booking.payment-failed', ['error' => 'Invalid payment session']);
        }

        $appointment = null;

        if ($appointmentId) {
            $appointment = Appointment::find($appointmentId);
        }

        if (! $appointment && $paymentId) {
            $appointment = Appointment::whereHas('transaction', function ($query) use ($paymentId) {
                $query->where('payment_id', $paymentId);
            })->first();
        }

        if (! $appointment) {
            return view('booking.payment-failed', ['error' => 'Appointment not found']);
        }

        $transaction = $appointment->transaction;

        if (! $transaction) {
            return view('booking.payment-failed', ['error' => 'Transaction not found']);
        }

        if ($transaction->status === TransactionStatus::COMPLETED) {
            session()->forget('pending_appointment_id');

            return view('booking.payment-success', [
                'appointment' => $appointment,
                'transaction' => $transaction,
            ]);
        }

        if ($transaction->status === TransactionStatus::FAILED) {
            session()->forget('pending_appointment_id');

            return view('booking.payment-failed', [
                'error' => 'Payment was not successful',
                'appointment' => $appointment,
            ]);
        }

        return view('booking.payment-processing', [
            'appointment' => $appointment,
            'transaction' => $transaction,
        ]);
    }

    public function paymentFailed(Request $request): View
    {
        $appointmentId = session('pending_appointment_id');
        $error = $request->query('error', 'Payment failed');

        $appointment = null;
        if ($appointmentId) {
            $appointment = Appointment::find($appointmentId);
            session()->forget('pending_appointment_id');
        }

        return view('booking.payment-failed', [
            'error' => $error,
            'appointment' => $appointment,
        ]);
    }

    public function paymentStatus(Appointment $appointment): JsonResponse
    {
        $transaction = $appointment->transaction;

        if (! $transaction) {
            return response()->json(['error' => 'Transaction not found'], 404);
        }

        $barionStatus = $this->barionService->getPaymentStatus($transaction->payment_id);

        if ($barionStatus) {
            $status = $this->barionService->mapTransactionStatus($barionStatus);

            if ($status !== $transaction->status) {
                $transaction->update([
                    'status' => $status,
                    'barion_status' => $barionStatus,
                ]);

                if ($status === TransactionStatus::COMPLETED) {
                    $appointment->update(['status' => \App\Enums\AppointmentStatus::CONFIRMED]);
                    $this->barionService->consumeVoucher($appointment);
                    $this->barionService->createMagicTokens($appointment);
                } elseif ($status === TransactionStatus::FAILED) {
                    $appointment->update(['status' => \App\Enums\AppointmentStatus::CANCELLED]);
                }
            }
        }

        return response()->json([
            'status' => $transaction->status->value,
            'barion_status' => $transaction->barion_status,
            'appointment_status' => $appointment->status->value,
        ]);
    }
}
