<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        <h1 class="text-3xl font-bold text-center mb-8">{{ __('booking.cancel_appointment') }}</h1>

        @if ($error)
            <div class="bg-red-100 border border-red-400 text-red-700 px-6 py-4 rounded-lg mb-6">
                <div class="flex items-center gap-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span class="font-medium">{{ $error }}</span>
                </div>
            </div>

            <div class="text-center mt-8">
                <a href="{{ route('booking') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-brand-gold text-white font-semibold rounded-lg hover:bg-brand-gold/90 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    {{ __('booking.new_booking') }}
                </a>
            </div>
        @elseif ($isCancelled)
            <div class="bg-green-100 border border-green-400 text-green-700 px-6 py-4 rounded-lg mb-6">
                <div class="flex items-center gap-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <span class="font-medium">{{ __('booking.cancel_success') }}</span>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <h2 class="text-lg font-semibold mb-4">{{ __('booking.cancelled_appointment_details') }}</h2>
                @if ($appointment)
                    <div class="space-y-2 text-gray-600">
                        <p><strong>{{ __('booking.service') }}:</strong> {{ $appointment->service?->name }}</p>
                        <p><strong>{{ __('booking.date') }}:</strong> {{ $appointment->start_time->format('Y. F j.') }}</p>
                        <p><strong>{{ __('booking.time') }}:</strong> {{ $appointment->start_time->format('H:i') }}</p>
                        <p><strong>{{ __('booking.customer') }}:</strong> {{ $appointment->user_name }}</p>
                    </div>
                @endif
            </div>

            <div class="text-center">
                <p class="text-gray-600 mb-4">{{ __('booking.cancellation_email_sent') }}</p>
                <a href="{{ route('booking') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-brand-gold text-white font-semibold rounded-lg hover:bg-brand-gold/90 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    {{ __('booking.new_booking') }}
                </a>
            </div>
        @elseif ($isValid && $appointment)
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <h2 class="text-lg font-semibold mb-4">{{ __('booking.appointment_details') }}</h2>
                <div class="space-y-2 text-gray-600">
                    <p><strong>{{ __('booking.service') }}:</strong> {{ $appointment->service?->name }}</p>
                    <p><strong>{{ __('booking.date') }}:</strong> {{ $appointment->start_time->format('Y. F j.') }}</p>
                    <p><strong>{{ __('booking.time') }}:</strong> {{ $appointment->start_time->format('H:i') }}</p>
                    <p><strong>{{ __('booking.customer') }}:</strong> {{ $appointment->user_name }}</p>
                    <p><strong>{{ __('booking.email') }}:</strong> {{ $appointment->user_email }}</p>
                </div>
            </div>

            @if ($canCancel)
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6 mb-6">
                    <div class="flex items-start gap-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-yellow-600 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        <div>
                            <h3 class="font-semibold text-yellow-800 mb-1">{{ __('booking.cancellation_warning') }}</h3>
                            <p class="text-yellow-700 text-sm">{{ __('booking.cancellation_irreversible') }}</p>
                        </div>
                    </div>
                </div>

                <button
                    wire:click="confirmCancel"
                    wire:loading.attr="disabled"
                    class="w-full py-3 bg-red-600 text-white font-semibold rounded-lg hover:bg-red-700 transition-colors flex items-center justify-center gap-2"
                >
                    <span wire:loading.remove wire:target="confirmCancel">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        {{ __('booking.confirm_cancel') }}
                    </span>
                    <span wire:loading wire:target="confirmCancel">
                        <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        {{ __('booking.processing') }}
                    </span>
                </button>
            @else
                <div class="bg-red-50 border border-red-200 rounded-lg p-6">
                    <div class="flex items-start gap-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-red-600 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <div>
                            <h3 class="font-semibold text-red-800 mb-1">{{ __('booking.cancellation_window_closed') }}</h3>
                            <p class="text-red-700 text-sm">{{ __('booking.cancellation_window_explanation') }}</p>
                        </div>
                    </div>
                </div>
            @endif
        @endif
    </div>
</div>
