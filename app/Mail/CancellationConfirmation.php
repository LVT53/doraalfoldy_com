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

class CancellationConfirmation extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public Appointment $appointment;

    public string $rebookUrl;

    public function __construct(Appointment $appointment)
    {
        $this->appointment = $appointment;
        $this->rebookUrl = route('booking');
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('emails.cancellation_confirmation_subject'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.cancellation-confirmation',
            with: [
                'appointment' => $this->appointment,
                'rebookUrl' => $this->rebookUrl,
                'cancelledAt' => now(),
            ],
        );
    }
}
