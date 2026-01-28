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

class BookingConfirmation extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public Appointment $appointment;

    public ?string $cancelUrl;

    public ?string $rescheduleUrl;

    public function __construct(Appointment $appointment)
    {
        $this->appointment = $appointment;
        $this->cancelUrl = $this->generateMagicLink('cancel');
        $this->rescheduleUrl = $this->generateMagicLink('reschedule');
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('emails.booking_confirmation_subject', [
                'service' => $this->appointment->service?->name ?? 'Szolgáltatás',
            ]),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.booking-confirmation',
            with: [
                'appointment' => $this->appointment,
                'cancelUrl' => $this->cancelUrl,
                'rescheduleUrl' => $this->rescheduleUrl,
            ],
        );
    }

    private function generateMagicLink(string $type): ?string
    {
        $token = $this->appointment->tokens()
            ->where('type', $type)
            ->valid()
            ->first();

        if (! $token instanceof BookingToken) {
            return null;
        }

        return route('booking.magic.'.$type, ['token' => $token->token]);
    }
}
