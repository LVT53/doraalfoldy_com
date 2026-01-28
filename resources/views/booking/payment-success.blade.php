@extends('layouts.page')

@section('title', 'Sikeres fizetés - Alföldy Dóra')

@section('page')
<section class="section-padding bg-brand-light">
    <div class="container mx-auto px-4">
        <div class="max-w-2xl mx-auto text-center">
            <div class="mb-8">
                <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-green-100 mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <h1 class="text-3xl font-bold text-brand-dark mb-4">Sikeres foglalás!</h1>
                <p class="text-lg text-gray-600">Köszönjük a foglalást. A visszaigazoló e-mailt elküldtük a megadott címre.</p>
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
                    <div class="flex justify-between border-b pb-2">
                        <span class="text-gray-600">E-mail:</span>
                        <span class="font-medium">{{ $appointment->user_email }}</span>
                    </div>
                    <div class="flex justify-between pt-2">
                        <span class="text-gray-600">Fizetett összeg:</span>
                        <span class="font-semibold text-green-600">{{ number_format($transaction->amount ?? 0, 0, ',', ' ') }} Ft</span>
                    </div>
                </div>
            </div>
            @endif

            <div class="space-y-4">
                <a href="{{ route('home') }}" class="inline-block bg-brand-gold text-white px-8 py-3 rounded-lg hover:bg-brand-gold/90 transition-colors">
                    Vissza a főoldalra
                </a>
            </div>

            <p class="mt-8 text-sm text-gray-500">
                Ha bármilyen kérdése van, kérjük vegye fel velünk a kapcsolatot.
            </p>
        </div>
    </div>
</section>
@endsection
