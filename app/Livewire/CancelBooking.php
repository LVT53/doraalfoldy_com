<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Enums\AppointmentStatus;
use App\Enums\BookingTokenType;
use App\Mail\CancellationConfirmation;
use App\Models\Appointment;
use App\Models\BookingToken;
use App\Models\Setting;
use Illuminate\Support\Facades\Mail;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.livewire')]
class CancelBooking extends Component
{
    public ?string $token = null;

    public ?BookingToken $bookingToken = null;

    public ?Appointment $appointment = null;

    public ?string $error = null;

    public bool $isValid = false;

    public bool $isCancelled = false;

    public bool $canCancel = false;

    public function mount(string $token): void
    {
        $this->token = $token;
        $this->validateToken();
    }

    private function validateToken(): void
    {
        $this->bookingToken = BookingToken::where('token', $this->token)
            ->where('type', BookingTokenType::CANCEL)
            ->first();

        if (! $this->bookingToken instanceof BookingToken) {
            $this->error = __('booking.invalid_token');

            return;
        }

        if ($this->bookingToken->used_at !== null) {
            $this->error = __('booking.token_already_used');

            return;
        }

        if ($this->bookingToken->expires_at !== null && $this->bookingToken->expires_at->isPast()) {
            $this->error = __('booking.token_expired');

            return;
        }

        $this->appointment = $this->bookingToken->appointment;

        if (! $this->appointment instanceof Appointment) {
            $this->error = __('booking.appointment_not_found');

            return;
        }

        if ($this->appointment->status === AppointmentStatus::CANCELLED) {
            $this->error = __('booking.already_cancelled');

            return;
        }

        if ($this->appointment->status === AppointmentStatus::COMPLETED) {
            $this->error = __('booking.appointment_completed');

            return;
        }

        $this->checkCancellationWindow();
        $this->isValid = true;
    }

    private function checkCancellationWindow(): void
    {
        $cancellationHours = (int) Setting::get('cancellation_window_hours', 24);
        $minimumCancelTime = $this->appointment->start_time->copy()->subHours($cancellationHours);

        $this->canCancel = now()->lessThan($minimumCancelTime);
    }

    public function confirmCancel(): void
    {
        if (! $this->isValid || ! $this->canCancel) {
            return;
        }

        if (! $this->appointment instanceof Appointment) {
            return;
        }

        $this->appointment->update([
            'status' => AppointmentStatus::CANCELLED,
        ]);

        $this->bookingToken?->update([
            'used_at' => now(),
        ]);

        Mail::to($this->appointment->user_email)
            ->send(new CancellationConfirmation($this->appointment));

        $this->isCancelled = true;
    }

    public function render()
    {
        return view('livewire.cancel-booking');
    }
}
