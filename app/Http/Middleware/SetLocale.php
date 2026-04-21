<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Priority: URL segment locale (e.g. /ca/products)
        $segment = $request->segment(1);
        if (in_array($segment, ['ca', 'es', 'en'])) {
            App::setLocale($segment);
            session(['locale' => $segment]);
            return $next($request);
        }

        // 2. Session locale
        if (session()->has('locale')) {
            App::setLocale(session('locale'));
            return $next($request);
        }

        // 3. Authenticated user preference
        if (Auth::check() && Auth::user()->locale) {
            App::setLocale(Auth::user()->locale);
            return $next($request);
        }

        // 4. Browser accept-language
        $browserLocale = substr($request->getPreferredLanguage(['ca', 'es', 'en']), 0, 2);
        if (in_array($browserLocale, ['ca', 'es', 'en'])) {
            App::setLocale($browserLocale);
        } else {
            App::setLocale(config('app.locale', 'ca'));
        }

        return $next($request);
    }
}
