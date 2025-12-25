<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OnboardingSlide;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class OnboardingSlideController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $query = OnboardingSlide::with(['translations']);

        if ($request->filled('status')) {
            $request->status === 'active'
                ? $query->where('is_active', true)
                : ($request->status === 'inactive' ? $query->where('is_active', false) : null);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('translations', function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%");
            });
        }

        // Sorting
        $sortField = $request->get('sort', 'sort_order');
        $sortDirection = $request->get('direction', 'asc');
        
        if ($sortField === 'title') {
            // Sort by translated title
            $locale = app()->getLocale();
            $query->leftJoin('onboarding_slide_translations', function($join) use ($locale) {
                $join->on('onboarding_slides.id', '=', 'onboarding_slide_translations.onboarding_slide_id')
                     ->where('onboarding_slide_translations.locale', '=', $locale);
            })
            ->orderBy('onboarding_slide_translations.title', $sortDirection)
            ->select('onboarding_slides.*');
        } elseif (in_array($sortField, ['id', 'sort_order', 'is_active', 'created_at', 'updated_at'])) {
            $query->orderBy($sortField, $sortDirection);
        } else {
            $query->ordered();
        }

        $slides = $query->paginate(15)->withQueryString();

        return view('admin.onboarding-slides.index', compact('slides'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $locales = config('translatable.locales');
        return view('admin.onboarding-slides.create', compact('locales'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'sort_order' => 'required|integer|min:0',
            'is_active' => 'boolean',
            'edited_image_data' => 'nullable|string',
        ]);

        // Translations validation - at least one language title and image
        $hasAtLeastOne = false;
        $translationRules = [];
        foreach (config('translatable.locales') as $locale) {
            if ($request->filled("{$locale}.title")) {
                $hasAtLeastOne = true;
                $translationRules["{$locale}.title"] = 'required|string|max:255';
                $translationRules["{$locale}.description"] = 'nullable|string';
                $translationRules["{$locale}.edited_image_data"] = 'nullable|string';
            }
        }
        if (!$hasAtLeastOne) {
            return back()->withErrors(['translations' => 'En az bir dilde başlık girilmelidir.'])->withInput();
        }
        if (!empty($translationRules)) {
            $request->validate($translationRules);
        }

        $slide = OnboardingSlide::create([
            'sort_order' => $request->sort_order,
            'is_active' => $request->boolean('is_active', false),
        ]);

        foreach (config('translatable.locales') as $locale) {
            if ($request->filled("{$locale}.title")) {
                $slide->translateOrNew($locale)->title = $request->input("{$locale}.title");
                $slide->translateOrNew($locale)->description = $request->input("{$locale}.description");
                
                // Handle canvas image for each locale
                if ($request->filled("{$locale}.edited_image_data")) {
                    \Log::info('Onboarding Create - Using edited canvas data for locale: ' . $locale);
                    $dataLength = strlen($request->input("{$locale}.edited_image_data"));
                    \Log::info('Onboarding Create - Canvas data length: ' . $dataLength);
                    // Save edited canvas as image
                    $imagePath = $this->saveCanvasAsImage($request->input("{$locale}.edited_image_data"));
                    \Log::info('Onboarding Create - Canvas saved to: ' . $imagePath);
                    $slide->translateOrNew($locale)->image = $imagePath;
                }
            }
        }
        $slide->save();

        return redirect()->route('admin.onboarding-slides.index')->with('success', 'Onboarding slaytı oluşturuldu.');
    }

    /**
     * Save canvas data URL as image file
     */
    private function saveCanvasAsImage(string $dataURL): string
    {
        // Remove data URL prefix
        $data = substr($dataURL, strpos($dataURL, ',') + 1);
        $data = base64_decode($data);
        
        // Generate unique filename
        $filename = 'onboarding/' . uniqid('canvas_') . '.png';
        
        // Save to storage
        Storage::disk('public')->put($filename, $data);
        
        return $filename;
    }

    /**
     * Display the specified resource.
     */
    public function show(OnboardingSlide $onboardingSlide): View
    {
        $onboardingSlide->load(['translations']);
        $locales = config('translatable.locales');
        return view('admin.onboarding-slides.show', compact('onboardingSlide', 'locales'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, OnboardingSlide $onboardingSlide): View
    {
        $onboardingSlide->load(['translations']);
        $locales = config('translatable.locales');
        
        // Store referer URL for redirect after update
        $referer = $request->header('referer');
        $returnUrl = $referer && str_contains($referer, route('admin.onboarding-slides.index')) 
            ? $referer 
            : route('admin.onboarding-slides.index', $request->query());
        
        return view('admin.onboarding-slides.edit', compact('onboardingSlide', 'locales', 'returnUrl'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, OnboardingSlide $onboardingSlide): RedirectResponse
    {
        $request->validate([
            'sort_order' => 'required|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $hasAtLeastOne = false;
        $translationRules = [];
        foreach (config('translatable.locales') as $locale) {
            if ($request->filled("{$locale}.title")) {
                $hasAtLeastOne = true;
                $translationRules["{$locale}.title"] = 'required|string|max:255';
                $translationRules["{$locale}.description"] = 'nullable|string';
                $translationRules["{$locale}.edited_image_data"] = 'nullable|string';
            }
        }
        if (!$hasAtLeastOne) {
            return back()->withErrors(['translations' => 'En az bir dilde başlık girilmelidir.'])->withInput();
        }
        if (!empty($translationRules)) {
            $request->validate($translationRules);
        }

        $onboardingSlide->sort_order = $request->sort_order;
        $onboardingSlide->is_active = $request->boolean('is_active', false);
        $onboardingSlide->save();

        foreach (config('translatable.locales') as $locale) {
            if ($request->filled("{$locale}.title")) {
                $onboardingSlide->translateOrNew($locale)->title = $request->input("{$locale}.title");
                $onboardingSlide->translateOrNew($locale)->description = $request->input("{$locale}.description");
                
                // Handle canvas image for each locale
                if ($request->filled("{$locale}.edited_image_data")) {
                    \Log::info('Onboarding Update - Using edited canvas data for locale: ' . $locale);
                    $dataLength = strlen($request->input("{$locale}.edited_image_data"));
                    \Log::info('Onboarding Update - Canvas data length: ' . $dataLength);
                    
                    // Delete old image for this locale if exists
                    $translation = $onboardingSlide->getTranslation($locale, false);
                    if ($translation && $translation->image) {
                        Storage::disk('public')->delete($translation->image);
                    }
                    
                    // Save edited canvas as image
                    $imagePath = $this->saveCanvasAsImage($request->input("{$locale}.edited_image_data"));
                    \Log::info('Onboarding Update - Canvas saved to: ' . $imagePath);
                    $onboardingSlide->translateOrNew($locale)->image = $imagePath;
                }
            }
        }
        $onboardingSlide->save();

        // Redirect to return URL if provided, otherwise to index
        $returnUrl = $request->input('return_url', route('admin.onboarding-slides.index', $request->query()));
        return redirect($returnUrl)->with('success', 'Onboarding slaytı güncellendi.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(OnboardingSlide $onboardingSlide): RedirectResponse
    {
        // Delete all translation images
        foreach (config('translatable.locales') as $locale) {
            $translation = $onboardingSlide->getTranslation($locale, false);
            if ($translation && $translation->image) {
                Storage::disk('public')->delete($translation->image);
            }
        }
        $onboardingSlide->delete();
        return redirect()->route('admin.onboarding-slides.index')->with('success', 'Onboarding slaytı silindi.');
    }

    /**
     * Move onboarding slide up in sort order
     */
    public function moveUp(OnboardingSlide $onboardingSlide): RedirectResponse
    {
        // Find the previous item (lower sort_order)
        $previous = OnboardingSlide::where('sort_order', '<', $onboardingSlide->sort_order)
            ->orderBy('sort_order', 'desc')
            ->first();
        
        if ($previous) {
            // Swap sort orders
            $tempOrder = $onboardingSlide->sort_order;
            $onboardingSlide->sort_order = $previous->sort_order;
            $previous->sort_order = $tempOrder;
            
            $onboardingSlide->save();
            $previous->save();
            
            return redirect()->back()->with('success', 'Sıralama güncellendi.');
        }
        
        return redirect()->back()->with('error', 'Slayt zaten en üstte.');
    }

    /**
     * Move onboarding slide down in sort order
     */
    public function moveDown(OnboardingSlide $onboardingSlide): RedirectResponse
    {
        // Find the next item (higher sort_order)
        $next = OnboardingSlide::where('sort_order', '>', $onboardingSlide->sort_order)
            ->orderBy('sort_order', 'asc')
            ->first();
        
        if ($next) {
            // Swap sort orders
            $tempOrder = $onboardingSlide->sort_order;
            $onboardingSlide->sort_order = $next->sort_order;
            $next->sort_order = $tempOrder;
            
            $onboardingSlide->save();
            $next->save();
            
            return redirect()->back()->with('success', 'Sıralama güncellendi.');
        }
        
        return redirect()->back()->with('error', 'Slayt zaten en altta.');
    }
}
