<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $locales = config('localization.locales', []);
        $defaultLocale = config('localization.default', config('app.locale', 'en'));
        $locale = (string) $request->session()->get('locale', $defaultLocale);

        if (! array_key_exists($locale, $locales)) {
            $locale = $defaultLocale;
        }

        $direction = $locales[$locale]['direction'] ?? 'ltr';

        App::setLocale($locale);

        View::share([
            'currentLocale' => $locale,
            'supportedLocales' => $locales,
            'textDirection' => $direction,
            'isRtl' => $direction === 'rtl',
        ]);

        return $next($request);
    }
}
