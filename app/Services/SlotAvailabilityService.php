<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\AppointmentStatus;
use App\Models\Appointment;
use App\Models\Schedule;
use App\Models\ScheduleException;
use App\Models\Service;
use App\Models\Setting;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class SlotAvailabilityService
{
    public function getAvailableDates(Service $service, Carbon $from, Carbon $to): Collection
    {
        $dates = collect();
        $period = CarbonPeriod::create($from->copy()->startOfDay(), $to->copy()->startOfDay());

        foreach ($period as $date) {
            $slots = $this->getAvailableSlots($service, $date);
            if ($slots->isNotEmpty()) {
                $dates->push($date->copy()->startOfDay());
            }
        }

        return $dates;
    }

    public function getAvailableSlots(Service $service, Carbon $date): Collection
    {
        $workingHours = $this->getWorkingHours($date);

        if ($workingHours === null) {
            return collect();
        }

        $slotDuration = $service->duration_minutes + $this->getEffectiveBuffer($service);
        $slots = collect();

        $current = $date->copy()->setTimeFrom($workingHours['start']);
        $end = $date->copy()->setTimeFrom($workingHours['end']);

        while ($current->copy()->addMinutes($service->duration_minutes)->lessThanOrEqualTo($end)) {
            if ($this->isSlotAvailable($service, $current)) {
                $slots->push($current->copy());
            }
            $current->addMinutes($slotDuration);
        }

        return $slots;
    }

    public function isSlotAvailable(Service $service, Carbon $startTime): bool
    {
        $endTime = $startTime->copy()->addMinutes($service->duration_minutes);

        $potentialConflicts = Appointment::where('service_id', $service->id)
            ->whereIn('status', [AppointmentStatus::PENDING, AppointmentStatus::CONFIRMED])
            ->where(function ($query) use ($startTime, $endTime): void {
                $query->where(function ($q) use ($startTime, $endTime): void {
                    $q->where('start_time', '>=', $startTime)
                        ->where('start_time', '<', $endTime);
                })->orWhere(function ($q) use ($startTime): void {
                    $q->where('start_time', '<=', $startTime)
                        ->where('end_time', '>=', $startTime);
                });
            })
            ->get();

        foreach ($potentialConflicts as $appointment) {
            $appointmentEndWithBuffer = $appointment->end_time->copy()
                ->addMinutes($appointment->buffer_at_booking);

            if ($startTime->lessThan($appointmentEndWithBuffer) && $endTime->greaterThan($appointment->start_time)) {
                return false;
            }
        }

        return true;
    }

    public function reserveSlot(Service $service, Carbon $startTime, array $customerData): ?Appointment
    {
        return DB::transaction(function () use ($service, $startTime, $customerData): ?Appointment {
            DB::table('settings')
                ->where('key', 'slot_lock')
                ->lockForUpdate()
                ->first();

            if (! $this->isSlotAvailable($service, $startTime)) {
                return null;
            }

            $buffer = $this->getEffectiveBuffer($service);
            $endTime = $startTime->copy()->addMinutes($service->duration_minutes);

            return Appointment::create([
                'service_id' => $service->id,
                'user_name' => $customerData['name'] ?? '',
                'user_email' => $customerData['email'] ?? '',
                'user_phone' => $customerData['phone'] ?? null,
                'start_time' => $startTime,
                'end_time' => $endTime,
                'buffer_at_booking' => $buffer,
                'status' => AppointmentStatus::PENDING,
                'price_at_booking' => $service->price,
                'deposit_at_booking' => $service->deposit_fee,
                'notes' => $customerData['notes'] ?? null,
                'locale' => $customerData['locale'] ?? app()->getLocale(),
            ]);
        });
    }

    public function getEffectiveBuffer(Service $service): int
    {
        return (int) ($service->buffer_minutes ?? Setting::get('default_buffer_minutes', 0));
    }

    public function getWorkingHours(Carbon $date): ?array
    {
        $dateString = $date->format('Y-m-d');

        $exception = ScheduleException::forDate($dateString)->first();

        if ($exception) {
            if ($exception->is_closed) {
                return null;
            }

            return [
                'start' => $exception->custom_start_time,
                'end' => $exception->custom_end_time,
            ];
        }

        $dayOfWeek = (int) $date->format('w');
        $schedule = Schedule::forDay($dayOfWeek)->first();

        if (! $schedule || $schedule->is_off) {
            return null;
        }

        return [
            'start' => $schedule->start_time,
            'end' => $schedule->end_time,
        ];
    }
}
