<?php

use App\Enums\AppointmentStatus;
use App\Enums\BookingTokenType;
use App\Livewire\SubmitReview;
use App\Models\Appointment;
use App\Models\BookingToken;
use App\Models\Review;
use Carbon\Carbon;
use Livewire\Livewire;

describe('Review Submission', function () {
    it('allows review submission with valid token', function () {
        // Arrange
        $appointment = Appointment::factory()->create([
            'start_time' => Carbon::now()->subHours(24),
            'status' => AppointmentStatus::COMPLETED,
            'user_name' => 'John Doe',
            'user_email' => 'john@example.com',
        ]);

        $token = BookingToken::factory()->create([
            'appointment_id' => $appointment->id,
            'type' => BookingTokenType::REVIEW,
            'token' => 'valid-review-token',
            'expires_at' => Carbon::now()->addDays(7),
            'used_at' => null,
        ]);

        // Act
        $component = Livewire::test(SubmitReview::class, ['token' => 'valid-review-token']);

        // Assert
        expect($component->get('isValid'))->toBeTrue();

        // Submit review
        $component->call('setRating', 5)
            ->set('content', 'Excellent service! Highly recommended.')
            ->call('submitReview');

        // Assert
        expect($component->get('isSubmitted'))->toBeTrue();

        $review = Review::first();
        expect($review)->not->toBeNull();
        expect($review->appointment_id)->toBe($appointment->id);
        expect($review->customer_name)->toBe('John Doe');
        expect($review->customer_email)->toBe('john@example.com');
        expect($review->rating)->toBe(5);
        expect($review->content)->toBe('Excellent service! Highly recommended.');
        expect($review->is_approved)->toBeFalse();

        // Token should be marked as used
        expect($token->fresh()->used_at)->not->toBeNull();
    });

    it('rejects invalid token', function () {
        // Act
        $component = Livewire::test(SubmitReview::class, ['token' => 'invalid-token']);

        // Assert
        expect($component->get('isValid'))->toBeFalse();
        expect($component->get('error'))->not->toBeNull();
    });

    it('rejects already used token', function () {
        // Arrange
        $appointment = Appointment::factory()->create([
            'start_time' => Carbon::now()->subHours(24),
            'status' => AppointmentStatus::COMPLETED,
        ]);

        $token = BookingToken::factory()->create([
            'appointment_id' => $appointment->id,
            'type' => BookingTokenType::REVIEW,
            'token' => 'used-token',
            'expires_at' => Carbon::now()->addDays(7),
            'used_at' => Carbon::now()->subDay(),
        ]);

        // Act
        $component = Livewire::test(SubmitReview::class, ['token' => 'used-token']);

        // Assert
        expect($component->get('isValid'))->toBeFalse();
        expect($component->get('error'))->not->toBeNull();
    });

    it('rejects review for non-completed appointment', function () {
        // Arrange
        $appointment = Appointment::factory()->create([
            'start_time' => Carbon::now()->addHours(24),
            'status' => AppointmentStatus::CONFIRMED,
        ]);

        $token = BookingToken::factory()->create([
            'appointment_id' => $appointment->id,
            'type' => BookingTokenType::REVIEW,
            'token' => 'not-completed-token',
            'expires_at' => Carbon::now()->addDays(7),
            'used_at' => null,
        ]);

        // Act
        $component = Livewire::test(SubmitReview::class, ['token' => 'not-completed-token']);

        // Assert
        expect($component->get('isValid'))->toBeFalse();
        expect($component->get('error'))->not->toBeNull();
    });

    it('validates rating is required', function () {
        // Arrange
        $appointment = Appointment::factory()->create([
            'start_time' => Carbon::now()->subHours(24),
            'status' => AppointmentStatus::COMPLETED,
        ]);

        $token = BookingToken::factory()->create([
            'appointment_id' => $appointment->id,
            'type' => BookingTokenType::REVIEW,
            'token' => 'rating-token',
            'expires_at' => Carbon::now()->addDays(7),
            'used_at' => null,
        ]);

        // Act
        $component = Livewire::test(SubmitReview::class, ['token' => 'rating-token'])
            ->set('content', 'Good service!')
            ->call('submitReview');

        // Assert
        expect($component->get('isSubmitted'))->toBeFalse();
        $component->assertHasErrors(['rating']);
    });

    it('validates rating is between 1 and 5', function () {
        // Arrange
        $appointment = Appointment::factory()->create([
            'start_time' => Carbon::now()->subHours(24),
            'status' => AppointmentStatus::COMPLETED,
        ]);

        $token = BookingToken::factory()->create([
            'appointment_id' => $appointment->id,
            'type' => BookingTokenType::REVIEW,
            'token' => 'rating-range-token',
            'expires_at' => Carbon::now()->addDays(7),
            'used_at' => null,
        ]);

        // Act - Try rating 0
        $component = Livewire::test(SubmitReview::class, ['token' => 'rating-range-token'])
            ->call('setRating', 0)
            ->set('content', 'Good service!')
            ->call('submitReview');

        expect($component->get('rating'))->toBe(0);

        // Act - Try rating 6
        $component->call('setRating', 6);
        expect($component->get('rating'))->toBe(0); // Should not change

        // Valid rating should work
        $component->call('setRating', 3);
        expect($component->get('rating'))->toBe(3);
    });

    it('validates content is required', function () {
        // Arrange
        $appointment = Appointment::factory()->create([
            'start_time' => Carbon::now()->subHours(24),
            'status' => AppointmentStatus::COMPLETED,
        ]);

        $token = BookingToken::factory()->create([
            'appointment_id' => $appointment->id,
            'type' => BookingTokenType::REVIEW,
            'token' => 'content-token',
            'expires_at' => Carbon::now()->addDays(7),
            'used_at' => null,
        ]);

        // Act
        $component = Livewire::test(SubmitReview::class, ['token' => 'content-token'])
            ->call('setRating', 4)
            ->set('content', '')
            ->call('submitReview');

        // Assert
        expect($component->get('isSubmitted'))->toBeFalse();
        $component->assertHasErrors(['content']);
    });

    it('validates content minimum length', function () {
        // Arrange
        $appointment = Appointment::factory()->create([
            'start_time' => Carbon::now()->subHours(24),
            'status' => AppointmentStatus::COMPLETED,
        ]);

        $token = BookingToken::factory()->create([
            'appointment_id' => $appointment->id,
            'type' => BookingTokenType::REVIEW,
            'token' => 'min-length-token',
            'expires_at' => Carbon::now()->addDays(7),
            'used_at' => null,
        ]);

        // Act
        $component = Livewire::test(SubmitReview::class, ['token' => 'min-length-token'])
            ->call('setRating', 4)
            ->set('content', 'Short') // Less than 10 characters
            ->call('submitReview');

        // Assert
        expect($component->get('isSubmitted'))->toBeFalse();
        $component->assertHasErrors(['content']);
    });

    it('validates content maximum length', function () {
        // Arrange
        $appointment = Appointment::factory()->create([
            'start_time' => Carbon::now()->subHours(24),
            'status' => AppointmentStatus::COMPLETED,
        ]);

        $token = BookingToken::factory()->create([
            'appointment_id' => $appointment->id,
            'type' => BookingTokenType::REVIEW,
            'token' => 'max-length-token',
            'expires_at' => Carbon::now()->addDays(7),
            'used_at' => null,
        ]);

        // Act
        $component = Livewire::test(SubmitReview::class, ['token' => 'max-length-token'])
            ->call('setRating', 4)
            ->set('content', str_repeat('a', 1001)) // More than 1000 characters
            ->call('submitReview');

        // Assert
        expect($component->get('isSubmitted'))->toBeFalse();
        $component->assertHasErrors(['content']);
    });

    it('creates review with is_approved set to false', function () {
        // Arrange
        $appointment = Appointment::factory()->create([
            'start_time' => Carbon::now()->subHours(24),
            'status' => AppointmentStatus::COMPLETED,
        ]);

        $token = BookingToken::factory()->create([
            'appointment_id' => $appointment->id,
            'type' => BookingTokenType::REVIEW,
            'token' => 'approval-token',
            'expires_at' => Carbon::now()->addDays(7),
            'used_at' => null,
        ]);

        // Act
        $component = Livewire::test(SubmitReview::class, ['token' => 'approval-token'])
            ->call('setRating', 5)
            ->set('content', 'Great service!')
            ->call('submitReview');

        // Assert
        $review = Review::first();
        expect($review->is_approved)->toBeFalse();
    });

    it('rejects expired token', function () {
        // Arrange
        $appointment = Appointment::factory()->create([
            'start_time' => Carbon::now()->subHours(24),
            'status' => AppointmentStatus::COMPLETED,
        ]);

        $token = BookingToken::factory()->create([
            'appointment_id' => $appointment->id,
            'type' => BookingTokenType::REVIEW,
            'token' => 'expired-token',
            'expires_at' => Carbon::now()->subDay(),
            'used_at' => null,
        ]);

        // Act
        $component = Livewire::test(SubmitReview::class, ['token' => 'expired-token']);

        // Assert
        expect($component->get('isValid'))->toBeFalse();
        expect($component->get('error'))->not->toBeNull();
    });
});
