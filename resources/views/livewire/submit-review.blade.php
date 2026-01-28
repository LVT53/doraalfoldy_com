<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        <div class="flex justify-end mb-4">
            <x-language-switcher />
        </div>
        <h1 class="text-3xl font-bold text-center mb-8">{{ __('booking.submit_review') }}</h1>

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
                <a href="{{ route('home') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-brand-gold text-white font-semibold rounded-lg hover:bg-brand-gold/90 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    {{ __('booking.back_to_home') }}
                </a>
            </div>
        @elseif ($isSubmitted)
            <div class="bg-green-100 border border-green-400 text-green-700 px-6 py-4 rounded-lg mb-6">
                <div class="flex items-center gap-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <span class="font-medium">{{ __('booking.review_submitted') }}</span>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <h2 class="text-lg font-semibold mb-4">{{ __('booking.review_summary') }}</h2>
                <div class="space-y-4">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">{{ __('booking.your_rating') }}</p>
                        <div class="flex gap-1">
                            @for ($i = 1; $i <= 5; $i++)
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 {{ $i <= $rating ? 'text-yellow-400' : 'text-gray-300' }}" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                </svg>
                            @endfor
                        </div>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 mb-1">{{ __('booking.your_review') }}</p>
                        <p class="text-gray-800 bg-gray-50 p-4 rounded-lg">{{ $content }}</p>
                    </div>
                </div>
            </div>

            <div class="text-center">
                <p class="text-gray-600 mb-4">{{ __('booking.review_approval_notice') }}</p>
                <a href="{{ route('home') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-brand-gold text-white font-semibold rounded-lg hover:bg-brand-gold/90 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    {{ __('booking.back_to_home') }}
                </a>
            </div>
        @elseif ($isValid && $appointment)
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <h2 class="text-lg font-semibold mb-4">{{ __('booking.appointment_details') }}</h2>
                <div class="space-y-2 text-gray-600">
                    <p><strong>{{ __('booking.service') }}:</strong> {{ $appointment->service?->name }}</p>
                    <p><strong>{{ __('booking.date') }}:</strong> {{ $appointment->start_time->format('Y. F j.') }}</p>
                    <p><strong>{{ __('booking.customer') }}:</strong> {{ $appointment->user_name }}</p>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold mb-6">{{ __('booking.rate_your_experience') }}</h2>

                <form wire:submit="submitReview" class="space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-3">{{ __('booking.select_rating') }}</label>
                        <div class="flex gap-2">
                            @for ($i = 1; $i <= 5; $i++)
                                <button
                                    type="button"
                                    wire:click="setRating({{ $i }})"
                                    class="focus:outline-none transition-transform hover:scale-110"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 transition-colors {{ $i <= $rating ? 'text-yellow-400' : 'text-gray-300 hover:text-yellow-200' }}" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                    </svg>
                                </button>
                            @endfor
                        </div>
                        @error('rating')
                            <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="content" class="block text-sm font-medium text-gray-700 mb-2">{{ __('booking.write_review') }}</label>
                        <textarea
                            id="content"
                            wire:model="content"
                            rows="5"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-gold focus:border-transparent resize-none"
                            placeholder="{{ __('booking.review_placeholder') }}"
                        ></textarea>
                        <div class="flex justify-between mt-2">
                            @error('content')
                                <p class="text-red-500 text-sm">{{ $message }}</p>
                            @enderror
                            <p class="text-gray-400 text-sm ml-auto">{{ strlen($content) }}/1000</p>
                        </div>
                    </div>

                    <button
                        type="submit"
                        wire:loading.attr="disabled"
                        class="w-full py-3 bg-brand-gold text-white font-semibold rounded-lg hover:bg-brand-gold/90 transition-colors flex items-center justify-center gap-2"
                    >
                        <span wire:loading.remove wire:target="submitReview">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            {{ __('booking.submit_review_button') }}
                        </span>
                        <span wire:loading wire:target="submitReview">
                            <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            {{ __('booking.processing') }}
                        </span>
                    </button>
                </form>
            </div>
        @endif
    </div>
</div>
