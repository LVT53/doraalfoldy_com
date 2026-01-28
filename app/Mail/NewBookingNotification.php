<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewBookingNotification extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public Appointment $appointment;

    public string $adminUrl;

    public function __construct(Appointment $appointment)
    {
        $this->appointment = $appointment;
        $this->adminUrl = route('filament.admin.resources.appointments.edit', ['record' => $appointment->id]);
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('emails.new_booking_notification_subject', [
                'service' => $this->appointment->service?->name ?? 'Szolgáltatás',
                'date' => $this->appointment->start_time->format('Y.m.d.'),
            ]),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.new-booking-notification',
            with: [
                'appointment' => $this->appointment,
                'adminUrl' => $this->adminUrl,
            ],
        );
    }
}
