<?php

use App\Http\Controllers\BookingController;
use App\Livewire\BookingWizard;
use App\Livewire\CancelBooking;
use App\Livewire\RescheduleBooking;
use App\Livewire\SubmitReview;
use Illuminate\Support\Facades\Route;

Route::middleware('booking')->group(function () {
    Route::get('/booking', BookingWizard::class)->name('booking');
    Route::get('/booking/{token}/cancel', CancelBooking::class)->name('booking.cancel');
    Route::get('/booking/{token}/reschedule', RescheduleBooking::class)->name('booking.reschedule');
    Route::get('/booking/{token}/review', SubmitReview::class)->name('booking.review');
    Route::get('/booking/payment/success', [BookingController::class, 'paymentSuccess'])->name('booking.payment.success');
    Route::get('/booking/payment/failed', [BookingController::class, 'paymentFailed'])->name('booking.payment.failed');
    Route::get('/booking/payment/status/{appointment}', [BookingController::class, 'paymentStatus'])->name('booking.payment.status');
});

Route::get('/', function () {
    return view('pages.home');
})->name('home');

Route::get('/szempilla', function () {
    return view('pages.szempilla');
})->name('szempilla');

Route::get('/smink', function () {
    return view('pages.smink');
})->name('smink');

Route::get('/szemoldok', function () {
    return view('pages.szemoldok');
})->name('szemoldok');

Route::get('/smink-tanacsadas', function () {
    return view('pages.smink-tanacsadas');
})->name('smink-tanacsadas');

Route::get('/galeria', function () {
    return view('pages.galeria');
})->name('galeria');

Route::get('/szempilla-galeria', function () {
    return view('pages.szempilla-galeria');
})->name('szempilla-galeria');

Route::get('/smink-galleria', function () {
    return view('pages.smink-galleria');
})->name('smink-galleria');

Route::get('/szemoldok-galleria', function () {
    return view('pages.szemoldok-galleria');
})->name('szemoldok-galleria');

Route::get('/gdpr', function () {
    return view('pages.gdpr');
})->name('gdpr');

Route::get('/aszf', function () {
    return view('pages.aszf');
})->name('aszf');
