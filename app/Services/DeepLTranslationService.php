<?php

namespace App\Services;

use DeepL\Translator;
use Illuminate\Support\Facades\Log;

class DeepLTranslationService
{
    private Translator $translator;

    public function __construct()
    {
        $apiKey = config('services.deepl.api_key');
        
        if (!$apiKey) {
            throw new \Exception('DeepL API key not configured');
        }

        $this->translator = new Translator($apiKey);
    }

    /**
     * Translate text from Turkish to English
     */
    public function translateToEnglish(string $text): ?string
    {
        try {
            if (empty(trim($text))) {
                return null;
            }

            $result = $this->translator->translateText(
                $text,
                'tr',
                'en-US'
            );

            return $result->text;
        } catch (\Exception $e) {
            Log::error('DeepL Translation Error: ' . $e->getMessage(), [
                'text' => $text,
                'from' => 'tr',
                'to' => 'en-US'
            ]);
            
            return null;
        }
    }

    /**
     * Translate text from English to Turkish
     */
    public function translateToTurkish(string $text): ?string
    {
        try {
            if (empty(trim($text))) {
                return null;
            }

            $result = $this->translator->translateText(
                $text,
                'en-US',
                'tr'
            );

            return $result->text;
        } catch (\Exception $e) {
            Log::error('DeepL Translation Error: ' . $e->getMessage(), [
                'text' => $text,
                'from' => 'en-US',
                'to' => 'tr'
            ]);
            
            return null;
        }
    }

    /**
     * Auto-translate content based on the primary language
     * If Turkish content is provided, translate to English
     * If English content is provided, translate to Turkish
     */
    public function autoTranslate(string $text, string $primaryLanguage = 'tr'): ?string
    {
        if ($primaryLanguage === 'tr') {
            return $this->translateToEnglish($text);
        } else {
            return $this->translateToTurkish($text);
        }
    }

    /**
     * Check if DeepL service is available
     */
    public function isAvailable(): bool
    {
        try {
            $this->translator->getUsage();
            return true;
        } catch (\Exception $e) {
            Log::error('DeepL Service Unavailable: ' . $e->getMessage());
            return false;
        }
    }
}
