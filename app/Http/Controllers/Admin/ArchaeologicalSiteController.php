<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ArchaeologicalSite;
use App\Models\SubRegion;
use App\Models\Region;
use App\Services\DeepLTranslationService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage;

class ArchaeologicalSiteController extends Controller
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
        $query = ArchaeologicalSite::with(['translations', 'subRegion.translations', 'subRegion.region.translations']);

        // Filter by region if provided
        if ($request->filled('region_id')) {
            $query->whereHas('subRegion', function ($q) use ($request) {
                $q->where('region_id', $request->region_id);
            });
        }

        // Filter by sub-region if provided
        if ($request->filled('sub_region_id')) {
            $query->where('sub_region_id', $request->sub_region_id);
        }

        // Filter by status
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('translations', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $archaeologicalSites = $query->latest()->paginate(15)->withQueryString();
        $regions = Region::with('translations')->active()->ordered()->get();
        $subRegions = SubRegion::with('translations')->active()->ordered()->get();

        return view('admin.archaeological-sites.index', compact('archaeologicalSites', 'regions', 'subRegions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request): View
    {
        $locales = config('translatable.locales');
        $regions = Region::with(['translations', 'activeSubRegions.translations'])->active()->ordered()->get();
        $selectedSubRegionId = $request->get('sub_region_id');
        $selectedRegionId = $request->get('region_id');

        // Prepare region data for JavaScript
        $regionData = $regions->map(function($region) {
            $subRegionsArray = $region->activeSubRegions->map(function($sub) {
                // Try to get name in Turkish first, then fallback to other locales
                $subName = $sub->translate('tr')->name ?? $sub->translate('en')->name ?? $sub->name ?? 'İsimsiz Alt Bölge';
                return [
                    'id' => $sub->id, 
                    'name' => $subName
                ];
            })->toArray();
            
            return [
                'id' => $region->id,
                'color' => $region->color_code,
                'subRegions' => $subRegionsArray
            ];
        })->toArray();

        return view('admin.archaeological-sites.create', compact('locales', 'regions', 'selectedSubRegionId', 'selectedRegionId', 'regionData'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'sub_region_id' => 'required|exists:sub_regions,id',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'image' => 'nullable|image|max:2048',
            'is_nearby_enabled' => 'boolean',
            'is_active' => 'boolean',
        ]);

        // Translations validation - at least one language must have a name
        $hasAtLeastOneName = false;
        $translationRules = [];
        
        foreach (config('translatable.locales') as $locale) {
            if ($request->filled("{$locale}.name")) {
                $hasAtLeastOneName = true;
                $translationRules["{$locale}.name"] = 'required|string|max:255';
                $translationRules["{$locale}.description"] = 'nullable|string';
                $translationRules["{$locale}.audio_guide"] = 'nullable|file|mimes:mp3|max:10240'; // Max 10MB
            }
        }
        
        if (!$hasAtLeastOneName) {
            return redirect()->back()
                ->withErrors(['translations' => 'En az bir dilde ören yeri adı girilmelidir.'])
                ->withInput();
        }
        
        if (!empty($translationRules)) {
            $request->validate($translationRules);
        }

        $archaeologicalSite = new ArchaeologicalSite();
        $archaeologicalSite->sub_region_id = $request->sub_region_id;
        $archaeologicalSite->latitude = $request->latitude;
        $archaeologicalSite->longitude = $request->longitude;
        $archaeologicalSite->is_nearby_enabled = $request->boolean('is_nearby_enabled', false);
        $archaeologicalSite->is_active = $request->boolean('is_active', false);

        // Handle image upload
        if ($request->hasFile('image')) {
            $archaeologicalSite->image = $request->file('image')->store('archaeological-sites', 'public');
        }

        $archaeologicalSite->save();

        // Save translations - only for languages with actual content
        foreach (config('translatable.locales') as $locale) {
            if ($request->filled("{$locale}.name")) {
                $translation = $archaeologicalSite->translateOrNew($locale);
                $translation->name = $request->input("{$locale}.name");
                $translation->description = $request->input("{$locale}.description");
                
                // Handle audio guide file upload
                if ($request->hasFile("{$locale}.audio_guide")) {
                    $audioFile = $request->file("{$locale}.audio_guide");
                    $audioPath = $audioFile->store("audio-guides/{$locale}", 'public');
                    $translation->audio_guide_path = $audioPath;
                }
                
                $translation->save();
            }
        }

        $archaeologicalSite->save();

        return redirect()
            ->route('admin.archaeological-sites.index', [
                'sub_region_id' => $archaeologicalSite->sub_region_id,
                'region_id' => $archaeologicalSite->subRegion->region_id
            ])
            ->with('success', 'Ören yeri başarıyla oluşturuldu.');
    }

    /**
     * Display the specified resource.
     */
    public function show(ArchaeologicalSite $archaeologicalSite): View
    {
        $archaeologicalSite->load([
            'translations', 
            'subRegion.translations', 
            'subRegion.region.translations',
            'models3d.translations',
            'audioGuides.translations',
            'qrCodes'
        ]);
        
        return view('admin.archaeological-sites.show', compact('archaeologicalSite'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, ArchaeologicalSite $archaeologicalSite): View
    {
        $archaeologicalSite->load(['translations', 'subRegion.region']);
        $locales = config('translatable.locales');
        $regions = Region::with(['translations', 'activeSubRegions.translations'])->active()->ordered()->get();
        
        // Prepare region data for JavaScript
        $regionData = $regions->map(function($region) {
            $subRegionsArray = $region->activeSubRegions->map(function($sub) {
                // Try to get name in Turkish first, then fallback to other locales
                $subName = $sub->translate('tr')->name ?? $sub->translate('en')->name ?? $sub->name ?? 'İsimsiz Alt Bölge';
                return [
                    'id' => $sub->id, 
                    'name' => $subName
                ];
            })->toArray();
            
            return [
                'id' => $region->id,
                'color' => $region->color_code,
                'subRegions' => $subRegionsArray
            ];
        })->toArray();
        
        // Store referer URL for redirect after update
        $referer = $request->header('referer');
        $returnUrl = $referer && str_contains($referer, route('admin.archaeological-sites.index')) 
            ? $referer 
            : route('admin.archaeological-sites.index', $request->query());
        
        return view('admin.archaeological-sites.edit', compact('archaeologicalSite', 'locales', 'regions', 'regionData', 'returnUrl'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ArchaeologicalSite $archaeologicalSite): RedirectResponse
    {
        $request->validate([
            'sub_region_id' => 'required|exists:sub_regions,id',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'image' => 'nullable|image|max:2048',
            'is_nearby_enabled' => 'boolean',
            'is_active' => 'boolean',
        ]);

        // Translations validation - at least one language must have a name
        $hasAtLeastOneName = false;
        $translationRules = [];
        
        foreach (config('translatable.locales') as $locale) {
            if ($request->filled("{$locale}.name")) {
                $hasAtLeastOneName = true;
                $translationRules["{$locale}.name"] = 'required|string|max:255';
                $translationRules["{$locale}.description"] = 'nullable|string';
                $translationRules["{$locale}.audio_guide"] = 'nullable|file|mimes:mp3|max:10240'; // Max 10MB
            }
        }
        
        if (!$hasAtLeastOneName) {
            return redirect()->back()
                ->withErrors(['translations' => 'En az bir dilde ören yeri adı girilmelidir.'])
                ->withInput();
        }
        
        if (!empty($translationRules)) {
            $request->validate($translationRules);
        }

        $archaeologicalSite->sub_region_id = $request->sub_region_id;
        $archaeologicalSite->latitude = $request->latitude;
        $archaeologicalSite->longitude = $request->longitude;
        $archaeologicalSite->is_nearby_enabled = $request->boolean('is_nearby_enabled', false);
        $archaeologicalSite->is_active = $request->boolean('is_active', false);

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image
            if ($archaeologicalSite->image) {
                Storage::disk('public')->delete($archaeologicalSite->image);
            }
            $archaeologicalSite->image = $request->file('image')->store('archaeological-sites', 'public');
        }

        $archaeologicalSite->save();

        // Update translations - only for languages with actual content
        foreach (config('translatable.locales') as $locale) {
            if ($request->filled("{$locale}.name")) {
                $translation = $archaeologicalSite->translateOrNew($locale);
                $translation->name = $request->input("{$locale}.name");
                $translation->description = $request->input("{$locale}.description");
                
                // Handle audio guide file upload
                if ($request->hasFile("{$locale}.audio_guide")) {
                    // Delete old audio file if exists
                    if ($translation->audio_guide_path) {
                        Storage::disk('public')->delete($translation->audio_guide_path);
                    }
                    
                    $audioFile = $request->file("{$locale}.audio_guide");
                    $audioPath = $audioFile->store("audio-guides/{$locale}", 'public');
                    $translation->audio_guide_path = $audioPath;
                }
                
                $translation->save();
            }
        }

        $archaeologicalSite->save();

        // Redirect to return URL if provided, otherwise to index
        $returnUrl = $request->input('return_url', route('admin.archaeological-sites.index', $request->query()));
        return redirect($returnUrl)
            ->with('success', 'Ören yeri başarıyla güncellendi.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ArchaeologicalSite $archaeologicalSite): RedirectResponse
    {
        $subRegionId = $archaeologicalSite->sub_region_id;
        $regionId = $archaeologicalSite->subRegion->region_id;

        // Delete image if exists
        if ($archaeologicalSite->image) {
            Storage::disk('public')->delete($archaeologicalSite->image);
        }

        $archaeologicalSite->delete();

        return redirect()
            ->route('admin.archaeological-sites.index', [
                'sub_region_id' => $subRegionId,
                'region_id' => $regionId
            ])
            ->with('success', 'Ören yeri başarıyla silindi.');
    }

    /**
     * Get sub-regions by region AJAX endpoint
     */
    public function getSubRegionsByRegion(Request $request)
    {
        $regionId = $request->get('region_id');
        
        $subRegions = SubRegion::with('translations')
            ->where('region_id', $regionId)
            ->active()
            ->ordered()
            ->get();
        
        $data = $subRegions->map(function($subRegion) {
            return [
                'id' => $subRegion->id,
                'name' => $subRegion->translate('tr')->name ?? $subRegion->translate('en')->name ?? $subRegion->name ?? 'İsimsiz Alt Bölge'
            ];
        });

        return response()->json($data);
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
