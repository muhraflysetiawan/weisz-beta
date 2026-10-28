<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
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
        $locale = null;

        // 1. Check session first (active switch in session)
        if (session()->has('locale')) {
            $locale = session('locale');
        }

        // 2. If logged in and no session override, check user profile preference
        if (! $locale && Auth::check() && ! empty(Auth::user()->locale)) {
            $locale = Auth::user()->locale;
        }

        // 3. Check cookie
        if (! $locale && $request->hasCookie('weisz_locale')) {
            $locale = $request->cookie('weisz_locale');
        }

        // 4. Default to Indonesian ('id')
        if (! $locale) {
            $locale = config('app.locale', 'id');
        }

        // 5. Strict validation: only 'id' or 'en'
        if (! in_array($locale, ['id', 'en'], true)) {
            $locale = 'id';
        }

        App::setLocale($locale);

        if (session('locale') !== $locale) {
            session(['locale' => $locale]);
        }

        return $next($request);
    }
}
