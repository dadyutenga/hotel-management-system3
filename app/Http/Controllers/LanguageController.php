<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class LanguageController extends Controller
{
    /**
     * Supported locales
     */
    protected array $supportedLocales = ['en', 'sw'];

    /**
     * Switch the application language
     *
     * @return RedirectResponse
     */
    public function switch(Request $request, string $locale)
    {
        // Validate the locale
        if (! in_array($locale, $this->supportedLocales)) {
            $locale = 'en';
        }

        // Store in session
        session(['locale' => $locale]);

        // Set the application locale
        App::setLocale($locale);

        // Redirect back to the previous page
        return redirect()->back()->with('success', $locale === 'sw'
            ? __('general.messages.language_switched_swahili')
            : __('general.messages.language_switched_english'));
    }

    /**
     * Get available locales
     */
    public function getLocales(): array
    {
        return [
            'en' => 'English',
            'sw' => 'Kiswahili',
        ];
    }
}
