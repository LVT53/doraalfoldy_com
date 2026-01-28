<?php

use App\Enums\AppointmentStatus;
use App\Models\Appointment;
use App\Models\Schedule;
use App\Models\ScheduleException;
use App\Models\Service;
use App\Models\Setting;
use App\Services\SlotAvailabilityService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

describe('Slot Availability', function () {
    beforeEach(function () {
        Setting::set('slot_lock', '1');
        Setting::set('default_buffer_minutes', 0);
    });

    it('getAvailableSlots respects working hours', function () {
        // Arrange
        $service = Service::factory()->create([
            'duration_minutes' => 60,
            'buffer_minutes' => 0,
        ]);

        Schedule::factory()->create([
            'day_of_week' => 1,
            'start_time' => '09:00',
            'end_time' => '17:00',
            'is_off' => false,
        ]);

        // Act
        $slotService = new SlotAvailabilityService;
        $date = Carbon::parse('2026-02-02');
        $slots = $slotService->getAvailableSlots($service, $date);

        // Assert
        expect($slots)->toHaveCount(8);
        expect($slots->first()->format('H:i'))->toBe('09:00');
        expect($slots->last()->format('H:i'))->toBe('16:00');
    });

    it('getAvailableSlots excludes existing appointments', function () {
        // Arrange
        $service = Service::factory()->create([
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
            'service_id' => $service->id,
            'start_time' => Carbon::parse('2026-02-02 10:00'),
            'end_time' => Carbon::parse('2026-02-02 11:00'),
            'buffer_at_booking' => 0,
            'status' => AppointmentStatus::CONFIRMED,
        ]);

        // Act
        $slotService = new SlotAvailabilityService;
        $date = Carbon::parse('2026-02-02');
        $slots = $slotService->getAvailableSlots($service, $date);

        // Assert
        expect($slots)->toHaveCount(7);
        expect($slots->map(fn ($slot) => $slot->format('H:i'))->toArray())
            ->not->toContain('10:00');
    });

    it('getAvailableSlots respects buffer time', function () {
        // Arrange
        $service = Service::factory()->create([
            'duration_minutes' => 60,
            'buffer_minutes' => 15,
        ]);

        Schedule::factory()->create([
            'day_of_week' => 1,
            'start_time' => '09:00',
            'end_time' => '17:00',
            'is_off' => false,
        ]);

        // Act
        $slotService = new SlotAvailabilityService;
        $date = Carbon::parse('2026-02-02');
        $slots = $slotService->getAvailableSlots($service, $date);

        // Assert - 75 min slots (60 + 15 buffer) in 8 hours = 6 slots
        expect($slots)->toHaveCount(6);
        expect($slots->first()->format('H:i'))->toBe('09:00');
        expect($slots->get(1)->format('H:i'))->toBe('10:15');
    });

    it('getAvailableSlots returns empty for closed days', function () {
        // Arrange
        $service = Service::factory()->create([
            'duration_minutes' => 60,
            'buffer_minutes' => 0,
        ]);

        Schedule::factory()->create([
            'day_of_week' => 1,
            'start_time' => '09:00',
            'end_time' => '17:00',
            'is_off' => true,
        ]);

        // Act
        $slotService = new SlotAvailabilityService;
        $date = Carbon::parse('2026-02-02');
        $slots = $slotService->getAvailableSlots($service, $date);

        // Assert
        expect($slots)->toBeEmpty();
    });

    it('getAvailableSlots uses schedule exceptions when present', function () {
        // Arrange
        $service = Service::factory()->create([
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

        // Act
        $slotService = new SlotAvailabilityService;
        $date = Carbon::parse('2026-02-02');
        $slots = $slotService->getAvailableSlots($service, $date);

        // Assert
        expect($slots)->toHaveCount(4);
        expect($slots->first()->format('H:i'))->toBe('10:00');
        expect($slots->last()->format('H:i'))->toBe('13:00');
    });

    it('getAvailableSlots returns empty when schedule exception marks day closed', function () {
        // Arrange
        $service = Service::factory()->create([
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

        // Act
        $slotService = new SlotAvailabilityService;
        $date = Carbon::parse('2026-02-02');
        $slots = $slotService->getAvailableSlots($service, $date);

        // Assert
        expect($slots)->toBeEmpty();
    });

    it('reserveSlot prevents double booking', function () {
        // Arrange
        Setting::set('slot_lock', '1');

        $service = Service::factory()->create([
            'duration_minutes' => 60,
            'buffer_minutes' => 0,
        ]);

        Schedule::factory()->create([
            'day_of_week' => 1,
            'start_time' => '09:00',
            'end_time' => '17:00',
            'is_off' => false,
        ]);

        $slotService = new SlotAvailabilityService;
        $startTime = Carbon::parse('2026-02-02 10:00');

        // Act - First reservation should succeed
        $appointment1 = $slotService->reserveSlot($service, $startTime, [
            'name' => 'First Customer',
            'email' => 'first@example.com',
            'phone' => '+1234567890',
        ]);

        // Second reservation should fail
        $appointment2 = $slotService->reserveSlot($service, $startTime, [
            'name' => 'Second Customer',
            'email' => 'second@example.com',
            'phone' => '+0987654321',
        ]);

        // Assert
        expect($appointment1)->not->toBeNull();
        expect($appointment2)->toBeNull();
        expect(Appointment::count())->toBe(1);
    });

    it('isSlotAvailable returns true when slot is free', function () {
        // Arrange
        $service = Service::factory()->create([
            'duration_minutes' => 60,
            'buffer_minutes' => 0,
        ]);

        // Act
        $slotService = new SlotAvailabilityService;
        $startTime = Carbon::parse('2026-02-02 10:00');

        // Assert
        expect($slotService->isSlotAvailable($service, $startTime))->toBeTrue();
    });

    it('isSlotAvailable returns false when slot conflicts with existing appointment', function () {
        // Arrange
        $service = Service::factory()->create([
            'duration_minutes' => 60,
            'buffer_minutes' => 0,
        ]);

        Appointment::factory()->create([
            'service_id' => $service->id,
            'start_time' => Carbon::parse('2026-02-02 10:00'),
            'end_time' => Carbon::parse('2026-02-02 11:00'),
            'buffer_at_booking' => 0,
            'status' => AppointmentStatus::CONFIRMED,
        ]);

        // Act
        $slotService = new SlotAvailabilityService;
        $startTime = Carbon::parse('2026-02-02 10:00');

        // Assert
        expect($slotService->isSlotAvailable($service, $startTime))->toBeFalse();
    });

    it('isSlotAvailable respects buffer from existing appointments', function () {
        // Arrange
        $service = Service::factory()->create([
            'duration_minutes' => 60,
            'buffer_minutes' => 0,
        ]);

        Appointment::factory()->create([
            'service_id' => $service->id,
            'start_time' => Carbon::parse('2026-02-02 09:00'),
            'end_time' => Carbon::parse('2026-02-02 10:00'),
            'buffer_at_booking' => 15,
            'status' => AppointmentStatus::CONFIRMED,
        ]);

        // Act
        $slotService = new SlotAvailabilityService;

        // Assert - 10:00 is blocked by 9:00 appointment with 15 min buffer
        expect($slotService->isSlotAvailable($service, Carbon::parse('2026-02-02 10:00')))->toBeFalse();
        expect($slotService->isSlotAvailable($service, Carbon::parse('2026-02-02 10:15')))->toBeTrue();
    });

    it('isSlotAvailable ignores cancelled appointments', function () {
        // Arrange
        $service = Service::factory()->create([
            'duration_minutes' => 60,
            'buffer_minutes' => 0,
        ]);

        Appointment::factory()->create([
            'service_id' => $service->id,
            'start_time' => Carbon::parse('2026-02-02 10:00'),
            'end_time' => Carbon::parse('2026-02-02 11:00'),
            'buffer_at_booking' => 0,
            'status' => AppointmentStatus::CANCELLED,
        ]);

        // Act
        $slotService = new SlotAvailabilityService;
        $startTime = Carbon::parse('2026-02-02 10:00');

        // Assert
        expect($slotService->isSlotAvailable($service, $startTime))->toBeTrue();
    });

    it('getAvailableDates returns dates with available slots', function () {
        // Arrange
        $service = Service::factory()->create([
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

        // Act
        $slotService = new SlotAvailabilityService;
        $from = Carbon::parse('2026-02-02');
        $to = Carbon::parse('2026-02-04');
        $dates = $slotService->getAvailableDates($service, $from, $to);

        // Assert
        expect($dates)->toHaveCount(2);
        expect($dates->map->format('Y-m-d')->toArray())
            ->toEqual(['2026-02-02', '2026-02-04']);
    });

    it('getEffectiveBuffer returns service buffer when set', function () {
        // Arrange
        $service = Service::factory()->create([
            'buffer_minutes' => 30,
        ]);

        // Act
        $slotService = new SlotAvailabilityService;

        // Assert
        expect($slotService->getEffectiveBuffer($service))->toBe(30);
    });

    it('getEffectiveBuffer returns default buffer when service buffer is null', function () {
        // Arrange
        $service = Service::factory()->create([
            'buffer_minutes' => null,
        ]);

        Setting::set('default_buffer_minutes', 15);

        // Act
        $slotService = new SlotAvailabilityService;

        // Assert
        expect($slotService->getEffectiveBuffer($service))->toBe(15);
    });

    it('does not have N+1 queries', function () {
        // Arrange
        $service = Service::factory()->create([
            'duration_minutes' => 60,
            'buffer_minutes' => 0,
        ]);

        Schedule::factory()->create([
            'day_of_week' => 1,
            'start_time' => '09:00',
            'end_time' => '17:00',
            'is_off' => false,
        ]);

        // Create multiple appointments
        for ($i = 0; $i < 5; $i++) {
            Appointment::factory()->create([
                'service_id' => $service->id,
                'start_time' => Carbon::parse('2026-02-02')->addHours($i + 1),
                'end_time' => Carbon::parse('2026-02-02')->addHours($i + 2),
                'buffer_at_booking' => 0,
                'status' => AppointmentStatus::CONFIRMED,
            ]);
        }

        // Act & Assert - Enable query log
        DB::enableQueryLog();

        $slotService = new SlotAvailabilityService;
        $date = Carbon::parse('2026-02-02');
        $slots = $slotService->getAvailableSlots($service, $date);

        $queries = DB::getQueryLog();

        // Should not have excessive queries (N+1 would show many similar queries)
        $selectQueries = collect($queries)->filter(fn ($q) => str_starts_with($q['query'], 'select'));

        // We expect reasonable number of queries, not hundreds
        expect($selectQueries->count())->toBeLessThan(15);

        DB::disableQueryLog();
    });
});
