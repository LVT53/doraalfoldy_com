<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\BarionService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class BarionController extends Controller
{
    public function __construct(private BarionService $barionService) {}

    public function callback(Request $request): Response
    {
        $paymentId = $request->input('PaymentId');

        if (empty($paymentId)) {
            return response('PaymentId is required', 400);
        }

        $success = $this->barionService->handleCallback($paymentId);

        if (! $success) {
            return response('Failed to process callback', 500);
        }

        return response('OK', 200);
    }
}
