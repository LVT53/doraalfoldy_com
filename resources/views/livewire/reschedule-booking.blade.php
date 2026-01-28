<div class="site-container py-8 bg-brand-beige-light">
    <div class="max-w-4xl mx-auto">
        <div class="flex justify-end mb-4">
            <x-language-switcher />
        </div>
        <h1 class="font-serif font-bold text-3xl md:text-4xl text-center mb-8 text-neutral-900">{{ __('booking.reschedule_appointment') }}</h1>

        @if ($error)
            <div class="bg-red-100 border border-red-400 text-red-700 px-6 py-4 rounded-[15px] mb-6">
                <div class="flex items-center gap-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span class="font-medium">{{ $error }}</span>
                </div>
            </div>

            <div class="text-center mt-8">
                <a href="{{ route('booking') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-brand-gold text-white font-semibold rounded-[15px] hover:bg-brand-gold/90 transition-colors shadow-lg">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    {{ __('booking.new_booking') }}
                </a>
            </div>
        @elseif ($isRescheduled)
            <div class="bg-green-100 border border-green-400 text-green-700 px-6 py-4 rounded-[15px] mb-6">
                <div class="flex items-center gap-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <span class="font-medium">{{ __('booking.reschedule_success') }}</span>
                </div>
            </div>

            <div class="bg-brand-beige rounded-[15px] shadow-lg border border-brand-gold/5 p-6 md:p-8 mb-6">
                <h2 class="font-serif font-bold text-lg mb-4 text-neutral-900">{{ __('booking.new_appointment_details') }}</h2>
                @if ($appointment)
                    <div class="space-y-2 text-brand-gold-muted">
                        <p><strong>{{ __('booking.service') }}:</strong> {{ $appointment->service?->name }}</p>
                        <p><strong>{{ __('booking.date') }}:</strong> {{ $appointment->start_time->format('Y. F j.') }}</p>
                        <p><strong>{{ __('booking.time') }}:</strong> {{ $appointment->start_time->format('H:i') }}</p>
                        <p><strong>{{ __('booking.customer') }}:</strong> {{ $appointment->user_name }}</p>
                    </div>
                @endif
            </div>

            <div class="text-center">
                <p class="text-brand-gold-muted mb-4">{{ __('booking.reschedule_email_sent') }}</p>
                <a href="{{ route('booking') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-brand-gold text-white font-semibold rounded-[15px] hover:bg-brand-gold/90 transition-colors shadow-lg">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    {{ __('booking.new_booking') }}
                </a>
            </div>
        @elseif ($isValid && $appointment)
            <div class="bg-brand-beige rounded-[15px] shadow-lg border border-brand-gold/5 p-6 md:p-8 mb-6">
                <h2 class="font-serif font-bold text-lg mb-4 text-neutral-900">{{ __('booking.current_appointment') }}</h2>
                <div class="space-y-2 text-brand-gold-muted">
                    <p><strong>{{ __('booking.service') }}:</strong> {{ $appointment->service?->name }}</p>
                    <p><strong>{{ __('booking.date') }}:</strong> {{ $appointment->start_time->format('Y. F j.') }}</p>
                    <p><strong>{{ __('booking.time') }}:</strong> {{ $appointment->start_time->format('H:i') }}</p>
                    <p><strong>{{ __('booking.customer') }}:</strong> {{ $appointment->user_name }}</p>
                </div>
            </div>

            <div class="bg-brand-beige rounded-[15px] shadow-lg border border-brand-gold/5 p-6 md:p-8">
                <h2 class="font-serif font-bold text-xl mb-6 text-neutral-900">{{ __('booking.select_new_date') }}</h2>

                <div class="grid grid-cols-7 gap-2 mb-6">
                    @foreach (['H', 'K', 'Sze', 'Cs', 'P', 'Szo', 'V'] as $day)
                        <div class="text-center text-sm font-semibold text-brand-gold-muted py-2">{{ $day }}</div>
                    @endforeach

                    @php
                        $firstDay = $this->availableDates->first()?->copy()->startOfMonth() ?? now();
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
                            $isSelected = $selectedDate === $day->format('Y-m-d');
                        @endphp

                        <button
                            wire:click="selectDate('{{ $day->format('Y-m-d') }}')"
                            @disabled(! $isAvailable || ! $isCurrentMonth)
                            class="aspect-square flex items-center justify-center rounded-lg text-sm font-medium transition-all
                                {{ $isSelected
                                    ? 'bg-brand-gold text-white ring-2 ring-brand-gold ring-offset-2'
                                    : ($isAvailable && $isCurrentMonth
                                        ? 'bg-brand-gold/10 text-brand-gold hover:bg-brand-gold/20 cursor-pointer'
                                        : ($isCurrentMonth ? 'bg-brand-beige-light text-brand-gold-muted/50 cursor-not-allowed' : 'bg-transparent')
                                    ) }}"
                        >
                            {{ $day->format('j') }}
                        </button>
                    @endforeach
                </div>

                @if ($selectedDate)
                    <div class="border-t border-brand-gold/10 pt-6">
                        <h3 class="font-serif font-bold text-lg mb-4 text-neutral-900">
                            {{ __('booking.select_new_time') }} - {{ \Carbon\Carbon::parse($selectedDate)->format('Y. F j.') }}
                        </h3>

                        @if ($this->availableSlots->isEmpty())
                            <div class="text-center py-8 text-brand-gold-muted">
                                {{ __('booking.no_slots_available') }}
                            </div>
                        @else
                            <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 gap-3 mb-6">
                                @foreach ($this->availableSlots as $slot)
                                    @php
                                        $timeString = $slot->format('H:i');
                                        $isTimeSelected = $selectedTime === $timeString;
                                    @endphp
                                    <button
                                        wire:click="selectTime('{{ $timeString }}')"
                                        class="py-3 px-4 border-2 rounded-lg transition-all duration-200 font-medium
                                            {{ $isTimeSelected
                                                ? 'border-brand-gold bg-brand-gold text-white'
                                                : 'border-brand-gold/20 hover:border-brand-gold hover:bg-brand-gold/5'
                                            }}"
                                    >
                                        {{ $timeString }}
                                    </button>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endif

                @if ($selectedDate && $selectedTime)
                    <div class="border-t border-brand-gold/10 pt-6">
                        <div class="bg-brand-gold/10 rounded-[15px] p-4 mb-6">
                            <h3 class="font-serif font-bold mb-2 text-neutral-900">{{ __('booking.new_appointment_summary') }}</h3>
                            <p class="text-brand-gold-muted">{{ $appointment->service?->name }}</p>
                            <p class="text-brand-gold-muted">{{ \Carbon\Carbon::parse($selectedDate)->format('Y. F j.') }} {{ $selectedTime }}</p>
                        </div>

                        <button
                            wire:click="confirmReschedule"
                            wire:loading.attr="disabled"
                            class="w-full py-3 bg-brand-gold text-white font-semibold rounded-[15px] hover:bg-brand-gold/90 transition-colors flex items-center justify-center gap-2 shadow-lg"
                        >
                            <span wire:loading.remove wire:target="confirmReschedule">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                {{ __('booking.confirm_reschedule') }}
                            </span>
                            <span wire:loading wire:target="confirmReschedule">
                                <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                {{ __('booking.processing') }}
                            </span>
                        </button>
                    </div>
                @endif
            </div>
        @endif
    </div>
</div>
