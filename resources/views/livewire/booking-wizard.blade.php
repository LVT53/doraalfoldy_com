<div class="site-container py-8 bg-brand-beige-light min-h-screen">
    <div class="max-w-4xl mx-auto">
        <div class="flex justify-end mb-4">
            <x-language-switcher />
        </div>
        <h1 class="text-3xl font-serif font-bold text-center mb-8 text-neutral-900">{{ __('booking.select_service') }}</h1>

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
                        <div class="bg-brand-beige rounded-[15px] shadow-lg border border-brand-gold/5 p-6 md:p-8 transform transition-all duration-500 hover:shadow-xl">
                            <h2 class="text-2xl font-serif font-bold mb-6 text-neutral-900 flex items-center gap-3">
                                <span class="w-2 h-8 bg-brand-gold rounded-full"></span>
                                {{ $category->name }}
                            </h2>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                @foreach ($category->services as $service)
                                    <button
                                        wire:click="selectService({{ $service->id }})"
                                        class="group text-left p-6 border-2 border-gray-200 rounded-xl hover:border-brand-gold hover:bg-gradient-to-br hover:from-brand-gold/5 hover:to-brand-gold/10 transition-all duration-300 transform hover:-translate-y-1 hover:shadow-lg relative overflow-hidden"
                                    >
                                        <div class="absolute top-0 right-0 w-20 h-20 bg-brand-gold/10 rounded-bl-full transform translate-x-10 -translate-y-10 group-hover:translate-x-8 group-hover:-translate-y-8 transition-transform duration-300"></div>
                                        <h3 class="font-serif font-bold text-lg text-neutral-900 group-hover:text-brand-gold transition-colors duration-300">{{ $service->name }}</h3>
                                        <p class="text-sm text-gray-600 mt-2 flex items-center gap-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            {{ $service->duration_minutes }} perc
                                        </p>
                                        <p class="text-xl font-bold text-brand-gold mt-3 group-hover:scale-105 transition-transform duration-300 inline-block">{{ number_format($service->price, 0, ',', ' ') }} Ft</p>
                                        @if ($service->description)
                                            <p class="text-sm text-gray-500 mt-3 line-clamp-2">{{ $service->description }}</p>
                                        @endif
                                        <div class="mt-4 flex items-center text-brand-gold font-medium text-sm opacity-0 group-hover:opacity-100 transition-opacity duration-300 transform translate-y-2 group-hover:translate-y-0">
                                            <span>{{ __('booking.select') }}</span>
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1 transform group-hover:translate-x-1 transition-transform duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                            </svg>
                                        </div>
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        @endif

        @if ($step === 2)
            <div class="bg-brand-beige rounded-[15px] shadow-lg border border-brand-gold/5 p-6 md:p-8">
                <div class="flex items-center justify-between mb-8">
                    <button
                        wire:click="goBack"
                        class="group flex items-center gap-2 text-gray-600 hover:text-brand-gold transition-all duration-300 px-3 py-2 rounded-lg hover:bg-brand-gold/10"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 transform group-hover:-translate-x-1 transition-transform duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                        <span class="text-sm font-medium">{{ __('booking.back') }}</span>
                    </button>
                    <h2 class="text-2xl font-serif font-bold text-neutral-900">{{ __('booking.select_date') }}</h2>
                    <div class="w-20"></div>
                </div>

                @php
                    $service = $this->getSelectedService();
                @endphp

                @if ($service)
                    <div class="mb-8 p-5 bg-gradient-to-r from-brand-gold/20 to-brand-gold/5 rounded-xl border border-brand-gold/20 shadow-sm">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-brand-gold/20 rounded-full flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-brand-gold" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-serif font-bold text-lg text-neutral-900">{{ $service->name }}</h3>
                                <p class="text-sm text-gray-600">{{ $service->duration_minutes }} perc • {{ number_format($service->price, 0, ',', ' ') }} Ft</p>
                            </div>
                        </div>
                    </div>
                @endif

                @php
                    $firstDay = $this->availableDates->first()?->copy()->startOfMonth() ?? now();
                    $monthName = $firstDay->translatedFormat('F Y');
                @endphp

                <div class="mb-6 text-center">
                    <h3 class="text-xl font-serif font-bold text-neutral-900">{{ $monthName }}</h3>
                </div>

                <div class="bg-brand-beige-light rounded-[15px] p-4 md:p-6">
                    <div class="grid grid-cols-7 gap-2 mb-4">
                        @foreach (['H', 'K', 'Sze', 'Cs', 'P', 'Szo', 'V'] as $day)
                            <div class="text-center text-sm font-bold text-gray-500 py-3">{{ $day }}</div>
                        @endforeach
                    </div>

                    <div class="grid grid-cols-7 gap-2">
                        @php
                            $startOfCalendar = $firstDay->copy()->startOfWeek();
                            $endOfCalendar = $firstDay->copy()->endOfMonth()->endOfWeek();
                            $calendarDays = [];
                            $currentDay = $startOfCalendar->copy();
                            while ($currentDay <= $endOfCalendar) {
                                $calendarDays[] = $currentDay->copy();
                                $currentDay->addDay();
                            }
                        @endphp

                        @foreach ($calendarDays as $day)
                            @php
                                $isAvailable = $this->availableDates->contains(fn ($date) => $date->isSameDay($day));
                                $isCurrentMonth = $day->isSameMonth($firstDay);
                                $isToday = $day->isToday();
                            @endphp

                            <div class="aspect-square">
                                @if ($isAvailable && $isCurrentMonth)
                                    <button
                                        wire:click="selectDate('{{ $day->format('Y-m-d') }}')"
                                        class="w-full h-full flex items-center justify-center rounded-xl text-base font-bold transition-all duration-300 transform hover:scale-110 hover:shadow-lg bg-brand-gold text-white hover:bg-brand-gold/90 shadow-md"
                                    >
                                        {{ $day->format('j') }}
                                    </button>
                                @elseif ($isCurrentMonth)
                                    <div class="w-full h-full flex items-center justify-center rounded-xl text-base font-medium bg-brand-beige text-gray-400 cursor-not-allowed">
                                        {{ $day->format('j') }}
                                    </div>
                                @else
                                    <div class="w-full h-full flex items-center justify-center rounded-xl text-base font-medium text-gray-300">
                                        {{ $day->format('j') }}
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="mt-6 flex items-center justify-center gap-6 text-sm">
                    <div class="flex items-center gap-2">
                        <div class="w-4 h-4 bg-brand-gold rounded"></div>
                        <span class="text-gray-600">{{ __('booking.available') }}</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-4 h-4 bg-gray-200 rounded"></div>
                        <span class="text-gray-600">{{ __('booking.not_available') }}</span>
                    </div>
                </div>
            </div>
        @endif

        @if ($step === 3)
            <div class="bg-brand-beige rounded-[15px] shadow-lg border border-brand-gold/5 p-6 md:p-8">
                <div class="flex items-center justify-between mb-8">
                    <button
                        wire:click="goBack"
                        class="group flex items-center gap-2 text-gray-600 hover:text-brand-gold transition-all duration-300 px-3 py-2 rounded-lg hover:bg-brand-gold/10"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 transform group-hover:-translate-x-1 transition-transform duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                        <span class="text-sm font-medium">{{ __('booking.back') }}</span>
                    </button>
                    <h2 class="text-2xl font-serif font-bold text-neutral-900">{{ __('booking.select_time') }}</h2>
                    <div class="w-20"></div>
                </div>

                @php
                    $service = $this->getSelectedService();
                @endphp

                @if ($service && $selectedDate)
                    <div class="mb-8 p-5 bg-gradient-to-r from-brand-gold/20 to-brand-gold/5 rounded-xl border border-brand-gold/20 shadow-sm">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-brand-gold/20 rounded-full flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-brand-gold" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-serif font-bold text-lg text-neutral-900">{{ $service->name }}</h3>
                                <p class="text-sm text-gray-600">{{ \Carbon\Carbon::parse($selectedDate)->translatedFormat('Y. F j.') }}</p>
                            </div>
                        </div>
                    </div>
                @endif

                @if ($this->availableSlots->isEmpty())
                    <div class="text-center py-12 px-6 bg-brand-beige-light rounded-[15px]">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <p class="text-gray-500 text-lg">{{ __('booking.no_slots_available') }}</p>
                    </div>
                @else
                    <div class="bg-brand-beige-light rounded-[15px] p-6">
                        <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 lg:grid-cols-6 gap-3">
                            @foreach ($this->availableSlots as $slot)
                                <button
                                    wire:click="selectTime('{{ $slot->format('H:i') }}')"
                                    class="group py-4 px-3 bg-brand-beige-light border-2 border-gray-200 rounded-[15px] hover:border-brand-gold hover:bg-brand-gold hover:text-white transition-all duration-300 transform hover:-translate-y-1 hover:shadow-lg font-bold text-gray-700"
                                >
                                    <span class="group-hover:scale-110 transition-transform duration-300 inline-block">{{ $slot->format('H:i') }}</span>
                                </button>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        @endif

        @if ($step === 4)
            <div class="bg-brand-beige rounded-[15px] shadow-lg border border-brand-gold/5 p-6 md:p-10">
                <div class="flex items-center justify-between mb-8">
                    <button
                        wire:click="goBack"
                        class="group flex items-center gap-2 text-gray-600 hover:text-brand-gold transition-all duration-300 px-3 py-2 rounded-lg hover:bg-brand-gold/10"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 transform group-hover:-translate-x-1 transition-transform duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                        <span class="text-sm font-medium">{{ __('booking.back') }}</span>
                    </button>
                    <h2 class="text-2xl font-serif font-bold text-neutral-900">{{ __('booking.your_details') }}</h2>
                    <div class="w-24"></div>
                </div>

                @php
                    $service = $this->getSelectedService();
                    $priceBreakdown = $this->priceBreakdown;
                @endphp

                @if ($service && $selectedDate && $selectedTime)
                    <div class="mb-8 p-6 bg-gradient-to-r from-brand-gold/20 to-brand-gold/5 rounded-xl border border-brand-gold/20 shadow-sm">
                        <div class="flex items-center gap-4">
                            <div class="w-14 h-14 bg-brand-gold/20 rounded-full flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-brand-gold" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-serif font-bold text-lg text-neutral-900">{{ $service->name }}</h3>
                                <p class="text-sm text-gray-600">
                                    {{ \Carbon\Carbon::parse($selectedDate)->translatedFormat('Y. F j.') }} {{ $selectedTime }}
                                </p>
                            </div>
                        </div>
                    </div>
                @endif

                <form wire:submit="proceedToPayment" class="space-y-6">
                    <div class="space-y-2">
                        <label class="block text-sm font-semibold text-gray-700">{{ __('booking.your_name') }} *</label>
                        <input
                            type="text"
                            wire:model="userName"
                            class="w-full px-5 py-3 border border-brand-gold/30 rounded-[15px] focus:ring-2 focus:ring-brand-gold focus:border-brand-gold transition-all duration-300 bg-white"
                            placeholder="{{ __('booking.enter_name') }}"
                            required
                        >
                        @error('userName')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="space-y-2">
                        <label class="block text-sm font-semibold text-gray-700">{{ __('booking.your_email') }} *</label>
                        <input
                            type="email"
                            wire:model="userEmail"
                            class="w-full px-5 py-3 border border-brand-gold/30 rounded-[15px] focus:ring-2 focus:ring-brand-gold focus:border-brand-gold transition-all duration-300 bg-white"
                            placeholder="{{ __('booking.enter_email') }}"
                            required
                        >
                        @error('userEmail')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="space-y-2">
                        <label class="block text-sm font-semibold text-gray-700">{{ __('booking.your_phone') }}</label>
                        <input
                            type="tel"
                            wire:model="userPhone"
                            class="w-full px-5 py-3 border border-brand-gold/30 rounded-[15px] focus:ring-2 focus:ring-brand-gold focus:border-brand-gold transition-all duration-300 bg-white"
                            placeholder="{{ __('booking.enter_phone') }}"
                        >
                    </div>

                    <div class="space-y-2">
                        <label class="block text-sm font-semibold text-gray-700">{{ __('booking.notes') }}</label>
                        <textarea
                            wire:model="notes"
                            rows="4"
                            class="w-full px-5 py-3 border border-brand-gold/30 rounded-[15px] focus:ring-2 focus:ring-brand-gold focus:border-brand-gold transition-all duration-300 resize-none bg-white"
                            placeholder="{{ __('booking.enter_notes') }}"
                        ></textarea>
                    </div>

                    <div class="bg-brand-beige-light rounded-[15px] p-6 space-y-4">
                        <label class="block text-sm font-semibold text-gray-700">{{ __('booking.voucher_code') }}</label>
                        <div class="flex gap-3">
                            <input
                                type="text"
                                wire:model="voucherCode"
                                class="flex-1 px-5 py-3 border border-brand-gold/30 rounded-[15px] focus:ring-2 focus:ring-brand-gold focus:border-brand-gold transition-all duration-300 bg-white"
                                placeholder="{{ __('booking.voucher_placeholder') }}"
                            >
                            <button
                                type="button"
                                wire:click="applyVoucher"
                                class="px-6 py-3 bg-gray-200 text-gray-700 rounded-[15px] hover:bg-brand-gold hover:text-white transition-all duration-300 font-semibold"
                            >
                                {{ __('booking.apply_voucher') }}
                            </button>
                        </div>
                        @if ($voucherError)
                            <p class="text-red-500 text-sm">{{ $voucherError }}</p>
                        @endif
                        @if ($appliedVoucher)
                            <p class="text-green-600 text-sm flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                {{ __('booking.discount_applied') }}
                            </p>
                        @endif
                    </div>

                    <div class="bg-brand-beige-light rounded-[15px] p-6 border border-brand-gold/5">
                        <h3 class="font-serif font-bold text-lg mb-4 text-neutral-900">{{ __('booking.price_summary') }}</h3>
                        <div class="space-y-3">
                            <div class="flex justify-between items-center py-2">
                                <span class="text-gray-600">{{ __('booking.total_price') }}:</span>
                                <span class="font-semibold">{{ number_format($priceBreakdown['total'], 0, ',', ' ') }} Ft</span>
                            </div>
                            @if ($priceBreakdown['discount'] > 0)
                                <div class="flex justify-between items-center py-2 text-green-600">
                                    <span>{{ __('booking.discount_applied') }}:</span>
                                    <span class="font-bold">-{{ number_format($priceBreakdown['discount'], 0, ',', ' ') }} Ft</span>
                                </div>
                            @endif
                            <div class="flex justify-between items-center py-3 border-t-2 border-gray-200">
                                <span class="font-bold text-gray-800">{{ __('booking.deposit_amount') }}:</span>
                                <span class="text-xl font-bold text-brand-gold">{{ number_format($priceBreakdown['deposit'], 0, ',', ' ') }} Ft</span>
                            </div>
                            <div class="flex justify-between items-center py-2 text-gray-600">
                                <span>{{ __('booking.remaining_amount') }}:</span>
                                <span>{{ number_format($priceBreakdown['remaining'], 0, ',', ' ') }} Ft</span>
                            </div>
                        </div>
                    </div>

                    <div class="bg-brand-gold/10 rounded-[15px] p-6">
                        <div class="flex items-start gap-4">
                            <input
                                type="checkbox"
                                wire:model="acceptTerms"
                                id="acceptTerms"
                                class="mt-1 w-5 h-5 text-brand-gold rounded focus:ring-brand-gold"
                                required
                            >
                            <label for="acceptTerms" class="text-sm text-gray-700 leading-relaxed">
                                Elfogadom az <a href="{{ route('aszf') }}" target="_blank" class="text-brand-gold hover:underline font-semibold">ÁSZF</a>-et és a <a href="{{ route('gdpr') }}" target="_blank" class="text-brand-gold hover:underline font-semibold">GDPR</a> szabályzatot *
                            </label>
                        </div>
                        @error('acceptTerms')
                            <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                        @enderror
                    </div>

                    <button
                        type="submit"
                        class="w-full py-4 bg-brand-gold text-white font-bold text-lg rounded-[15px] hover:bg-brand-gold/90 transition-all duration-300 transform hover:-translate-y-1 hover:shadow-gold shadow-gold"
                    >
                        {{ __('booking.pay_deposit') }}
                    </button>
                </form>
            </div>
        @endif

        @if ($step === 5)
            <div class="bg-brand-beige rounded-[15px] shadow-lg border border-brand-gold/5 p-6 md:p-10">
                <div class="flex items-center justify-between mb-8">
                    <button
                        wire:click="goBack"
                        class="group flex items-center gap-2 text-gray-600 hover:text-brand-gold transition-all duration-300 px-3 py-2 rounded-lg hover:bg-brand-gold/10"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 transform group-hover:-translate-x-1 transition-transform duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                        <span class="text-sm font-medium">{{ __('booking.back') }}</span>
                    </button>
                    <h2 class="text-2xl font-serif font-bold text-neutral-900">{{ __('booking.confirm_payment') }}</h2>
                    <div class="w-24"></div>
                </div>

                @php
                    $service = $this->getSelectedService();
                    $priceBreakdown = $this->priceBreakdown;
                @endphp

                <div class="space-y-6 mb-8">
                    <div class="p-6 bg-gradient-to-r from-brand-gold/20 to-brand-gold/5 rounded-xl border border-brand-gold/20 shadow-sm">
                        <div class="flex items-center gap-4 mb-4">
                            <div class="w-12 h-12 bg-brand-gold/20 rounded-full flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-brand-gold" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                                </svg>
                            </div>
                            <h3 class="font-serif font-bold text-lg text-neutral-900">{{ __('booking.appointment_details') }}</h3>
                        </div>
                        @if ($service)
                            <p class="font-semibold text-gray-800">{{ $service->name }}</p>
                        @endif
                        @if ($selectedDate && $selectedTime)
                            <p class="text-gray-600">
                                {{ \Carbon\Carbon::parse($selectedDate)->translatedFormat('Y. F j.') }} {{ $selectedTime }}
                            </p>
                        @endif
                    </div>

                    <div class="p-6 bg-brand-beige-light rounded-[15px]">
                        <h3 class="font-serif font-bold text-lg mb-4 text-neutral-900 flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-brand-gold" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            {{ __('booking.your_name') }}
                        </h3>
                        <p class="font-semibold text-gray-800">{{ $userName }}</p>
                        <p class="text-gray-600">{{ $userEmail }}</p>
                        @if ($userPhone)
                            <p class="text-gray-600">{{ $userPhone }}</p>
                        @endif
                    </div>

                    @if ($notes)
                        <div class="p-6 bg-brand-beige-light rounded-[15px]">
                            <h3 class="font-serif font-bold text-lg mb-2 text-neutral-900">{{ __('booking.notes') }}</h3>
                            <p class="text-gray-600">{{ $notes }}</p>
                        </div>
                    @endif

                    <div class="bg-brand-beige-light rounded-[15px] p-6 border border-brand-gold/5">
                        <h3 class="font-serif font-bold text-lg mb-4 text-neutral-900">{{ __('booking.price_summary') }}</h3>
                        <div class="space-y-3">
                            <div class="flex justify-between items-center py-2">
                                <span class="text-gray-600">{{ __('booking.total_price') }}:</span>
                                <span class="font-semibold">{{ number_format($priceBreakdown['total'], 0, ',', ' ') }} Ft</span>
                            </div>
                            @if ($priceBreakdown['discount'] > 0)
                                <div class="flex justify-between items-center py-2 text-green-600">
                                    <span>{{ __('booking.discount_applied') }}:</span>
                                    <span class="font-bold">-{{ number_format($priceBreakdown['discount'], 0, ',', ' ') }} Ft</span>
                                </div>
                            @endif
                            <div class="flex justify-between items-center py-3 border-t-2 border-gray-200">
                                <span class="font-bold text-gray-800">{{ __('booking.deposit_amount') }}:</span>
                                <span class="text-xl font-bold text-brand-gold">{{ number_format($priceBreakdown['deposit'], 0, ',', ' ') }} Ft</span>
                            </div>
                            <div class="flex justify-between items-center py-2 text-gray-600">
                                <span>{{ __('booking.remaining_amount') }}:</span>
                                <span>{{ number_format($priceBreakdown['remaining'], 0, ',', ' ') }} Ft</span>
                            </div>
                        </div>
                    </div>
                </div>

                @if ($slotError)
                    <div class="bg-red-100 border-2 border-red-400 text-red-700 px-6 py-4 rounded-[15px] mb-6 flex items-center gap-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        {{ $slotError }}
                    </div>
                @endif

                <button
                    wire:click="initiatePayment"
                    class="w-full py-4 bg-brand-gold text-white font-bold text-lg rounded-[15px] hover:bg-brand-gold/90 transition-all duration-300 transform hover:-translate-y-1 hover:shadow-gold shadow-gold"
                >
                    {{ __('booking.pay_deposit') }}
                </button>
            </div>
        @endif
    </div>
</div>
