<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SubRegion;
use App\Models\Region;
use App\Services\DeepLTranslationService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage;

class SubRegionController extends Controller
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
        $query = SubRegion::with(['translations', 'region.translations']);

        // Filter by region if provided
        if ($request->filled('region_id')) {
            $query->where('region_id', $request->region_id);
        }

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('translations', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $subRegions = $query->ordered()->paginate(15)->withQueryString();
        $regions = Region::with('translations')->active()->ordered()->get();

        return view('admin.sub-regions.index', compact('subRegions', 'regions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request): View
    {
        $locales = config('translatable.locales');
        $regions = Region::with('translations')->active()->ordered()->get();
        $selectedRegionId = $request->get('region_id');

        return view('admin.sub-regions.create', compact('locales', 'regions', 'selectedRegionId'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'region_id' => 'required|exists:regions,id',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'sort_order' => 'required|integer|min:0',
            'image' => 'nullable|image|max:2048',
            'color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'is_active' => 'boolean',
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
                ->withErrors(['translations' => 'En az bir dilde alt bölge adı girilmelidir.'])
                ->withInput();
        }
        
        if (!empty($translationRules)) {
            $request->validate($translationRules);
        }

        $subRegion = new SubRegion();
        $subRegion->region_id = $request->region_id;
        $subRegion->latitude = $request->latitude;
        $subRegion->longitude = $request->longitude;
        $subRegion->color = $request->color ?? '#1a4a9f';
        $subRegion->sort_order = $request->sort_order;
        $subRegion->is_active = $request->has('is_active') ? $request->boolean('is_active') : false;

        // Handle image upload
        if ($request->hasFile('image')) {
            $subRegion->image = $request->file('image')->store('sub-regions', 'public');
        }

        $subRegion->save();

        // Save translations - only for languages with actual content
        foreach (config('translatable.locales') as $locale) {
            if ($request->filled("{$locale}.name")) {
                $subRegion->translateOrNew($locale)->name = $request->input("{$locale}.name");
                $subRegion->translateOrNew($locale)->subtitle = $request->input("{$locale}.subtitle");
                $subRegion->translateOrNew($locale)->description = $request->input("{$locale}.description");
                
                // Handle audio guide upload
                if ($request->hasFile("{$locale}.audio_guide")) {
                    $subRegion->translateOrNew($locale)->audio_guide_path = $request->file("{$locale}.audio_guide")->store('sub-regions/audio', 'public');
                }
            }
        }

        $subRegion->save();

        return redirect()
            ->route('admin.sub-regions.index', ['region_id' => $subRegion->region_id])
            ->with('success', 'Alt bölge başarıyla oluşturuldu.');
    }

    /**
     * Display the specified resource.
     */
    public function show(SubRegion $subRegion): View
    {
        $subRegion->load([
            'translations', 
            'region.translations', 
            'archaeologicalSites.translations',
            'models3d.translations',
            'audioGuides.translations',
            'qrCodes'
        ]);
        
        return view('admin.sub-regions.show', compact('subRegion'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, SubRegion $subRegion): View
    {
        $subRegion->load(['translations', 'region']);
        $locales = config('translatable.locales');
        $regions = Region::with('translations')->active()->ordered()->get();
        
        // Store referer URL for redirect after update
        $referer = $request->header('referer');
        $returnUrl = $referer && str_contains($referer, route('admin.sub-regions.index')) 
            ? $referer 
            : route('admin.sub-regions.index', $request->query());
        
        return view('admin.sub-regions.edit', compact('subRegion', 'locales', 'regions', 'returnUrl'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SubRegion $subRegion): RedirectResponse
    {
        $request->validate([
            'region_id' => 'required|exists:regions,id',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'sort_order' => 'required|integer|min:0',
            'image' => 'nullable|image|max:2048',
            'color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'is_active' => 'boolean',
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
                ->withErrors(['translations' => 'En az bir dilde alt bölge adı girilmelidir.'])
                ->withInput();
        }
        
        if (!empty($translationRules)) {
            $request->validate($translationRules);
        }

        $subRegion->region_id = $request->region_id;
        $subRegion->latitude = $request->latitude;
        $subRegion->longitude = $request->longitude;
        $subRegion->color = $request->color ?? $subRegion->color ?? '#1a4a9f';
        $subRegion->sort_order = $request->sort_order;
        $subRegion->is_active = $request->has('is_active') ? $request->boolean('is_active') : false;

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image
            if ($subRegion->image) {
                Storage::disk('public')->delete($subRegion->image);
            }
            $subRegion->image = $request->file('image')->store('sub-regions', 'public');
        }

        $subRegion->save();

        // Update translations - only for languages with actual content
        foreach (config('translatable.locales') as $locale) {
            if ($request->filled("{$locale}.name")) {
                $subRegion->translateOrNew($locale)->name = $request->input("{$locale}.name");
                $subRegion->translateOrNew($locale)->subtitle = $request->input("{$locale}.subtitle");
                $subRegion->translateOrNew($locale)->description = $request->input("{$locale}.description");
                
                // Handle audio guide upload
                if ($request->hasFile("{$locale}.audio_guide")) {
                    // Delete old audio file
                    $translation = $subRegion->translate($locale, false);
                    if ($translation && $translation->audio_guide_path) {
                        Storage::disk('public')->delete($translation->audio_guide_path);
                    }
                    $subRegion->translateOrNew($locale)->audio_guide_path = $request->file("{$locale}.audio_guide")->store('sub-regions/audio', 'public');
                }
            }
        }

        $subRegion->save();

        // Redirect to return URL if provided, otherwise to index
        $returnUrl = $request->input('return_url', route('admin.sub-regions.index', $request->query()));
        return redirect($returnUrl)
            ->with('success', 'Alt bölge başarıyla güncellendi.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SubRegion $subRegion): RedirectResponse
    {
        $regionId = $subRegion->region_id;

        // Delete image if exists
        if ($subRegion->image) {
            Storage::disk('public')->delete($subRegion->image);
        }

        $subRegion->delete();

        return redirect()
            ->route('admin.sub-regions.index', ['region_id' => $regionId])
            ->with('success', 'Alt bölge başarıyla silindi.');
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