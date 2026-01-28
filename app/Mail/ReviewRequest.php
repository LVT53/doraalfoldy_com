<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\Appointment;
use App\Models\BookingToken;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReviewRequest extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public Appointment $appointment;

    public ?string $reviewUrl;

    public function __construct(Appointment $appointment)
    {
        $this->appointment = $appointment;
        $this->reviewUrl = $this->generateReviewLink();
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('emails.review_request_subject', [
                'service' => $this->appointment->service?->name ?? 'Szolgáltatás',
            ]),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.review-request',
            with: [
                'appointment' => $this->appointment,
                'reviewUrl' => $this->reviewUrl,
            ],
        );
    }

    private function generateReviewLink(): ?string
    {
        $token = $this->appointment->tokens()
            ->where('type', 'review')
            ->valid()
            ->first();

        if (! $token instanceof BookingToken) {
            return null;
        }

        return route('booking.magic.review', ['token' => $token->token]);
    }
}
