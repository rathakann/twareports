<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $allowedLocales = ['en', 'km'];
        $requestedLocale = $request->query('lang');

        if (is_string($requestedLocale) && in_array($requestedLocale, $allowedLocales, true)) {
            $locale = $requestedLocale;
            if ($request->hasSession()) {
                $request->session()->put('locale', $locale);
            }
        } elseif ($request->hasSession() && $request->session()->has('locale')) {
            $locale = $request->session()->get('locale');
        } elseif ($request->hasCookie('twa_locale')) {
            $locale = $request->cookie('twa_locale');
        } else {
            $locale = config('app.locale', 'en');
        }

        if (is_string($locale) && in_array($locale, $allowedLocales, true)) {
            app()->setLocale($locale);
        }

        $response = $next($request);

        if (isset($locale) && is_string($locale) && in_array($locale, $allowedLocales, true)) {
            $response->headers->setCookie(cookie('twa_locale', $locale, 60 * 24 * 365, null, null, false, false));
        }

        return $response;
    }
}
