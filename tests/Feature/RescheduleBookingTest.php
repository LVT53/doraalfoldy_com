<?php

use App\Enums\AppointmentStatus;
use App\Enums\BookingTokenType;
use App\Livewire\RescheduleBooking;
use App\Models\Appointment;
use App\Models\BookingToken;
use App\Models\Schedule;
use App\Models\Service;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;

describe('Reschedule Booking', function () {
    beforeEach(function () {
        Setting::set('slot_lock', '1');
        Setting::set('default_buffer_minutes', 0);
        Setting::set('barion_pos_key', 'test-pos-key');
        Setting::set('barion_sandbox', true);
        Mail::fake();
    });

    it('allows rescheduling within window', function () {
        // Arrange
        $service = Service::factory()->create([
            'duration_minutes' => 60,
            'is_active' => true,
        ]);

        $appointment = Appointment::factory()->create([
            'service_id' => $service->id,
            'start_time' => Carbon::now()->addHours(48),
            'end_time' => Carbon::now()->addHours(49),
            'status' => AppointmentStatus::CONFIRMED,
        ]);

        $token = BookingToken::factory()->create([
            'appointment_id' => $appointment->id,
            'type' => BookingTokenType::RESCHEDULE,
            'token' => 'valid-reschedule-token',
            'expires_at' => Carbon::now()->addDays(7),
            'used_at' => null,
        ]);

        // Create schedule for new date
        $newDate = Carbon::now()->addDays(7);
        Schedule::factory()->create([
            'day_of_week' => (int) $newDate->format('w'),
            'start_time' => '09:00',
            'end_time' => '17:00',
            'is_off' => false,
        ]);

        // Act
        $component = Livewire::test(RescheduleBooking::class, ['token' => 'valid-reschedule-token']);

        // Assert
        expect($component->get('isValid'))->toBeTrue();

        // Select new date and time (use 10:00 which should be available)
        $component->call('selectDate', $newDate->format('Y-m-d'))
            ->call('selectTime', '10:00')
            ->call('confirmReschedule');

        // Assert
        expect($component->get('isRescheduled'))->toBeTrue();

        $appointment->refresh();
        expect($appointment->start_time->format('Y-m-d'))->toBe($newDate->format('Y-m-d'));
        expect($appointment->start_time->format('H:i'))->toBe('10:00');
        expect($token->fresh()->used_at)->not->toBeNull();

        // Email was sent
        Mail::assertSent(\App\Mail\RescheduleConfirmation::class);
    });

    it('prevents rescheduling outside window', function () {
        // Arrange - Appointment is within 24 hours
        $service = Service::factory()->create([
            'duration_minutes' => 60,
            'is_active' => true,
        ]);

        $appointment = Appointment::factory()->create([
            'service_id' => $service->id,
            'start_time' => Carbon::now()->addHours(12),
            'end_time' => Carbon::now()->addHours(13),
            'status' => AppointmentStatus::CONFIRMED,
        ]);

        $token = BookingToken::factory()->create([
            'appointment_id' => $appointment->id,
            'type' => BookingTokenType::RESCHEDULE,
            'token' => 'window-token',
            'expires_at' => Carbon::now()->addDays(7),
            'used_at' => null,
        ]);

        // Act
        $component = Livewire::test(RescheduleBooking::class, ['token' => 'window-token']);

        // Assert - Token is valid but rescheduling is not allowed
        expect($component->get('isValid'))->toBeTrue();

        // Create schedule for new date
        $newDate = Carbon::now()->addDays(7);
        Schedule::factory()->create([
            'day_of_week' => (int) $newDate->format('w'),
            'start_time' => '09:00',
            'end_time' => '17:00',
            'is_off' => false,
        ]);

        // Try to reschedule (use 10:00 which should be available)
        $component->call('selectDate', $newDate->format('Y-m-d'))
            ->call('selectTime', '10:00')
            ->call('confirmReschedule');

        // Assert - Appointment not rescheduled (because outside window, not because of slot)
        expect($component->get('isRescheduled'))->toBeFalse();
        expect($appointment->fresh()->start_time)->toEqual($appointment->start_time);
    });

    it('rejects invalid token', function () {
        // Act
        $component = Livewire::test(RescheduleBooking::class, ['token' => 'invalid-token']);

        // Assert
        expect($component->get('isValid'))->toBeFalse();
        expect($component->get('error'))->not->toBeNull();
    });

    it('rejects expired token', function () {
        // Arrange
        $service = Service::factory()->create([
            'duration_minutes' => 60,
            'is_active' => true,
        ]);

        $appointment = Appointment::factory()->create([
            'service_id' => $service->id,
            'start_time' => Carbon::now()->addHours(48),
            'status' => AppointmentStatus::CONFIRMED,
        ]);

        $token = BookingToken::factory()->create([
            'appointment_id' => $appointment->id,
            'type' => BookingTokenType::RESCHEDULE,
            'token' => 'expired-token',
            'expires_at' => Carbon::now()->subDay(),
            'used_at' => null,
        ]);

        // Act
        $component = Livewire::test(RescheduleBooking::class, ['token' => 'expired-token']);

        // Assert
        expect($component->get('isValid'))->toBeFalse();
        expect($component->get('error'))->not->toBeNull();
    });

    it('rejects already used token', function () {
        // Arrange
        $service = Service::factory()->create([
            'duration_minutes' => 60,
            'is_active' => true,
        ]);

        $appointment = Appointment::factory()->create([
            'service_id' => $service->id,
            'start_time' => Carbon::now()->addHours(48),
            'status' => AppointmentStatus::CONFIRMED,
        ]);

        $token = BookingToken::factory()->create([
            'appointment_id' => $appointment->id,
            'type' => BookingTokenType::RESCHEDULE,
            'token' => 'used-token',
            'expires_at' => Carbon::now()->addDays(7),
            'used_at' => Carbon::now()->subDay(),
        ]);

        // Act
        $component = Livewire::test(RescheduleBooking::class, ['token' => 'used-token']);

        // Assert
        expect($component->get('isValid'))->toBeFalse();
        expect($component->get('error'))->not->toBeNull();
    });

    it('rejects rescheduling to unavailable slot', function () {
        // Arrange
        $service = Service::factory()->create([
            'duration_minutes' => 60,
            'is_active' => true,
        ]);

        $appointment = Appointment::factory()->create([
            'service_id' => $service->id,
            'start_time' => Carbon::now()->addHours(48),
            'end_time' => Carbon::now()->addHours(49),
            'status' => AppointmentStatus::CONFIRMED,
        ]);

        // Create another appointment at the target time
        $targetDate = Carbon::now()->addDays(7);
        Appointment::factory()->create([
            'service_id' => $service->id,
            'start_time' => $targetDate->copy()->setTime(14, 0),
            'end_time' => $targetDate->copy()->setTime(15, 0),
            'status' => AppointmentStatus::CONFIRMED,
        ]);

        $token = BookingToken::factory()->create([
            'appointment_id' => $appointment->id,
            'type' => BookingTokenType::RESCHEDULE,
            'token' => 'conflict-token',
            'expires_at' => Carbon::now()->addDays(7),
            'used_at' => null,
        ]);

        // Create schedule for new date
        Schedule::factory()->create([
            'day_of_week' => (int) $targetDate->format('w'),
            'start_time' => '09:00',
            'end_time' => '17:00',
            'is_off' => false,
        ]);

        // Act
        $component = Livewire::test(RescheduleBooking::class, ['token' => 'conflict-token']);

        // Try to reschedule to conflicting slot
        $component->call('selectDate', $targetDate->format('Y-m-d'))
            ->call('selectTime', '14:00')
            ->call('confirmReschedule');

        // Assert - Appointment not rescheduled
        expect($component->get('isRescheduled'))->toBeFalse();
        expect($component->get('error'))->not->toBeNull();
    });

    it('creates new tokens after rescheduling', function () {
        // Arrange
        $service = Service::factory()->create([
            'duration_minutes' => 60,
            'is_active' => true,
        ]);

        $appointment = Appointment::factory()->create([
            'service_id' => $service->id,
            'start_time' => Carbon::now()->addHours(48),
            'end_time' => Carbon::now()->addHours(49),
            'status' => AppointmentStatus::CONFIRMED,
        ]);

        $token = BookingToken::factory()->create([
            'appointment_id' => $appointment->id,
            'type' => BookingTokenType::RESCHEDULE,
            'token' => 'reschedule-token',
            'expires_at' => Carbon::now()->addDays(7),
            'used_at' => null,
        ]);

        // Create schedule for new date
        $newDate = Carbon::now()->addDays(7);
        Schedule::factory()->create([
            'day_of_week' => (int) $newDate->format('w'),
            'start_time' => '09:00',
            'end_time' => '17:00',
            'is_off' => false,
        ]);

        // Act
        $component = Livewire::test(RescheduleBooking::class, ['token' => 'reschedule-token']);

        $component->call('selectDate', $newDate->format('Y-m-d'))
            ->call('selectTime', '14:00')
            ->call('confirmReschedule');

        // Assert
        expect($component->get('isRescheduled'))->toBeTrue();

        // New tokens should be created
        $cancelToken = BookingToken::where('appointment_id', $appointment->id)
            ->where('type', BookingTokenType::CANCEL)
            ->whereNull('used_at')
            ->first();

        $rescheduleToken = BookingToken::where('appointment_id', $appointment->id)
            ->where('type', BookingTokenType::RESCHEDULE)
            ->whereNull('used_at')
            ->first();

        expect($cancelToken)->not->toBeNull();
        expect($rescheduleToken)->not->toBeNull();
    });

    it('rejects rescheduling cancelled appointment', function () {
        // Arrange
        $service = Service::factory()->create([
            'duration_minutes' => 60,
            'is_active' => true,
        ]);

        $appointment = Appointment::factory()->create([
            'service_id' => $service->id,
            'start_time' => Carbon::now()->addHours(48),
            'status' => AppointmentStatus::CANCELLED,
        ]);

        $token = BookingToken::factory()->create([
            'appointment_id' => $appointment->id,
            'type' => BookingTokenType::RESCHEDULE,
            'token' => 'cancelled-token',
            'expires_at' => Carbon::now()->addDays(7),
            'used_at' => null,
        ]);

        // Act
        $component = Livewire::test(RescheduleBooking::class, ['token' => 'cancelled-token']);

        // Assert
        expect($component->get('isValid'))->toBeFalse();
        expect($component->get('error'))->not->toBeNull();
    });

    it('rejects rescheduling completed appointment', function () {
        // Arrange
        $service = Service::factory()->create([
            'duration_minutes' => 60,
            'is_active' => true,
        ]);

        $appointment = Appointment::factory()->create([
            'service_id' => $service->id,
            'start_time' => Carbon::now()->subHours(24),
            'status' => AppointmentStatus::COMPLETED,
        ]);

        $token = BookingToken::factory()->create([
            'appointment_id' => $appointment->id,
            'type' => BookingTokenType::RESCHEDULE,
            'token' => 'completed-token',
            'expires_at' => Carbon::now()->addDays(7),
            'used_at' => null,
        ]);

        // Act
        $component = Livewire::test(RescheduleBooking::class, ['token' => 'completed-token']);

        // Assert
        expect($component->get('isValid'))->toBeFalse();
        expect($component->get('error'))->not->toBeNull();
    });
});
