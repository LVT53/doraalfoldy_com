<?php

declare(strict_types=1);

use App\Http\Controllers\BarionController;
use Illuminate\Support\Facades\Route;

Route::post('/barion/callback', [BarionController::class, 'callback'])->name('barion.callback');
