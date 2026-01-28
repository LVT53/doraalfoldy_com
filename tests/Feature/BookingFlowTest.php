<?php

use App\Enums\AppointmentStatus;
use App\Enums\VoucherType;
use App\Livewire\BookingWizard;
use App\Models\Appointment;
use App\Models\Schedule;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\Setting;
use App\Models\Voucher;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;

describe('Booking Flow', function () {
    beforeEach(function () {
        Setting::set('slot_lock', '1');
        Setting::set('default_buffer_minutes', 0);
    });

    it('completes full wizard flow from service selection to payment', function () {
        // Arrange: Create service with schedule
        $category = ServiceCategory::factory()->create();
        $service = Service::factory()->create([
            'category_id' => $category->id,
            'duration_minutes' => 60,
            'buffer_minutes' => 0,
            'price' => 15000.00,
            'deposit_fee' => 5000.00,
            'is_active' => true,
        ]);

        // Create schedule for tomorrow (day 1 = Monday if tomorrow is Monday)
        $tomorrow = Carbon::tomorrow();
        Schedule::factory()->create([
            'day_of_week' => (int) $tomorrow->format('w'),
            'start_time' => '09:00',
            'end_time' => '17:00',
            'is_off' => false,
        ]);

        // Act: Step 1 - Select service
        $component = Livewire::test(BookingWizard::class)
            ->call('selectService', $service->id);

        expect($component->get('step'))->toBe(2);
        expect($component->get('serviceId'))->toBe($service->id);

        // Act: Step 2 - Select date
        $component->call('selectDate', $tomorrow->format('Y-m-d'));

        expect($component->get('step'))->toBe(3);
        expect($component->get('selectedDate'))->toBe($tomorrow->format('Y-m-d'));

        // Act: Step 3 - Select time
        $component->call('selectTime', '10:00');

        expect($component->get('step'))->toBe(4);
        expect($component->get('selectedTime'))->toBe('10:00');

        // Act: Step 4 - Fill customer details and proceed
        $component->set('userName', 'John Doe')
            ->set('userEmail', 'john@example.com')
            ->set('userPhone', '+36123456789')
            ->set('notes', 'Test appointment notes')
            ->set('acceptTerms', true)
            ->call('proceedToPayment');

        expect($component->get('step'))->toBe(5);

        // Assert: Price breakdown is correct
        $priceBreakdown = $component->get('priceBreakdown');
        expect($priceBreakdown['total'])->toBe(15000.00);
        expect((float) $priceBreakdown['discount'])->toBe(0.0);
        expect($priceBreakdown['discountedTotal'])->toBe(15000.00);
        expect($priceBreakdown['deposit'])->toBe(5000.00);
        expect($priceBreakdown['remaining'])->toBe(10000.00);
    });

    it('applies percentage voucher correctly', function () {
        // Arrange
        $category = ServiceCategory::factory()->create();
        $service = Service::factory()->create([
            'category_id' => $category->id,
            'duration_minutes' => 60,
            'price' => 10000.00,
            'is_active' => true,
        ]);

        $voucher = Voucher::factory()->create([
            'code' => 'DISCOUNT10',
            'type' => VoucherType::PERCENTAGE,
            'value' => 10, // 10% discount
        ]);

        $tomorrow = Carbon::tomorrow();
        Schedule::factory()->create([
            'day_of_week' => (int) $tomorrow->format('w'),
            'start_time' => '09:00',
            'end_time' => '17:00',
            'is_off' => false,
        ]);

        // Act
        $component = Livewire::test(BookingWizard::class)
            ->call('selectService', $service->id)
            ->call('selectDate', $tomorrow->format('Y-m-d'))
            ->call('selectTime', '10:00')
            ->set('voucherCode', 'DISCOUNT10')
            ->call('applyVoucher');

        // Assert
        expect($component->get('appliedVoucher'))->not->toBeNull();
        expect($component->get('voucherDiscount'))->toBe(1000.00); // 10% of 10000

        $priceBreakdown = $component->get('priceBreakdown');
        expect($priceBreakdown['discount'])->toBe(1000.00);
        expect($priceBreakdown['discountedTotal'])->toBe(9000.00);
    });

    it('applies fixed amount voucher correctly', function () {
        // Arrange
        $category = ServiceCategory::factory()->create();
        $service = Service::factory()->create([
            'category_id' => $category->id,
            'duration_minutes' => 60,
            'price' => 10000.00,
            'is_active' => true,
        ]);

        $voucher = Voucher::factory()->create([
            'code' => 'FIXED5000',
            'type' => VoucherType::FIXED,
            'value' => 5000,
        ]);

        $tomorrow = Carbon::tomorrow();
        Schedule::factory()->create([
            'day_of_week' => (int) $tomorrow->format('w'),
            'start_time' => '09:00',
            'end_time' => '17:00',
            'is_off' => false,
        ]);

        // Act
        $component = Livewire::test(BookingWizard::class)
            ->call('selectService', $service->id)
            ->call('selectDate', $tomorrow->format('Y-m-d'))
            ->call('selectTime', '10:00')
            ->set('voucherCode', 'FIXED5000')
            ->call('applyVoucher');

        // Assert
        expect($component->get('voucherDiscount'))->toBe(5000.00);

        $priceBreakdown = $component->get('priceBreakdown');
        expect($priceBreakdown['discountedTotal'])->toBe(5000.00);
    });

    it('applies gift card voucher correctly', function () {
        // Arrange
        $category = ServiceCategory::factory()->create();
        $service = Service::factory()->create([
            'category_id' => $category->id,
            'duration_minutes' => 60,
            'price' => 15000.00,
            'is_active' => true,
        ]);

        $voucher = Voucher::factory()->create([
            'code' => 'GIFT10000',
            'type' => VoucherType::GIFT_CARD,
            'value' => 10000,
            'balance' => 10000,
        ]);

        $tomorrow = Carbon::tomorrow();
        Schedule::factory()->create([
            'day_of_week' => (int) $tomorrow->format('w'),
            'start_time' => '09:00',
            'end_time' => '17:00',
            'is_off' => false,
        ]);

        // Act
        $component = Livewire::test(BookingWizard::class)
            ->call('selectService', $service->id)
            ->call('selectDate', $tomorrow->format('Y-m-d'))
            ->call('selectTime', '10:00')
            ->set('voucherCode', 'GIFT10000')
            ->call('applyVoucher');

        // Assert - gift card covers up to its balance
        expect($component->get('voucherDiscount'))->toBe(10000.00);

        $priceBreakdown = $component->get('priceBreakdown');
        expect($priceBreakdown['discountedTotal'])->toBe(5000.00);
    });

    it('shows error for invalid voucher', function () {
        // Arrange
        $category = ServiceCategory::factory()->create();
        $service = Service::factory()->create([
            'category_id' => $category->id,
            'duration_minutes' => 60,
            'is_active' => true,
        ]);

        $tomorrow = Carbon::tomorrow();
        Schedule::factory()->create([
            'day_of_week' => (int) $tomorrow->format('w'),
            'start_time' => '09:00',
            'end_time' => '17:00',
            'is_off' => false,
        ]);

        // Act
        $component = Livewire::test(BookingWizard::class)
            ->call('selectService', $service->id)
            ->call('selectDate', $tomorrow->format('Y-m-d'))
            ->call('selectTime', '10:00')
            ->set('voucherCode', 'INVALIDCODE')
            ->call('applyVoucher');

        // Assert
        expect($component->get('voucherError'))->not->toBeNull();
        expect($component->get('appliedVoucher'))->toBeNull();
        expect((float) $component->get('voucherDiscount'))->toBe(0.0);
    });

    it('creates appointment with PENDING status on payment initiation', function () {
        // Arrange
        $category = ServiceCategory::factory()->create();
        $service = Service::factory()->create([
            'category_id' => $category->id,
            'duration_minutes' => 60,
            'buffer_minutes' => 0,
            'price' => 10000.00,
            'deposit_fee' => 3000.00,
            'is_active' => true,
        ]);

        $tomorrow = Carbon::tomorrow();
        Schedule::factory()->create([
            'day_of_week' => (int) $tomorrow->format('w'),
            'start_time' => '09:00',
            'end_time' => '17:00',
            'is_off' => false,
        ]);

        // Act
        $component = Livewire::test(BookingWizard::class)
            ->call('selectService', $service->id)
            ->call('selectDate', $tomorrow->format('Y-m-d'))
            ->call('selectTime', '10:00')
            ->set('userName', 'Jane Doe')
            ->set('userEmail', 'jane@example.com')
            ->set('userPhone', '+36987654321')
            ->set('acceptTerms', true)
            ->call('proceedToPayment')
            ->call('initiatePayment');

        // Assert appointment was created
        $appointment = Appointment::first();
        expect($appointment)->not->toBeNull();
        expect($appointment->status)->toBe(AppointmentStatus::PENDING);
        expect($appointment->user_name)->toBe('Jane Doe');
        expect($appointment->user_email)->toBe('jane@example.com');
        expect($appointment->service_id)->toBe($service->id);
        expect((float) $appointment->price_at_booking)->toBe(10000.00);
        expect((float) $appointment->deposit_at_booking)->toBe(3000.00);
    });

    it('allows going back to previous steps', function () {
        // Arrange
        $category = ServiceCategory::factory()->create();
        $service = Service::factory()->create([
            'category_id' => $category->id,
            'duration_minutes' => 60,
            'is_active' => true,
        ]);

        $tomorrow = Carbon::tomorrow();
        Schedule::factory()->create([
            'day_of_week' => (int) $tomorrow->format('w'),
            'start_time' => '09:00',
            'end_time' => '17:00',
            'is_off' => false,
        ]);

        // Act - Go through steps
        $component = Livewire::test(BookingWizard::class)
            ->call('selectService', $service->id)
            ->call('selectDate', $tomorrow->format('Y-m-d'))
            ->call('selectTime', '10:00');

        expect($component->get('step'))->toBe(4);

        // Go back
        $component->call('goBack');
        expect($component->get('step'))->toBe(3);

        $component->call('goBack');
        expect($component->get('step'))->toBe(2);

        $component->call('goBack');
        expect($component->get('step'))->toBe(1);

        // Can't go below 1
        $component->call('goBack');
        expect($component->get('step'))->toBe(1);
    });

    it('validates customer details before proceeding to payment', function () {
        // Arrange
        $category = ServiceCategory::factory()->create();
        $service = Service::factory()->create([
            'category_id' => $category->id,
            'duration_minutes' => 60,
            'is_active' => true,
        ]);

        $tomorrow = Carbon::tomorrow();
        Schedule::factory()->create([
            'day_of_week' => (int) $tomorrow->format('w'),
            'start_time' => '09:00',
            'end_time' => '17:00',
            'is_off' => false,
        ]);

        // Act - Try to proceed without required fields
        $component = Livewire::test(BookingWizard::class)
            ->call('selectService', $service->id)
            ->call('selectDate', $tomorrow->format('Y-m-d'))
            ->call('selectTime', '10:00')
            ->set('userName', '')
            ->set('userEmail', '')
            ->set('acceptTerms', false)
            ->call('proceedToPayment');

        // Assert - Should have validation errors and stay on step 4
        expect($component->get('step'))->toBe(4);
        $component->assertHasErrors(['userName', 'userEmail', 'acceptTerms']);
    });

    it('validates email format', function () {
        // Arrange
        $category = ServiceCategory::factory()->create();
        $service = Service::factory()->create([
            'category_id' => $category->id,
            'duration_minutes' => 60,
            'is_active' => true,
        ]);

        $tomorrow = Carbon::tomorrow();
        Schedule::factory()->create([
            'day_of_week' => (int) $tomorrow->format('w'),
            'start_time' => '09:00',
            'end_time' => '17:00',
            'is_off' => false,
        ]);

        // Act
        $component = Livewire::test(BookingWizard::class)
            ->call('selectService', $service->id)
            ->call('selectDate', $tomorrow->format('Y-m-d'))
            ->call('selectTime', '10:00')
            ->set('userName', 'Test User')
            ->set('userEmail', 'invalid-email')
            ->set('acceptTerms', true)
            ->call('proceedToPayment');

        // Assert
        expect($component->get('step'))->toBe(4);
        $component->assertHasErrors(['userEmail']);
    });

    it('does not have N+1 query problems', function () {
        // Arrange
        $category = ServiceCategory::factory()->create();
        $service = Service::factory()->create([
            'category_id' => $category->id,
            'duration_minutes' => 60,
            'is_active' => true,
        ]);

        $tomorrow = Carbon::tomorrow();
        Schedule::factory()->create([
            'day_of_week' => (int) $tomorrow->format('w'),
            'start_time' => '09:00',
            'end_time' => '17:00',
            'is_off' => false,
        ]);

        // Act & Assert - Enable query log and check count
        DB::enableQueryLog();

        Livewire::test(BookingWizard::class)
            ->call('selectService', $service->id)
            ->call('selectDate', $tomorrow->format('Y-m-d'))
            ->call('selectTime', '10:00');

        $queries = DB::getQueryLog();

        // Should not have excessive queries (N+1 would show many similar queries)
        $selectQueries = collect($queries)->filter(fn ($q) => str_starts_with($q['query'], 'select'));

        // We expect reasonable number of queries, not hundreds
        expect($selectQueries->count())->toBeLessThan(150);

        DB::disableQueryLog();
    });
});
