<?php

use App\Enums\AppointmentStatus;
use App\Enums\BookingTokenType;
use App\Livewire\CancelBooking;
use App\Models\Appointment;
use App\Models\BookingToken;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;

describe('Cancel Booking', function () {
    beforeEach(function () {
        Setting::set('cancellation_window_hours', 24);
        Mail::fake();
    });

    it('allows cancellation within window', function () {
        // Arrange
        $appointment = Appointment::factory()->create([
            'start_time' => Carbon::now()->addHours(48),
            'status' => AppointmentStatus::CONFIRMED,
        ]);

        $token = BookingToken::factory()->create([
            'appointment_id' => $appointment->id,
            'type' => BookingTokenType::CANCEL,
            'token' => 'valid-cancel-token',
            'expires_at' => Carbon::now()->addDays(7),
            'used_at' => null,
        ]);

        // Act
        $component = Livewire::test(CancelBooking::class, ['token' => 'valid-cancel-token']);

        // Assert
        expect($component->get('isValid'))->toBeTrue();
        expect($component->get('canCancel'))->toBeTrue();

        // Act - Confirm cancellation
        $component->call('confirmCancel');

        // Assert
        expect($component->get('isCancelled'))->toBeTrue();
        expect($appointment->fresh()->status)->toBe(AppointmentStatus::CANCELLED);
        expect($token->fresh()->used_at)->not->toBeNull();

        // Email was sent
        Mail::assertSent(\App\Mail\CancellationConfirmation::class);
    });

    it('prevents cancellation outside window', function () {
        // Arrange - Appointment is within 24 hours
        $appointment = Appointment::factory()->create([
            'start_time' => Carbon::now()->addHours(12),
            'status' => AppointmentStatus::CONFIRMED,
        ]);

        $token = BookingToken::factory()->create([
            'appointment_id' => $appointment->id,
            'type' => BookingTokenType::CANCEL,
            'token' => 'valid-cancel-token',
            'expires_at' => Carbon::now()->addDays(7),
            'used_at' => null,
        ]);

        // Act
        $component = Livewire::test(CancelBooking::class, ['token' => 'valid-cancel-token']);

        // Assert - Token is valid but cancellation is not allowed
        expect($component->get('isValid'))->toBeTrue();
        expect($component->get('canCancel'))->toBeFalse();

        // Act - Try to cancel anyway
        $component->call('confirmCancel');

        // Assert - Appointment not cancelled
        expect($component->get('isCancelled'))->toBeFalse();
        expect($appointment->fresh()->status)->toBe(AppointmentStatus::CONFIRMED);
    });

    it('rejects invalid token', function () {
        // Act
        $component = Livewire::test(CancelBooking::class, ['token' => 'invalid-token']);

        // Assert
        expect($component->get('isValid'))->toBeFalse();
        expect($component->get('error'))->not->toBeNull();
    });

    it('rejects expired token', function () {
        // Arrange
        $appointment = Appointment::factory()->create([
            'start_time' => Carbon::now()->addHours(48),
            'status' => AppointmentStatus::CONFIRMED,
        ]);

        $token = BookingToken::factory()->create([
            'appointment_id' => $appointment->id,
            'type' => BookingTokenType::CANCEL,
            'token' => 'expired-token',
            'expires_at' => Carbon::now()->subDay(),
            'used_at' => null,
        ]);

        // Act
        $component = Livewire::test(CancelBooking::class, ['token' => 'expired-token']);

        // Assert
        expect($component->get('isValid'))->toBeFalse();
        expect($component->get('error'))->not->toBeNull();
    });

    it('rejects already used token', function () {
        // Arrange
        $appointment = Appointment::factory()->create([
            'start_time' => Carbon::now()->addHours(48),
            'status' => AppointmentStatus::CONFIRMED,
        ]);

        $token = BookingToken::factory()->create([
            'appointment_id' => $appointment->id,
            'type' => BookingTokenType::CANCEL,
            'token' => 'used-token',
            'expires_at' => Carbon::now()->addDays(7),
            'used_at' => Carbon::now()->subDay(),
        ]);

        // Act
        $component = Livewire::test(CancelBooking::class, ['token' => 'used-token']);

        // Assert
        expect($component->get('isValid'))->toBeFalse();
        expect($component->get('error'))->not->toBeNull();
    });

    it('rejects cancellation of already cancelled appointment', function () {
        // Arrange
        $appointment = Appointment::factory()->create([
            'start_time' => Carbon::now()->addHours(48),
            'status' => AppointmentStatus::CANCELLED,
        ]);

        $token = BookingToken::factory()->create([
            'appointment_id' => $appointment->id,
            'type' => BookingTokenType::CANCEL,
            'token' => 'cancel-token',
            'expires_at' => Carbon::now()->addDays(7),
            'used_at' => null,
        ]);

        // Act
        $component = Livewire::test(CancelBooking::class, ['token' => 'cancel-token']);

        // Assert
        expect($component->get('isValid'))->toBeFalse();
        expect($component->get('error'))->not->toBeNull();
    });

    it('rejects cancellation of completed appointment', function () {
        // Arrange
        $appointment = Appointment::factory()->create([
            'start_time' => Carbon::now()->subHours(24),
            'status' => AppointmentStatus::COMPLETED,
        ]);

        $token = BookingToken::factory()->create([
            'appointment_id' => $appointment->id,
            'type' => BookingTokenType::CANCEL,
            'token' => 'complete-token',
            'expires_at' => Carbon::now()->addDays(7),
            'used_at' => null,
        ]);

        // Act
        $component = Livewire::test(CancelBooking::class, ['token' => 'complete-token']);

        // Assert
        expect($component->get('isValid'))->toBeFalse();
        expect($component->get('error'))->not->toBeNull();
    });

    it('respects custom cancellation window', function () {
        // Arrange - Set 48 hour window
        Setting::set('cancellation_window_hours', 48);

        // Appointment is 36 hours away - within 48h window
        $appointment = Appointment::factory()->create([
            'start_time' => Carbon::now()->addHours(36),
            'status' => AppointmentStatus::CONFIRMED,
        ]);

        $token = BookingToken::factory()->create([
            'appointment_id' => $appointment->id,
            'type' => BookingTokenType::CANCEL,
            'token' => 'window-token',
            'expires_at' => Carbon::now()->addDays(7),
            'used_at' => null,
        ]);

        // Act
        $component = Livewire::test(CancelBooking::class, ['token' => 'window-token']);

        // Assert - Cannot cancel within 48h window
        expect($component->get('isValid'))->toBeTrue();
        expect($component->get('canCancel'))->toBeFalse();
    });

    it('allows cancellation with 0 hour window', function () {
        // Arrange - Set 0 hour window (can cancel anytime)
        Setting::set('cancellation_window_hours', 0);

        // Appointment is 1 hour away
        $appointment = Appointment::factory()->create([
            'start_time' => Carbon::now()->addHour(),
            'status' => AppointmentStatus::CONFIRMED,
        ]);

        $token = BookingToken::factory()->create([
            'appointment_id' => $appointment->id,
            'type' => BookingTokenType::CANCEL,
            'token' => 'zero-window-token',
            'expires_at' => Carbon::now()->addDays(7),
            'used_at' => null,
        ]);

        // Act
        $component = Livewire::test(CancelBooking::class, ['token' => 'zero-window-token']);

        // Assert - Can cancel with 0 hour window
        expect($component->get('isValid'))->toBeTrue();
        expect($component->get('canCancel'))->toBeTrue();
    });
});
