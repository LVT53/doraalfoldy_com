<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Enums\AppointmentStatus;
use App\Enums\BookingTokenType;
use App\Mail\ReviewRequest;
use App\Models\Appointment;
use App\Models\BookingToken;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class SendReviewRequests extends Command
{
    protected $signature = 'booking:send-review-requests';

    protected $description = 'Send review request emails for completed appointments';

    public function handle(): int
    {
        $appointments = Appointment::query()
            ->where('status', AppointmentStatus::COMPLETED)
            ->where('end_time', '<', now())
            ->where('end_time', '>', now()->subDays(7))
            ->whereDoesntHave('tokens', function ($query): void {
                $query->where('type', BookingTokenType::REVIEW);
            })
            ->get();

        $count = 0;

        foreach ($appointments as $appointment) {
            BookingToken::create([
                'appointment_id' => $appointment->id,
                'token' => Str::random(64),
                'type' => BookingTokenType::REVIEW,
                'expires_at' => now()->addDays(30),
            ]);

            Mail::to($appointment->user_email)
                ->send(new ReviewRequest($appointment));

            $count++;
        }

        $this->info("Sent {$count} review request emails.");

        return self::SUCCESS;
    }
}
