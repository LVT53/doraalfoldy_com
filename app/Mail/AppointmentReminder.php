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

class AppointmentReminder extends Mailable implements ShouldQueue
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
            subject: __('emails.appointment_reminder_subject', [
                'date' => $this->appointment->start_time->format('Y.m.d.'),
                'time' => $this->appointment->start_time->format('H:i'),
            ]),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.appointment-reminder',
            with: [
                'appointment' => $this->appointment,
                'cancelUrl' => $this->cancelUrl,
                'rescheduleUrl' => $this->rescheduleUrl,
                'hoursUntil' => now()->diffInHours($this->appointment->start_time, false),
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
