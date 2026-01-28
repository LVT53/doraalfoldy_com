<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Enums\AppointmentStatus;
use App\Enums\BookingTokenType;
use App\Models\Appointment;
use App\Models\BookingToken;
use App\Models\Review;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.livewire')]
class SubmitReview extends Component
{
    public ?string $token = null;

    public ?BookingToken $bookingToken = null;

    public ?Appointment $appointment = null;

    public ?string $error = null;

    public bool $isValid = false;

    public bool $isSubmitted = false;

    public int $rating = 0;

    public string $content = '';

    public function mount(string $token): void
    {
        $this->token = $token;
        $this->validateToken();
    }

    private function validateToken(): void
    {
        $this->bookingToken = BookingToken::where('token', $this->token)
            ->where('type', BookingTokenType::REVIEW)
            ->first();

        if (! $this->bookingToken instanceof BookingToken) {
            $this->error = __('booking.invalid_token');

            return;
        }

        if ($this->bookingToken->used_at !== null) {
            $this->error = __('booking.review_already_submitted');

            return;
        }

        $this->appointment = $this->bookingToken->appointment;

        if (! $this->appointment instanceof Appointment) {
            $this->error = __('booking.appointment_not_found');

            return;
        }

        if ($this->appointment->status !== AppointmentStatus::COMPLETED) {
            $this->error = __('booking.appointment_not_completed');

            return;
        }

        $this->isValid = true;
    }

    public function setRating(int $rating): void
    {
        if ($rating >= 1 && $rating <= 5) {
            $this->rating = $rating;
        }
    }

    public function submitReview(): void
    {
        if (! $this->isValid) {
            return;
        }

        if (! $this->appointment instanceof Appointment) {
            return;
        }

        $this->validate([
            'rating' => 'required|integer|min:1|max:5',
            'content' => 'required|string|min:10|max:1000',
        ], [
            'rating.required' => __('booking.rating_required'),
            'rating.min' => __('booking.rating_min'),
            'rating.max' => __('booking.rating_max'),
            'content.required' => __('booking.review_content_required'),
            'content.min' => __('booking.review_content_min'),
            'content.max' => __('booking.review_content_max'),
        ]);

        Review::create([
            'appointment_id' => $this->appointment->id,
            'customer_name' => $this->appointment->user_name,
            'customer_email' => $this->appointment->user_email,
            'rating' => $this->rating,
            'content' => $this->content,
            'is_approved' => false,
        ]);

        $this->bookingToken?->update([
            'used_at' => now(),
        ]);

        $this->isSubmitted = true;
    }

    public function render()
    {
        return view('livewire.submit-review');
    }
}
