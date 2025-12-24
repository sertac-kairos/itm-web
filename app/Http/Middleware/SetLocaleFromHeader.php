<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocaleFromHeader
{
    /**
     * Handle an incoming request and set locale from Accept-Language header.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Get Accept-Language header
        $acceptLanguage = $request->header('Accept-Language', config('translatable.fallback_locale', 'tr'));
        
        // Parse Accept-Language header (e.g., "tr,en;q=0.9,en-US;q=0.8")
        // Extract first locale
        if (str_contains($acceptLanguage, ',')) {
            $acceptLanguage = explode(',', $acceptLanguage)[0];
        }
        
        // Remove quality values (e.g., ";q=0.9")
        if (str_contains($acceptLanguage, ';')) {
            $acceptLanguage = explode(';', $acceptLanguage)[0];
        }
        
        // Clean and normalize
        $locale = trim(strtolower($acceptLanguage));
        
        // Validate against supported locales
        $supportedLocales = config('translatable.locales', ['tr', 'en']);
        if (!in_array($locale, $supportedLocales)) {
            $locale = config('translatable.fallback_locale', 'tr');
        }
        
        // Set application locale
        app()->setLocale($locale);
        
        // Log for debugging
        \Log::info('SetLocaleFromHeader middleware', [
            'accept_language_header' => $acceptLanguage,
            'parsed_locale' => $locale,
            'app_locale' => app()->getLocale()
        ]);
        
        return $next($request);
    }
}

