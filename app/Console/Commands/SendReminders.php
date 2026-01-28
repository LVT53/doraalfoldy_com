<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Enums\AppointmentStatus;
use App\Mail\AppointmentReminder;
use App\Models\Appointment;
use App\Models\Setting;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendReminders extends Command
{
    protected $signature = 'booking:send-reminders';

    protected $description = 'Send reminder emails for appointments due in X hours';

    public function handle(): int
    {
        $hoursBefore = (int) Setting::get('reminder_hours_before', 24);
        $reminderTime = now()->addHours($hoursBefore);

        $appointments = Appointment::query()
            ->where('status', AppointmentStatus::CONFIRMED)
            ->whereNull('reminder_sent_at')
            ->where('start_time', '<=', $reminderTime)
            ->where('start_time', '>', now())
            ->get();

        $count = 0;

        foreach ($appointments as $appointment) {
            Mail::to($appointment->user_email)
                ->send(new AppointmentReminder($appointment));

            $appointment->update(['reminder_sent_at' => now()]);
            $count++;
        }

        $this->info("Sent {$count} reminder emails.");

        return self::SUCCESS;
    }
}
