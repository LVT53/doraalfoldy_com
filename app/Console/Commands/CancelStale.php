<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Enums\AppointmentStatus;
use App\Models\Appointment;
use Illuminate\Console\Command;

class CancelStale extends Command
{
    protected $signature = 'booking:cancel-stale';

    protected $description = 'Cancel PENDING appointments older than 30 minutes';

    public function handle(): int
    {
        $cutoffTime = now()->subMinutes(30);

        $appointments = Appointment::query()
            ->where('status', AppointmentStatus::PENDING)
            ->where('created_at', '<', $cutoffTime)
            ->get();

        $count = 0;

        foreach ($appointments as $appointment) {
            $appointment->update(['status' => AppointmentStatus::CANCELLED]);
            $count++;
        }

        $this->info("Cancelled {$count} stale appointments.");

        return self::SUCCESS;
    }
}
