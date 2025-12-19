<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\AppSetting;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class SiteSettingController extends Controller
{
    /**
     * Get all site settings for mobile app
     */
    public function index(Request $request): JsonResponse
    {
        // Get locale from request header or default to app locale
        $locale = $request->header('Accept-Language', app()->getLocale());
        
        // Set application locale for this request
        app()->setLocale($locale);

        // Get all settings
        $settings = [
            'qr_enabled' => (bool) AppSetting::get('qr_enabled', true),
            'x_url' => AppSetting::get('x_url'),
            'linkedin_url' => AppSetting::get('linkedin_url'),
            'instagram_url' => AppSetting::get('instagram_url'),
            'email_address' => AppSetting::get('email_address'),
            // New parameters with English names
            'time_travel_slider_active' => (bool) AppSetting::get('time_travel_slider_active', true),
            'time_travel_slider_regions' => $this->getTimeTravelSliderRegions(),
            'stories_active' => (bool) AppSetting::get('stories_active', true),
            'nearby_archaeological_sites_active' => (bool) AppSetting::get('nearby_archaeological_sites_active', true),
            'nearby_archaeological_sites_count' => (int) AppSetting::get('nearby_archaeological_sites_count', 10),
            'featured_articles_active' => (bool) AppSetting::get('featured_articles_active', true),
            'memory_izmir_active' => (bool) AppSetting::get('memory_izmir_active', true),
            'model_background_color' => AppSetting::get('model_background_color', '#ffffff'),
            'model_title_color' => AppSetting::get('model_title_color', '#000000'),
            'time_travel_hotspot_image_visible' => (bool) AppSetting::get('time_travel_hotspot_image_visible', true),
        ];

        // Get timeline images JSON
        $timelineImageJson = AppSetting::get('timeline_image_json');
        $timelineImages = null;
        if ($timelineImageJson) {
            $decoded = json_decode($timelineImageJson, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $timelineImages = $decoded;
            }
        }

        // Get about project content for current locale
        $aboutProject = AppSetting::get("about_project.$locale");

        // Get logo URLs
        $logos = [
            'izmir_kalkinma_ajansi_logo' => $this->getLogoUrl(AppSetting::get('izmir_kalkinma_ajansi_logo')),
            'sanayi_teknoloji_bakanligi_logo' => $this->getLogoUrl(AppSetting::get('sanayi_teknoloji_bakanligi_logo')),
            'hafiza_izmir_logo' => $this->getLogoUrl(AppSetting::get('hafiza_izmir_logo')),
        ];

        return response()->json([
            'success' => true,
            'data' => [
                'settings' => $settings,
                'about_project' => $aboutProject,
                'logos' => $logos,
                'timeline_image_json' => $timelineImages,
                'locale' => $locale,
                'available_locales' => config('translatable.locales'),
            ]
        ]);
    }


    /**
     * Helper method to get full URL for logo
     */
    private function getLogoUrl(?string $logoPath): ?string
    {
        if (!$logoPath) {
            return null;
        }

        if (Storage::disk('public')->exists($logoPath)) {
            return Storage::url($logoPath);
        }

        return null;
    }

    /**
     * Get time travel slider regions as array of IDs
     */
    private function getTimeTravelSliderRegions(): array
    {
        $regionsString = AppSetting::get('time_travel_slider_regions');
        if (!$regionsString) {
            return [];
        }

        $regionIds = explode('|', $regionsString);
        return array_filter(array_map('intval', $regionIds), function($id) {
            return $id > 0;
        });
    }
}