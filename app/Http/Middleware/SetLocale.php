<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

/**
 * Resolves the active UI locale for each request, in priority order:
 * ?lang=xx (persisted) → session → authenticated user preference → config.
 * Only locales listed in config('barta.locales') are honoured.
 */
class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $available = barta_locales();

        $locale = $request->query('lang')
            ?? Session::get('locale')
            ?? $request->user()?->locale
            ?? config('app.locale');

        if (! in_array($locale, $available, true)) {
            $locale = config('app.locale');
        }

        App::setLocale($locale);
        Session::put('locale', $locale);

        return $next($request);
    }
}
