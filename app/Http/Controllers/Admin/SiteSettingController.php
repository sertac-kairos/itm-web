<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AppSetting;
use App\Models\Region;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage;

class SiteSettingController extends Controller
{
    public function edit(): View
    {
        $locales = config('translatable.locales');

        $settings = [
            'qr_enabled' => (bool) AppSetting::get('qr_enabled', true),
            'x_url' => AppSetting::get('x_url'),
            'linkedin_url' => AppSetting::get('linkedin_url'),
            'instagram_url' => AppSetting::get('instagram_url'),
            'email_address' => AppSetting::get('email_address'),
            'app_store_url' => AppSetting::get('app_store_url', 'https://www.apple.com/uk/app-store/'),
            'timeline_image_json' => AppSetting::get('timeline_image_json'),
            'izmir_kalkinma_ajansi_logo' => AppSetting::get('izmir_kalkinma_ajansi_logo'),
            'sanayi_teknoloji_bakanligi_logo' => AppSetting::get('sanayi_teknoloji_bakanligi_logo'),
            'hafiza_izmir_logo' => AppSetting::get('hafiza_izmir_logo'),
            // New parameters with English names
            'time_travel_slider_active' => (bool) AppSetting::get('time_travel_slider_active', true),
            'stories_active' => (bool) AppSetting::get('stories_active', true),
            'nearby_archaeological_sites_active' => (bool) AppSetting::get('nearby_archaeological_sites_active', true),
            'nearby_archaeological_sites_count' => AppSetting::get('nearby_archaeological_sites_count', 10),
            'featured_articles_active' => (bool) AppSetting::get('featured_articles_active', true),
            'memory_izmir_active' => (bool) AppSetting::get('memory_izmir_active', true),
            'model_background_color' => AppSetting::get('model_background_color', '#ffffff'),
            'model_title_color' => AppSetting::get('model_title_color', '#000000'),
            'time_travel_hotspot_image_visible' => (bool) AppSetting::get('time_travel_hotspot_image_visible', true),
            'model_ar_experience_active' => (bool) AppSetting::get('model_ar_experience_active', true),
        ];

        $aboutProject = [];
        foreach ($locales as $locale) {
            $aboutProject[$locale] = AppSetting::get("about_project.$locale");
        }

        $privacyPolicy = [];
        foreach ($locales as $locale) {
            $privacyPolicy[$locale] = AppSetting::get("privacy_policy.$locale");
        }

        // Get all active regions for time travel slider selection
        $regions = Region::with('translations')->active()->ordered()->get();

        // Get selected region IDs for time travel slider
        $selectedRegionIds = [];
        $timeTravelSliderRegions = AppSetting::get('time_travel_slider_regions');
        if ($timeTravelSliderRegions) {
            $selectedRegionIds = explode('|', $timeTravelSliderRegions);
            $selectedRegionIds = array_filter($selectedRegionIds, function ($id) {
                return !empty($id) && is_numeric($id);
            });
            $selectedRegionIds = array_map('intval', $selectedRegionIds);
        }

        return view('admin.settings.edit', compact('settings', 'aboutProject', 'privacyPolicy', 'locales', 'regions', 'selectedRegionIds'));
    }

    public function update(Request $request): RedirectResponse
    {
        $locales = config('translatable.locales');

        $rules = [
            'qr_enabled' => 'nullable|boolean',
            'x_url' => 'nullable|url|max:255',
            'linkedin_url' => 'nullable|url|max:255',
            'instagram_url' => 'nullable|url|max:255',
            'email_address' => 'nullable|email|max:255',
            'app_store_url' => 'nullable|url|max:500',
            'timeline_image_json' => 'nullable|json',
            'izmir_kalkinma_ajansi_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:1024',
            'sanayi_teknoloji_bakanligi_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:1024',
            'hafiza_izmir_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:1024',
            // Validation rules for new parameters with English names
            'time_travel_slider_active' => 'nullable|boolean',
            'stories_active' => 'nullable|boolean',
            'nearby_archaeological_sites_active' => 'nullable|boolean',
            'nearby_archaeological_sites_count' => 'nullable|integer|min:1|max:50',
            'featured_articles_active' => 'nullable|boolean',
            'memory_izmir_active' => 'nullable|boolean',
            'model_background_color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'model_title_color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'time_travel_hotspot_image_visible' => 'nullable|boolean',
            'model_ar_experience_active' => 'nullable|boolean',
            'time_travel_slider_regions' => 'nullable|array',
            'time_travel_slider_regions.*' => 'nullable|integer|exists:regions,id',
        ];
        foreach ($locales as $locale) {
            $rules["about_project.$locale"] = 'nullable|string';
            $rules["privacy_policy.$locale"] = 'nullable|string';
        }

        $validated = $request->validate($rules);

        AppSetting::set('qr_enabled', $request->boolean('qr_enabled'));
        AppSetting::set('x_url', $validated['x_url'] ?? null);
        AppSetting::set('linkedin_url', $validated['linkedin_url'] ?? null);
        AppSetting::set('instagram_url', $validated['instagram_url'] ?? null);
        AppSetting::set('email_address', $validated['email_address'] ?? null);
        AppSetting::set('app_store_url', $validated['app_store_url'] ?? 'https://www.apple.com/uk/app-store/');
        AppSetting::set('timeline_image_json', $validated['timeline_image_json'] ?? null);

        // Handle logo uploads
        $logoFields = [
            'izmir_kalkinma_ajansi_logo',
            'sanayi_teknoloji_bakanligi_logo',
            'hafiza_izmir_logo'
        ];

        foreach ($logoFields as $field) {
            if ($request->hasFile($field)) {
                // Delete old logo if exists
                $oldLogo = AppSetting::get($field);
                if ($oldLogo && Storage::disk('public')->exists($oldLogo)) {
                    Storage::disk('public')->delete($oldLogo);
                }

                // Store new logo
                $logoPath = $request->file($field)->store('logos', 'public');
                AppSetting::set($field, $logoPath);
            }
        }

        // Save new parameters with English names
        AppSetting::set('time_travel_slider_active', $request->boolean('time_travel_slider_active'));

        // Save time travel slider regions (IDs separated by |)
        $regionIds = $request->input('time_travel_slider_regions', []);
        if (is_array($regionIds) && !empty($regionIds)) {
            $regionIds = array_filter($regionIds, function ($id) {
                return !empty($id) && is_numeric($id);
            });
            AppSetting::set('time_travel_slider_regions', implode('|', $regionIds));
        } else {
            AppSetting::set('time_travel_slider_regions', null);
        }

        AppSetting::set('stories_active', $request->boolean('stories_active'));
        AppSetting::set('nearby_archaeological_sites_active', $request->boolean('nearby_archaeological_sites_active'));
        AppSetting::set('nearby_archaeological_sites_count', $validated['nearby_archaeological_sites_count'] ?? 10);
        AppSetting::set('featured_articles_active', $request->boolean('featured_articles_active'));
        AppSetting::set('memory_izmir_active', $request->boolean('memory_izmir_active'));
        AppSetting::set('model_background_color', $validated['model_background_color'] ?? '#ffffff');
        AppSetting::set('model_title_color', $validated['model_title_color'] ?? '#000000');
        AppSetting::set('time_travel_hotspot_image_visible', $request->boolean('time_travel_hotspot_image_visible'));
        AppSetting::set('model_ar_experience_active', $request->boolean('model_ar_experience_active'));

        foreach ($locales as $locale) {
            AppSetting::set("about_project.$locale", data_get($validated, "about_project.$locale"));
            AppSetting::set("privacy_policy.$locale", data_get($validated, "privacy_policy.$locale"));
        }

        return redirect()->route('admin.settings.edit')->with('success', 'Ayarlar kaydedildi.');
    }
}


