<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-3xl font-bold text-center mb-8">{{ __('booking.select_service') }}</h1>

        <div class="flex justify-center mb-8">
            <div class="flex items-center space-x-2">
                @for ($i = 1; $i <= 5; $i++)
                    <div class="flex items-center">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-semibold
                            {{ $step >= $i ? 'bg-brand-gold text-white' : 'bg-gray-200 text-gray-600' }}">
                            {{ $i }}
                        </div>
                        @if ($i < 5)
                            <div class="w-8 h-0.5 mx-1 {{ $step > $i ? 'bg-brand-gold' : 'bg-gray-200' }}"></div>
                        @endif
                    </div>
                @endfor
            </div>
        </div>

        @if ($step === 1)
            <div class="space-y-8">
                @foreach ($this->groupedServices as $category)
                    @if ($category->services->isNotEmpty())
                        <div class="bg-white rounded-lg shadow-md p-6">
                            <h2 class="text-xl font-semibold mb-4 text-gray-800">{{ $category->name }}</h2>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @foreach ($category->services as $service)
                                    <button
                                        wire:click="selectService({{ $service->id }})"
                                        class="text-left p-4 border-2 border-gray-200 rounded-lg hover:border-brand-gold hover:bg-brand-gold/5 transition-all duration-200"
                                    >
                                        <h3 class="font-semibold text-gray-800">{{ $service->name }}</h3>
                                        <p class="text-sm text-gray-600 mt-1">{{ $service->duration_minutes }} perc</p>
                                        <p class="text-lg font-bold text-brand-gold mt-2">{{ number_format($service->price, 0, ',', ' ') }} Ft</p>
                                        @if ($service->description)
                                            <p class="text-sm text-gray-500 mt-2">{{ $service->description }}</p>
                                        @endif
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        @endif

        @if ($step === 2)
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center justify-between mb-6">
                    <button wire:click="goBack" class="text-gray-600 hover:text-brand-gold transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </button>
                    <h2 class="text-xl font-semibold">{{ __('booking.select_date') }}</h2>
                    <div class="w-6"></div>
                </div>

                @php
                    $service = $this->getSelectedService();
                @endphp

                @if ($service)
                    <div class="mb-6 p-4 bg-brand-gold/10 rounded-lg">
                        <h3 class="font-semibold">{{ $service->name }}</h3>
                        <p class="text-sm text-gray-600">{{ $service->duration_minutes }} perc • {{ number_format($service->price, 0, ',', ' ') }} Ft</p>
                    </div>
                @endif

                <div class="grid grid-cols-7 gap-2">
                    @foreach (['H', 'K', 'Sze', 'Cs', 'P', 'Szo', 'V'] as $day)
                        <div class="text-center text-sm font-semibold text-gray-500 py-2">{{ $day }}</div>
                    @endforeach

                    @php
                        $firstDay = $this->availableDates->first()?->copy()->startOfMonth() ?? now();
                        $startOfCalendar = $firstDay->copy()->startOfWeek();
                        $endOfCalendar = $firstDay->copy()->endOfMonth()->endOfWeek();
                        $currentDay = $startOfCalendar->copy();
                    @endphp

                    @while ($currentDay <= $endOfCalendar)
                        @php
                            $isAvailable = $this->availableDates->contains(fn ($date) => $date->isSameDay($currentDay));
                            $isCurrentMonth = $currentDay->isSameMonth($firstDay);
                        @endphp

                        <button
                            wire:click="selectDate('{{ $currentDay->format('Y-m-d') }}')"
                            @disabled(! $isAvailable || ! $isCurrentMonth)
                            class="aspect-square flex items-center justify-center rounded-lg text-sm font-medium transition-all
                                {{ $isAvailable && $isCurrentMonth
                                    ? 'bg-brand-gold text-white hover:bg-brand-gold/90 cursor-pointer'
                                    : ($isCurrentMonth ? 'bg-gray-100 text-gray-400 cursor-not-allowed' : 'bg-transparent')
                                }}"
                        >
                            {{ $currentDay->format('j') }}
                        </button>

                        @php
                            $currentDay->addDay();
                        @endphp
                    @endwhile
                </div>
            </div>
        @endif

        @if ($step === 3)
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center justify-between mb-6">
                    <button wire:click="goBack" class="text-gray-600 hover:text-brand-gold transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </button>
                    <h2 class="text-xl font-semibold">{{ __('booking.select_time') }}</h2>
                    <div class="w-6"></div>
                </div>

                @php
                    $service = $this->getSelectedService();
                @endphp

                @if ($service && $selectedDate)
                    <div class="mb-6 p-4 bg-brand-gold/10 rounded-lg">
                        <h3 class="font-semibold">{{ $service->name }}</h3>
                        <p class="text-sm text-gray-600">{{ \Carbon\Carbon::parse($selectedDate)->format('Y. F j.') }}</p>
                    </div>
                @endif

                @if ($this->availableSlots->isEmpty())
                    <div class="text-center py-8 text-gray-500">
                        {{ __('booking.no_slots_available') }}
                    </div>
                @else
                    <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 gap-3">
                        @foreach ($this->availableSlots as $slot)
                            <button
                                wire:click="selectTime('{{ $slot->format('H:i') }}')"
                                class="py-3 px-4 border-2 border-gray-200 rounded-lg hover:border-brand-gold hover:bg-brand-gold/5 transition-all duration-200 font-medium"
                            >
                                {{ $slot->format('H:i') }}
                            </button>
                        @endforeach
                    </div>
                @endif
            </div>
        @endif

        @if ($step === 4)
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center justify-between mb-6">
                    <button wire:click="goBack" class="text-gray-600 hover:text-brand-gold transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </button>
                    <h2 class="text-xl font-semibold">{{ __('booking.your_name') }}</h2>
                    <div class="w-6"></div>
                </div>

                @php
                    $service = $this->getSelectedService();
                    $priceBreakdown = $this->priceBreakdown;
                @endphp

                @if ($service && $selectedDate && $selectedTime)
                    <div class="mb-6 p-4 bg-brand-gold/10 rounded-lg">
                        <h3 class="font-semibold">{{ $service->name }}</h3>
                        <p class="text-sm text-gray-600">
                            {{ \Carbon\Carbon::parse($selectedDate)->format('Y. F j.') }} {{ $selectedTime }}
                        </p>
                    </div>
                @endif

                <form wire:submit="proceedToPayment" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('booking.your_name') }} *</label>
                        <input
                            type="text"
                            wire:model="userName"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-gold focus:border-transparent"
                            required
                        >
                        @error('userName')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('booking.your_email') }} *</label>
                        <input
                            type="email"
                            wire:model="userEmail"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-gold focus:border-transparent"
                            required
                        >
                        @error('userEmail')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('booking.your_phone') }}</label>
                        <input
                            type="tel"
                            wire:model="userPhone"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-gold focus:border-transparent"
                        >
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('booking.notes') }}</label>
                        <textarea
                            wire:model="notes"
                            rows="3"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-gold focus:border-transparent"
                        ></textarea>
                    </div>

                    <div class="border-t pt-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('booking.voucher_code') }}</label>
                        <div class="flex gap-2">
                            <input
                                type="text"
                                wire:model="voucherCode"
                                class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-gold focus:border-transparent"
                                placeholder="Kuponkód"
                            >
                            <button
                                type="button"
                                wire:click="applyVoucher"
                                class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors"
                            >
                                {{ __('booking.apply_voucher') }}
                            </button>
                        </div>
                        @if ($voucherError)
                            <p class="text-red-500 text-sm mt-1">{{ $voucherError }}</p>
                        @endif
                        @if ($appliedVoucher)
                            <p class="text-green-600 text-sm mt-1">{{ __('booking.discount_applied') }}</p>
                        @endif
                    </div>

                    <div class="border-t pt-4">
                        <h3 class="font-semibold mb-3">{{ __('booking.total_price') }}</h3>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span>{{ __('booking.total_price') }}:</span>
                                <span>{{ number_format($priceBreakdown['total'], 0, ',', ' ') }} Ft</span>
                            </div>
                            @if ($priceBreakdown['discount'] > 0)
                                <div class="flex justify-between text-green-600">
                                    <span>{{ __('booking.discount_applied') }}:</span>
                                    <span>-{{ number_format($priceBreakdown['discount'], 0, ',', ' ') }} Ft</span>
                                </div>
                            @endif
                            <div class="flex justify-between font-semibold border-t pt-2">
                                <span>{{ __('booking.deposit_amount') }}:</span>
                                <span class="text-brand-gold">{{ number_format($priceBreakdown['deposit'], 0, ',', ' ') }} Ft</span>
                            </div>
                            <div class="flex justify-between text-gray-600">
                                <span>{{ __('booking.remaining_amount') }}:</span>
                                <span>{{ number_format($priceBreakdown['remaining'], 0, ',', ' ') }} Ft</span>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-start gap-2">
                        <input
                            type="checkbox"
                            wire:model="acceptTerms"
                            id="acceptTerms"
                            class="mt-1"
                            required
                        >
                        <label for="acceptTerms" class="text-sm text-gray-600">
                            Elfogadom az <a href="{{ route('aszf') }}" target="_blank" class="text-brand-gold hover:underline">ÁSZF</a>-et és a <a href="{{ route('gdpr') }}" target="_blank" class="text-brand-gold hover:underline">GDPR</a> szabályzatot *
                        </label>
                    </div>
                    @error('acceptTerms')
                        <p class="text-red-500 text-sm">{{ $message }}</p>
                    @enderror

                    <button
                        type="submit"
                        class="w-full py-3 bg-brand-gold text-white font-semibold rounded-lg hover:bg-brand-gold/90 transition-colors"
                    >
                        {{ __('booking.pay_deposit') }}
                    </button>
                </form>
            </div>
        @endif

        @if ($step === 5)
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center justify-between mb-6">
                    <button wire:click="goBack" class="text-gray-600 hover:text-brand-gold transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </button>
                    <h2 class="text-xl font-semibold">{{ __('booking.booking_confirmed') }}</h2>
                    <div class="w-6"></div>
                </div>

                @php
                    $service = $this->getSelectedService();
                    $priceBreakdown = $this->priceBreakdown;
                @endphp

                <div class="space-y-4 mb-6">
                    <div class="p-4 bg-brand-gold/10 rounded-lg">
                        <h3 class="font-semibold mb-2">{{ __('booking.appointment_details') }}</h3>
                        @if ($service)
                            <p><strong>{{ $service->name }}</strong></p>
                        @endif
                        @if ($selectedDate && $selectedTime)
                            <p class="text-gray-600">
                                {{ \Carbon\Carbon::parse($selectedDate)->format('Y. F j.') }} {{ $selectedTime }}
                            </p>
                        @endif
                    </div>

                    <div class="p-4 bg-gray-50 rounded-lg">
                        <h3 class="font-semibold mb-2">{{ __('booking.your_name') }}</h3>
                        <p>{{ $userName }}</p>
                        <p class="text-gray-600">{{ $userEmail }}</p>
                        @if ($userPhone)
                            <p class="text-gray-600">{{ $userPhone }}</p>
                        @endif
                    </div>

                    @if ($notes)
                        <div class="p-4 bg-gray-50 rounded-lg">
                            <h3 class="font-semibold mb-2">{{ __('booking.notes') }}</h3>
                            <p class="text-gray-600">{{ $notes }}</p>
                        </div>
                    @endif

                    <div class="p-4 bg-gray-50 rounded-lg">
                        <h3 class="font-semibold mb-3">{{ __('booking.total_price') }}</h3>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span>{{ __('booking.total_price') }}:</span>
                                <span>{{ number_format($priceBreakdown['total'], 0, ',', ' ') }} Ft</span>
                            </div>
                            @if ($priceBreakdown['discount'] > 0)
                                <div class="flex justify-between text-green-600">
                                    <span>{{ __('booking.discount_applied') }}:</span>
                                    <span>-{{ number_format($priceBreakdown['discount'], 0, ',', ' ') }} Ft</span>
                                </div>
                            @endif
                            <div class="flex justify-between font-semibold border-t pt-2">
                                <span>{{ __('booking.deposit_amount') }}:</span>
                                <span class="text-brand-gold">{{ number_format($priceBreakdown['deposit'], 0, ',', ' ') }} Ft</span>
                            </div>
                            <div class="flex justify-between text-gray-600">
                                <span>{{ __('booking.remaining_amount') }}:</span>
                                <span>{{ number_format($priceBreakdown['remaining'], 0, ',', ' ') }} Ft</span>
                            </div>
                        </div>
                    </div>
                </div>

                @if ($slotError)
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                        {{ $slotError }}
                    </div>
                @endif

                <button
                    wire:click="initiatePayment"
                    class="w-full py-3 bg-brand-gold text-white font-semibold rounded-lg hover:bg-brand-gold/90 transition-colors"
                >
                    {{ __('booking.pay_deposit') }}
                </button>
            </div>
        @endif
    </div>
</div>
