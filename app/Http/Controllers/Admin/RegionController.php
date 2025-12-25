<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Region;
use App\Services\DeepLTranslationService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage;

class RegionController extends Controller
{
    protected DeepLTranslationService $translationService;

    public function __construct(DeepLTranslationService $translationService)
    {
        $this->translationService = $translationService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $query = Region::with('translations');

        // Sorting
        $sortField = $request->get('sort', 'sort_order');
        $sortDirection = $request->get('direction', 'asc');
        
        if ($sortField === 'name') {
            // Sort by translated name
            $locale = app()->getLocale();
            $query->leftJoin('region_translations', function($join) use ($locale) {
                $join->on('regions.id', '=', 'region_translations.region_id')
                     ->where('region_translations.locale', '=', $locale);
            })
            ->orderBy('region_translations.name', $sortDirection)
            ->select('regions.*');
        } elseif (in_array($sortField, ['id', 'sort_order', 'is_active', 'created_at', 'updated_at', 'latitude', 'longitude', 'color_code'])) {
            $query->orderBy($sortField, $sortDirection);
        } else {
            $query->ordered();
        }

        $regions = $query->paginate(15)->withQueryString();

        return view('admin.regions.index', compact('regions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $locales = config('translatable.locales');
        return view('admin.regions.create', compact('locales'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'color_code' => 'required|string|size:7|regex:/^#[0-9a-fA-F]{6}$/',
            'sort_order' => 'required|integer|min:0',
            'main_image' => 'nullable|image|max:2048',
            'hotspot_image' => 'nullable|json',
            'is_active' => 'boolean',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
        ]);

        // Translations validation - at least one language must have a name
        $hasAtLeastOneName = false;
        $translationRules = [];
        
        foreach (config('translatable.locales') as $locale) {
            if ($request->filled("{$locale}.name")) {
                $hasAtLeastOneName = true;
                $translationRules["{$locale}.name"] = 'required|string|max:255';
                $translationRules["{$locale}.subtitle"] = 'nullable|string|max:255';
                $translationRules["{$locale}.description"] = 'nullable|string';
                $translationRules["{$locale}.audio_guide"] = 'nullable|file|mimes:mp3|max:10240'; // Max 10MB
            }
        }
        
        if (!$hasAtLeastOneName) {
            return redirect()->back()
                ->withErrors(['translations' => 'En az bir dilde bölge adı girilmelidir.'])
                ->withInput();
        }
        
        if (!empty($translationRules)) {
            $request->validate($translationRules);
        }

        $region = new Region();
        $region->color_code = $request->color_code;
        $region->sort_order = $request->sort_order;
        $region->is_active = $request->boolean('is_active', false);
        $region->latitude = $request->filled('latitude') ? $request->latitude : null;
        $region->longitude = $request->filled('longitude') ? $request->longitude : null;
        $region->hotspot_image = $request->filled('hotspot_image') ? json_decode($request->hotspot_image, true) : null;

        // Handle image upload
        if ($request->hasFile('main_image')) {
            $region->main_image = $request->file('main_image')->store('regions', 'public');
        }

        $region->save();

        // Save translations - only for languages with actual content
        foreach (config('translatable.locales') as $locale) {
            if ($request->filled("{$locale}.name")) {
                $region->translateOrNew($locale)->name = $request->input("{$locale}.name");
                $region->translateOrNew($locale)->subtitle = $request->input("{$locale}.subtitle");
                $region->translateOrNew($locale)->description = $request->input("{$locale}.description");
                
                // Handle audio guide upload
                if ($request->hasFile("{$locale}.audio_guide")) {
                    $region->translateOrNew($locale)->audio_guide_path = $request->file("{$locale}.audio_guide")->store('regions/audio', 'public');
                }
            }
        }

        $region->save();

        return redirect()
            ->route('admin.regions.index')
            ->with('success', 'Bölge başarıyla oluşturuldu.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Region $region): View
    {
        $region->load(['translations', 'subRegions.translations']);
        
        return view('admin.regions.show', compact('region'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, Region $region): View
    {
        $region->load('translations');
        $locales = config('translatable.locales');
        
        // Store referer URL for redirect after update
        $referer = $request->header('referer');
        $returnUrl = $referer && str_contains($referer, route('admin.regions.index')) 
            ? $referer 
            : route('admin.regions.index', $request->query());
        
        return view('admin.regions.edit', compact('region', 'locales', 'returnUrl'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Region $region): RedirectResponse
    {
        $request->validate([
            'color_code' => 'required|string|size:7|regex:/^#[0-9a-fA-F]{6}$/',
            'sort_order' => 'required|integer|min:0',
            'main_image' => 'nullable|image|max:2048',
            'hotspot_image' => 'nullable|json',
            'is_active' => 'boolean',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
        ]);

        // Translations validation - at least one language must have a name
        $hasAtLeastOneName = false;
        $translationRules = [];
        
        foreach (config('translatable.locales') as $locale) {
            if ($request->filled("{$locale}.name")) {
                $hasAtLeastOneName = true;
                $translationRules["{$locale}.name"] = 'required|string|max:255';
                $translationRules["{$locale}.subtitle"] = 'nullable|string|max:255';
                $translationRules["{$locale}.description"] = 'nullable|string';
                $translationRules["{$locale}.audio_guide"] = 'nullable|file|mimes:mp3|max:10240'; // Max 10MB
            }
        }
        
        if (!$hasAtLeastOneName) {
            return redirect()->back()
                ->withErrors(['translations' => 'En az bir dilde bölge adı girilmelidir.'])
                ->withInput();
        }
        
        if (!empty($translationRules)) {
            $request->validate($translationRules);
        }

        $region->color_code = $request->color_code;
        $region->sort_order = $request->sort_order;
        $region->is_active = $request->boolean('is_active', false);
        $region->latitude = $request->filled('latitude') ? $request->latitude : null;
        $region->longitude = $request->filled('longitude') ? $request->longitude : null;
        $region->hotspot_image = $request->filled('hotspot_image') ? json_decode($request->hotspot_image, true) : null;

        // Handle image upload
        if ($request->hasFile('main_image')) {
            // Delete old image
            if ($region->main_image) {
                Storage::disk('public')->delete($region->main_image);
            }
            $region->main_image = $request->file('main_image')->store('regions', 'public');
        }

        $region->save();

        // Update translations - only for languages with actual content
        foreach (config('translatable.locales') as $locale) {
            if ($request->filled("{$locale}.name")) {
                $region->translateOrNew($locale)->name = $request->input("{$locale}.name");
                $region->translateOrNew($locale)->subtitle = $request->input("{$locale}.subtitle");
                $region->translateOrNew($locale)->description = $request->input("{$locale}.description");
                
                // Handle audio guide upload
                if ($request->hasFile("{$locale}.audio_guide")) {
                    // Delete old audio file
                    $translation = $region->translate($locale, false);
                    if ($translation && $translation->audio_guide_path) {
                        Storage::disk('public')->delete($translation->audio_guide_path);
                    }
                    $region->translateOrNew($locale)->audio_guide_path = $request->file("{$locale}.audio_guide")->store('regions/audio', 'public');
                }
            }
        }

        $region->save();

        // Redirect to return URL if provided, otherwise to index
        $returnUrl = $request->input('return_url', route('admin.regions.index', $request->query()));
        return redirect($returnUrl)
            ->with('success', 'Bölge başarıyla güncellendi.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Region $region): RedirectResponse
    {
        // Delete image if exists
        if ($region->main_image) {
            Storage::disk('public')->delete($region->main_image);
        }

        $region->delete();

        return redirect()
            ->route('admin.regions.index')
            ->with('success', 'Bölge başarıyla silindi.');
    }

    /**
     * Move region up in sort order
     */
    public function moveUp(Region $region): RedirectResponse
    {
        // Find the previous item (lower sort_order)
        $previous = Region::where('sort_order', '<', $region->sort_order)
            ->orderBy('sort_order', 'desc')
            ->first();
        
        if ($previous) {
            // Swap sort orders
            $tempOrder = $region->sort_order;
            $region->sort_order = $previous->sort_order;
            $previous->sort_order = $tempOrder;
            
            $region->save();
            $previous->save();
            
            return redirect()->back()->with('success', 'Sıralama güncellendi.');
        }
        
        return redirect()->back()->with('error', 'Bölge zaten en üstte.');
    }

    /**
     * Move region down in sort order
     */
    public function moveDown(Region $region): RedirectResponse
    {
        // Find the next item (higher sort_order)
        $next = Region::where('sort_order', '>', $region->sort_order)
            ->orderBy('sort_order', 'asc')
            ->first();
        
        if ($next) {
            // Swap sort orders
            $tempOrder = $region->sort_order;
            $region->sort_order = $next->sort_order;
            $next->sort_order = $tempOrder;
            
            $region->save();
            $next->save();
            
            return redirect()->back()->with('success', 'Sıralama güncellendi.');
        }
        
        return redirect()->back()->with('error', 'Bölge zaten en altta.');
    }

    /**
     * Translate text using DeepL
     */
    public function translate(Request $request)
    {
        $request->validate([
            'text' => 'required|string',
            'from' => 'required|string',
            'to' => 'required|string',
        ]);

        try {
            $translatedText = null;
            
            if ($request->from === 'tr' && $request->to === 'en') {
                $translatedText = $this->translationService->translateToEnglish($request->text);
            } elseif ($request->from === 'en' && $request->to === 'tr') {
                $translatedText = $this->translationService->translateToTurkish($request->text);
            } else {
                $translatedText = $this->translationService->autoTranslate($request->text, $request->from);
            }

            if ($translatedText) {
                return response()->json([
                    'success' => true,
                    'translated_text' => $translatedText
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Çeviri başarısız oldu'
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Çeviri servisi kullanılamıyor: ' . $e->getMessage()
            ], 500);
        }
    }
}
