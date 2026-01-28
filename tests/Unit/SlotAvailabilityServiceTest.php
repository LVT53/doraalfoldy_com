<?php

use App\Enums\AppointmentStatus;
use App\Models\Appointment;
use App\Models\Schedule;
use App\Models\ScheduleException;
use App\Models\Service;
use App\Models\Setting;
use App\Services\SlotAvailabilityService;
use Carbon\Carbon;

describe('getAvailableSlots', function () {
    it('respects working hours', function () {
        $serviceModel = Service::factory()->create([
            'duration_minutes' => 60,
            'buffer_minutes' => 0,
        ]);

        Schedule::factory()->create([
            'day_of_week' => 1,
            'start_time' => '09:00',
            'end_time' => '17:00',
            'is_off' => false,
        ]);

        $service = new SlotAvailabilityService;
        $date = Carbon::parse('2026-02-02');
        $slots = $service->getAvailableSlots($serviceModel, $date);

        expect($slots)->toHaveCount(8);
        expect($slots->first()->format('H:i'))->toBe('09:00');
        expect($slots->last()->format('H:i'))->toBe('16:00');
    });

    it('excludes existing appointments', function () {
        $serviceModel = Service::factory()->create([
            'duration_minutes' => 60,
            'buffer_minutes' => 0,
        ]);

        Schedule::factory()->create([
            'day_of_week' => 1,
            'start_time' => '09:00',
            'end_time' => '17:00',
            'is_off' => false,
        ]);

        Appointment::factory()->create([
            'service_id' => $serviceModel->id,
            'start_time' => Carbon::parse('2026-02-02 10:00'),
            'end_time' => Carbon::parse('2026-02-02 11:00'),
            'buffer_at_booking' => 0,
            'status' => AppointmentStatus::CONFIRMED,
        ]);

        $service = new SlotAvailabilityService;
        $date = Carbon::parse('2026-02-02');
        $slots = $service->getAvailableSlots($serviceModel, $date);

        expect($slots)->toHaveCount(7);
        expect($slots->map(fn ($slot) => $slot->format('H:i'))->toArray())
            ->not->toContain('10:00');
    });

    it('respects buffer time', function () {
        $serviceModel = Service::factory()->create([
            'duration_minutes' => 60,
            'buffer_minutes' => 15,
        ]);

        Schedule::factory()->create([
            'day_of_week' => 1,
            'start_time' => '09:00',
            'end_time' => '17:00',
            'is_off' => false,
        ]);

        $service = new SlotAvailabilityService;
        $date = Carbon::parse('2026-02-02');
        $slots = $service->getAvailableSlots($serviceModel, $date);

        expect($slots)->toHaveCount(6);
        expect($slots->first()->format('H:i'))->toBe('09:00');
        expect($slots->get(1)->format('H:i'))->toBe('10:15');
    });

    it('returns empty when day is off', function () {
        $serviceModel = Service::factory()->create([
            'duration_minutes' => 60,
            'buffer_minutes' => 0,
        ]);

        Schedule::factory()->create([
            'day_of_week' => 1,
            'start_time' => '09:00',
            'end_time' => '17:00',
            'is_off' => true,
        ]);

        $service = new SlotAvailabilityService;
        $date = Carbon::parse('2026-02-02');
        $slots = $service->getAvailableSlots($serviceModel, $date);

        expect($slots)->toBeEmpty();
    });

    it('uses schedule exception when present', function () {
        $serviceModel = Service::factory()->create([
            'duration_minutes' => 60,
            'buffer_minutes' => 0,
        ]);

        Schedule::factory()->create([
            'day_of_week' => 1,
            'start_time' => '09:00',
            'end_time' => '17:00',
            'is_off' => false,
        ]);

        ScheduleException::factory()->create([
            'date' => '2026-02-02',
            'is_closed' => false,
            'custom_start_time' => '10:00',
            'custom_end_time' => '14:00',
        ]);

        $service = new SlotAvailabilityService;
        $date = Carbon::parse('2026-02-02');

        // Debug: Check if exception exists
        $exception = \App\Models\ScheduleException::forDate('2026-02-02')->first();
        expect($exception)->not->toBeNull('Schedule exception should exist');
        expect($exception->is_closed)->toBeFalse('Exception should not be closed');

        $slots = $service->getAvailableSlots($serviceModel, $date);

        expect($slots)->toHaveCount(4);
        expect($slots->first()->format('H:i'))->toBe('10:00');
        expect($slots->last()->format('H:i'))->toBe('13:00');
    });

    it('returns empty when schedule exception marks day closed', function () {
        $serviceModel = Service::factory()->create([
            'duration_minutes' => 60,
            'buffer_minutes' => 0,
        ]);

        Schedule::factory()->create([
            'day_of_week' => 1,
            'start_time' => '09:00',
            'end_time' => '17:00',
            'is_off' => false,
        ]);

        ScheduleException::factory()->create([
            'date' => '2026-02-02',
            'is_closed' => true,
        ]);

        $service = new SlotAvailabilityService;
        $date = Carbon::parse('2026-02-02');
        $slots = $service->getAvailableSlots($serviceModel, $date);

        expect($slots)->toBeEmpty();
    });
});

describe('reserveSlot', function () {
    it('creates pending appointment when slot is available', function () {
        Setting::set('slot_lock', '1');

        $serviceModel = Service::factory()->create([
            'duration_minutes' => 60,
            'buffer_minutes' => 0,
            'price' => 100.00,
            'deposit_fee' => 20.00,
        ]);

        Schedule::factory()->create([
            'day_of_week' => 1,
            'start_time' => '09:00',
            'end_time' => '17:00',
            'is_off' => false,
        ]);

        $service = new SlotAvailabilityService;
        $startTime = Carbon::parse('2026-02-02 10:00');

        $appointment = $service->reserveSlot($serviceModel, $startTime, [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'phone' => '+1234567890',
            'notes' => 'Test notes',
        ]);

        expect($appointment)->not->toBeNull();
        expect($appointment->status)->toBe(AppointmentStatus::PENDING);
        expect($appointment->user_name)->toBe('John Doe');
        expect($appointment->user_email)->toBe('john@example.com');
        expect($appointment->start_time)->toEqual($startTime);
        expect($appointment->end_time)->toEqual($startTime->copy()->addMinutes(60));
        expect((float) $appointment->price_at_booking)->toBe(100.00);
        expect((float) $appointment->deposit_at_booking)->toBe(20.00);
    });

    it('returns null when slot is already taken', function () {
        Setting::set('slot_lock', '1');

        $serviceModel = Service::factory()->create([
            'duration_minutes' => 60,
            'buffer_minutes' => 0,
        ]);

        Schedule::factory()->create([
            'day_of_week' => 1,
            'start_time' => '09:00',
            'end_time' => '17:00',
            'is_off' => false,
        ]);

        Appointment::factory()->create([
            'service_id' => $serviceModel->id,
            'start_time' => Carbon::parse('2026-02-02 10:00'),
            'end_time' => Carbon::parse('2026-02-02 11:00'),
            'buffer_at_booking' => 0,
            'status' => AppointmentStatus::CONFIRMED,
        ]);

        $service = new SlotAvailabilityService;
        $startTime = Carbon::parse('2026-02-02 10:00');

        $appointment = $service->reserveSlot($serviceModel, $startTime, [
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
        ]);

        expect($appointment)->toBeNull();
    });

    it('prevents double booking with sequential requests', function () {
        Setting::set('slot_lock', '1');

        $serviceModel = Service::factory()->create([
            'duration_minutes' => 60,
            'buffer_minutes' => 0,
        ]);

        Schedule::factory()->create([
            'day_of_week' => 1,
            'start_time' => '09:00',
            'end_time' => '17:00',
            'is_off' => false,
        ]);

        $service = new SlotAvailabilityService;
        $startTime = Carbon::parse('2026-02-02 10:00');

        $appointment1 = $service->reserveSlot($serviceModel, $startTime, [
            'name' => 'First Customer',
            'email' => 'first@example.com',
            'phone' => '+1234567890',
        ]);

        $appointment2 = $service->reserveSlot($serviceModel, $startTime, [
            'name' => 'Second Customer',
            'email' => 'second@example.com',
            'phone' => '+0987654321',
        ]);

        expect($appointment1)->not->toBeNull();
        expect($appointment2)->toBeNull();
        expect(Appointment::count())->toBe(1);
    });
});

describe('isSlotAvailable', function () {
    it('returns true when slot is free', function () {
        $serviceModel = Service::factory()->create([
            'duration_minutes' => 60,
            'buffer_minutes' => 0,
        ]);

        $service = new SlotAvailabilityService;
        $startTime = Carbon::parse('2026-02-02 10:00');

        expect($service->isSlotAvailable($serviceModel, $startTime))->toBeTrue();
    });

    it('returns false when slot conflicts with existing appointment', function () {
        $serviceModel = Service::factory()->create([
            'duration_minutes' => 60,
            'buffer_minutes' => 0,
        ]);

        Appointment::factory()->create([
            'service_id' => $serviceModel->id,
            'start_time' => Carbon::parse('2026-02-02 10:00'),
            'end_time' => Carbon::parse('2026-02-02 11:00'),
            'buffer_at_booking' => 0,
            'status' => AppointmentStatus::CONFIRMED,
        ]);

        $service = new SlotAvailabilityService;
        $startTime = Carbon::parse('2026-02-02 10:00');

        expect($service->isSlotAvailable($serviceModel, $startTime))->toBeFalse();
    });

    it('respects buffer from existing appointments', function () {
        $serviceModel = Service::factory()->create([
            'duration_minutes' => 60,
            'buffer_minutes' => 0,
        ]);

        Appointment::factory()->create([
            'service_id' => $serviceModel->id,
            'start_time' => Carbon::parse('2026-02-02 09:00'),
            'end_time' => Carbon::parse('2026-02-02 10:00'),
            'buffer_at_booking' => 15,
            'status' => AppointmentStatus::CONFIRMED,
        ]);

        $service = new SlotAvailabilityService;

        expect($service->isSlotAvailable($serviceModel, Carbon::parse('2026-02-02 10:00')))->toBeFalse();
        expect($service->isSlotAvailable($serviceModel, Carbon::parse('2026-02-02 10:15')))->toBeTrue();
    });

    it('ignores cancelled appointments', function () {
        $serviceModel = Service::factory()->create([
            'duration_minutes' => 60,
            'buffer_minutes' => 0,
        ]);

        Appointment::factory()->create([
            'service_id' => $serviceModel->id,
            'start_time' => Carbon::parse('2026-02-02 10:00'),
            'end_time' => Carbon::parse('2026-02-02 11:00'),
            'buffer_at_booking' => 0,
            'status' => AppointmentStatus::CANCELLED,
        ]);

        $service = new SlotAvailabilityService;
        $startTime = Carbon::parse('2026-02-02 10:00');

        expect($service->isSlotAvailable($serviceModel, $startTime))->toBeTrue();
    });
});

describe('getAvailableDates', function () {
    it('returns dates with available slots', function () {
        $serviceModel = Service::factory()->create([
            'duration_minutes' => 60,
            'buffer_minutes' => 0,
        ]);

        Schedule::factory()->create([
            'day_of_week' => 1,
            'start_time' => '09:00',
            'end_time' => '17:00',
            'is_off' => false,
        ]);

        Schedule::factory()->create([
            'day_of_week' => 2,
            'start_time' => '09:00',
            'end_time' => '17:00',
            'is_off' => false,
        ]);

        Schedule::factory()->create([
            'day_of_week' => 3,
            'start_time' => '09:00',
            'end_time' => '17:00',
            'is_off' => false,
        ]);

        ScheduleException::factory()->create([
            'date' => '2026-02-03',
            'is_closed' => true,
        ]);

        $service = new SlotAvailabilityService;
        $from = Carbon::parse('2026-02-02');
        $to = Carbon::parse('2026-02-04');

        $dates = $service->getAvailableDates($serviceModel, $from, $to);

        expect($dates)->toHaveCount(2);
        expect($dates->map->format('Y-m-d')->toArray())
            ->toEqual(['2026-02-02', '2026-02-04']);
    });
});

describe('getEffectiveBuffer', function () {
    it('returns service buffer when set', function () {
        $serviceModel = Service::factory()->create([
            'buffer_minutes' => 30,
        ]);

        $service = new SlotAvailabilityService;

        expect($service->getEffectiveBuffer($serviceModel))->toBe(30);
    });

    it('returns default buffer when service buffer is null', function () {
        $serviceModel = Service::factory()->create([
            'buffer_minutes' => null,
        ]);

        Setting::set('default_buffer_minutes', 15);

        $service = new SlotAvailabilityService;

        expect($service->getEffectiveBuffer($serviceModel))->toBe(15);
    });
});
