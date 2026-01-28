<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Enums\AppointmentStatus;
use App\Enums\BookingTokenType;
use App\Mail\RescheduleConfirmation;
use App\Models\Appointment;
use App\Models\BookingToken;
use App\Services\BarionService;
use App\Services\SlotAvailabilityService;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Mail;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.livewire')]
class RescheduleBooking extends Component
{
    public ?string $token = null;

    public ?BookingToken $bookingToken = null;

    public ?Appointment $appointment = null;

    public ?string $error = null;

    public bool $isValid = false;

    public bool $isRescheduled = false;

    public ?string $selectedDate = null;

    public ?string $selectedTime = null;

    public ?Carbon $oldStartTime = null;

    public ?Carbon $oldEndTime = null;

    public function mount(string $token): void
    {
        $this->token = $token;
        $this->validateToken();
    }

    private function validateToken(): void
    {
        $this->bookingToken = BookingToken::where('token', $this->token)
            ->where('type', BookingTokenType::RESCHEDULE)
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

        $this->isValid = true;
    }

    public function selectDate(string $date): void
    {
        $this->selectedDate = $date;
        $this->selectedTime = null;
    }

    public function selectTime(string $time): void
    {
        $this->selectedTime = $time;
    }

    public function confirmReschedule(SlotAvailabilityService $slotService, BarionService $barionService): void
    {
        if (! $this->isValid || ! $this->selectedDate || ! $this->selectedTime) {
            return;
        }

        if (! $this->appointment instanceof Appointment) {
            return;
        }

        $service = $this->appointment->service;

        if (! $service) {
            $this->error = __('booking.service_not_found');

            return;
        }

        $newStartTime = Carbon::parse("{$this->selectedDate} {$this->selectedTime}");

        if (! $slotService->isSlotAvailable($service, $newStartTime)) {
            $this->error = __('booking.slot_taken');

            return;
        }

        $this->oldStartTime = $this->appointment->start_time->copy();
        $this->oldEndTime = $this->appointment->end_time->copy();

        $newEndTime = $newStartTime->copy()->addMinutes($service->duration_minutes);

        $this->appointment->update([
            'start_time' => $newStartTime,
            'end_time' => $newEndTime,
        ]);

        $this->bookingToken?->update([
            'used_at' => now(),
        ]);

        $barionService->createMagicTokens($this->appointment);

        Mail::to($this->appointment->user_email)
            ->send(new RescheduleConfirmation($this->appointment, $this->oldStartTime, $this->oldEndTime));

        $this->isRescheduled = true;
    }

    public function getAvailableDatesProperty(): Collection
    {
        if (! $this->isValid || ! $this->appointment?->service) {
            return collect();
        }

        $slotService = app(SlotAvailabilityService::class);

        return $slotService->getAvailableDates(
            $this->appointment->service,
            Carbon::now(),
            Carbon::now()->addDays(60)
        );
    }

    public function getAvailableSlotsProperty(): Collection
    {
        if (! $this->isValid || ! $this->appointment?->service || ! $this->selectedDate) {
            return collect();
        }

        $slotService = app(SlotAvailabilityService::class);

        return $slotService->getAvailableSlots(
            $this->appointment->service,
            Carbon::parse($this->selectedDate)
        );
    }

    public function render()
    {
        return view('livewire.reschedule-booking');
    }
}
