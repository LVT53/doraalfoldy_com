<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Enums\VoucherType;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\Voucher;
use App\Services\SlotAvailabilityService;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.livewire')]
class BookingWizard extends Component
{
    public int $step = 1;

    public ?int $serviceId = null;

    public ?string $selectedDate = null;

    public ?string $selectedTime = null;

    public string $userName = '';

    public string $userEmail = '';

    public string $userPhone = '';

    public string $notes = '';

    public string $voucherCode = '';

    public ?Voucher $appliedVoucher = null;

    public float $voucherDiscount = 0;

    public bool $acceptTerms = false;

    public ?string $voucherError = null;

    public ?string $slotError = null;

    public function mount(): void
    {
        $this->step = 1;
    }

    public function selectService(int $id): void
    {
        $this->serviceId = $id;
        $this->step = 2;
        $this->selectedDate = null;
        $this->selectedTime = null;
    }

    public function selectDate(string $date): void
    {
        $this->selectedDate = $date;
        $this->step = 3;
        $this->selectedTime = null;
    }

    public function selectTime(string $time): void
    {
        $this->selectedTime = $time;
        $this->step = 4;
    }

    public function applyVoucher(): void
    {
        $this->voucherError = null;

        if (empty($this->voucherCode)) {
            $this->voucherError = __('booking.invalid_voucher');

            return;
        }

        $voucher = Voucher::valid($this->voucherCode)->first();

        if (! $voucher) {
            $this->voucherError = __('booking.invalid_voucher');
            $this->appliedVoucher = null;
            $this->voucherDiscount = 0;

            return;
        }

        $this->appliedVoucher = $voucher;
        $this->calculateDiscount();
    }

    private function calculateDiscount(): void
    {
        if (! $this->appliedVoucher) {
            $this->voucherDiscount = 0;

            return;
        }

        $service = $this->getSelectedService();
        if (! $service) {
            $this->voucherDiscount = 0;

            return;
        }

        $price = (float) $service->price;

        if ($this->appliedVoucher->type === VoucherType::PERCENTAGE) {
            $this->voucherDiscount = $price * ((float) $this->appliedVoucher->value / 100);
        } elseif ($this->appliedVoucher->type === VoucherType::FIXED) {
            $this->voucherDiscount = min((float) $this->appliedVoucher->value, $price);
        } elseif ($this->appliedVoucher->type === VoucherType::GIFT_CARD) {
            $this->voucherDiscount = min((float) $this->appliedVoucher->balance, $price);
        }
    }

    public function proceedToPayment(): void
    {
        $this->validate([
            'userName' => 'required|string|max:255',
            'userEmail' => 'required|email|max:255',
            'userPhone' => 'nullable|string|max:50',
            'notes' => 'nullable|string|max:1000',
            'acceptTerms' => 'accepted',
        ], [
            'userName.required' => __('validation.required', ['attribute' => __('booking.your_name')]),
            'userEmail.required' => __('validation.required', ['attribute' => __('booking.your_email')]),
            'userEmail.email' => __('validation.email', ['attribute' => __('booking.your_email')]),
            'acceptTerms.accepted' => __('validation.accepted', ['attribute' => 'ÁSZF']),
        ]);

        $this->step = 5;
    }

    public function initiatePayment(SlotAvailabilityService $slotService): void
    {
        $this->slotError = null;

        $service = $this->getSelectedService();
        if (! $service) {
            $this->slotError = __('booking.slot_taken');

            return;
        }

        $startTime = Carbon::parse("{$this->selectedDate} {$this->selectedTime}");

        $appointment = $slotService->reserveSlot($service, $startTime, [
            'name' => $this->userName,
            'email' => $this->userEmail,
            'phone' => $this->userPhone,
            'notes' => $this->notes,
            'locale' => app()->getLocale(),
        ]);

        if (! $appointment) {
            $this->slotError = __('booking.slot_taken');

            return;
        }

        session(['pending_appointment_id' => $appointment->id]);

        $this->redirectRoute('booking.payment', ['appointment' => $appointment->id]);
    }

    public function goBack(): void
    {
        if ($this->step > 1) {
            $this->step--;
        }
    }

    public function getSelectedService(): ?Service
    {
        if (! $this->serviceId) {
            return null;
        }

        return Service::with('category')
            ->where('is_active', true)
            ->find($this->serviceId);
    }

    public function getAvailableDatesProperty(): Collection
    {
        $service = $this->getSelectedService();
        if (! $service) {
            return collect();
        }

        $slotService = app(SlotAvailabilityService::class);

        return $slotService->getAvailableDates(
            $service,
            Carbon::now(),
            Carbon::now()->addDays(30)
        );
    }

    public function getAvailableSlotsProperty(): Collection
    {
        $service = $this->getSelectedService();
        if (! $service || ! $this->selectedDate) {
            return collect();
        }

        $slotService = app(SlotAvailabilityService::class);

        return $slotService->getAvailableSlots(
            $service,
            Carbon::parse($this->selectedDate)
        );
    }

    public function getPriceBreakdownProperty(): array
    {
        $service = $this->getSelectedService();
        if (! $service) {
            return [
                'total' => 0,
                'discount' => 0,
                'discountedTotal' => 0,
                'deposit' => 0,
                'remaining' => 0,
            ];
        }

        $total = (float) $service->price;
        $discount = $this->voucherDiscount;
        $discountedTotal = max(0, $total - $discount);
        $deposit = (float) $service->deposit_fee;
        $remaining = max(0, $discountedTotal - $deposit);

        return [
            'total' => $total,
            'discount' => $discount,
            'discountedTotal' => $discountedTotal,
            'deposit' => $deposit,
            'remaining' => $remaining,
        ];
    }

    public function getGroupedServicesProperty(): Collection
    {
        return ServiceCategory::with(['services' => function ($query) {
            $query->where('is_active', true)
                ->orderBy('name');
        }])
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();
    }

    public function render()
    {
        return view('livewire.booking-wizard');
    }
}
