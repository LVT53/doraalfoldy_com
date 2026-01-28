@extends('layouts.page')

@section('title', 'Fizetés feldolgozása - Alföldy Dóra')

@section('page')
<section class="section-padding bg-brand-light">
    <div class="container mx-auto px-4">
        <div class="max-w-2xl mx-auto text-center">
            <div class="mb-8">
                <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-blue-100 mb-4" id="loading-spinner">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-blue-600 animate-spin" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                </div>
                <h1 class="text-3xl font-bold text-brand-dark mb-4">Fizetés feldolgozása</h1>
                <p class="text-lg text-gray-600">Kérjük várjon, amíg feldolgozzuk a fizetést...</p>
            </div>

            @if($appointment ?? false)
            <div class="bg-white rounded-lg shadow-md p-6 mb-8 text-left">
                <h2 class="text-xl font-semibold text-brand-dark mb-4">Foglalás részletei</h2>
                <div class="space-y-3">
                    <div class="flex justify-between border-b pb-2">
                        <span class="text-gray-600">Szolgáltatás:</span>
                        <span class="font-medium">{{ $appointment->service->name }}</span>
                    </div>
                    <div class="flex justify-between border-b pb-2">
                        <span class="text-gray-600">Időpont:</span>
                        <span class="font-medium">{{ $appointment->start_time->format('Y. m. d. H:i') }}</span>
                    </div>
                    <div class="flex justify-between border-b pb-2">
                        <span class="text-gray-600">Név:</span>
                        <span class="font-medium">{{ $appointment->user_name }}</span>
                    </div>
                </div>
            </div>
            @endif

            <div id="status-message" class="hidden">
                <p class="text-gray-600 mb-4">Az oldal automatikusan frissül...</p>
            </div>
        </div>
    </div>
</section>

@push('scripts')
<script>
    (function() {
        const appointmentId = {{ $appointment->id ?? 'null' }};
        let checkCount = 0;
        const maxChecks = 30;

        function checkStatus() {
            if (!appointmentId || checkCount >= maxChecks) {
                window.location.reload();
                return;
            }

            checkCount++;

            fetch('{{ route("booking.payment.status", ["appointment" => $appointment->id ?? 0]) }}')
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'completed') {
                        window.location.href = '{{ route("booking.payment.success") }}';
                    } else if (data.status === 'failed') {
                        window.location.href = '{{ route("booking.payment.failed") }}';
                    } else {
                        setTimeout(checkStatus, 2000);
                    }
                })
                .catch(() => {
                    setTimeout(checkStatus, 2000);
                });
        }

        setTimeout(checkStatus, 2000);
    })();
</script>
@endpush
@endsection
