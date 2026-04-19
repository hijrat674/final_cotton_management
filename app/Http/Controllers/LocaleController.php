<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;

class LocaleController extends Controller
{
    public function update(string $locale): RedirectResponse
    {
        $locales = array_keys(config('localization.locales', []));

        abort_unless(in_array($locale, $locales, true), 404);

        session()->put('locale', $locale);

        return back();
    }
}
