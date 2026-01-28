<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $locale = 'hu';

        if ($request->has('lang')) {
            $requestedLocale = $request->get('lang');
            if (in_array($requestedLocale, ['hu', 'en'])) {
                $locale = $requestedLocale;
                session(['locale' => $locale]);
            }
        } elseif (session()->has('locale')) {
            $locale = session('locale');
        }

        App::setLocale($locale);

        return $next($request);
    }
}
