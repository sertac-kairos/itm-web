<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\News;
use App\Models\NewsImage;
use App\Services\DeepLTranslationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Illuminate\Support\Str;

class NewsController extends Controller
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
        $query = News::with(['translations', 'images']);

        if ($request->filled('status')) {
            $request->status === 'active'
                ? $query->where('is_active', true)
                : ($request->status === 'inactive' ? $query->where('is_active', false) : null);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('translations', function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }

        if ($request->filled('date_from')) {
            $query->where('news_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('news_date', '<=', $request->date_to);
        }

        $news = $query->ordered()->paginate(15)->withQueryString();

        return view('admin.news.index', compact('news'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $locales = config('translatable.locales');
        return view('admin.news.create', compact('locales'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'news_date' => 'required|date',
            'images.*' => 'nullable|image|max:4096',
            'image_alt_texts.*' => 'nullable|string|max:255',
            'sort_order' => 'required|integer|min:0',
            'is_active' => 'boolean',
        ]);

        // Translations validation - at least one language title and content
        $hasAtLeastOne = false;
        $translationRules = [];
        foreach (config('translatable.locales') as $locale) {
            if ($request->filled("{$locale}.title") || $request->filled("{$locale}.content")) {
                $hasAtLeastOne = true;
                $translationRules["{$locale}.title"] = 'required|string|max:255';
                $translationRules["{$locale}.content"] = 'required|string';
            }
        }
        if (!$hasAtLeastOne) {
            return back()->withErrors(['translations' => 'En az bir dilde başlık ve içerik girilmelidir.'])->withInput();
        }
        if (!empty($translationRules)) {
            $request->validate($translationRules);
        }

        // Generate slug from first available title
        $slug = '';
        foreach (config('translatable.locales') as $locale) {
            if ($request->filled("{$locale}.title")) {
                $slug = Str::slug($request->input("{$locale}.title"));
                break;
            }
        }

        $news = News::create([
            'slug' => $slug,
            'news_date' => $request->news_date,
            'sort_order' => $request->sort_order,
            'is_active' => $request->boolean('is_active', true),
        ]);

        // Handle translations with auto-translation
        $this->handleTranslations($news, $request);
        $news->save();

        // Handle images
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $image) {
                if ($image) {
                    $path = $image->store('news', 'public');
                    $altText = $request->input("image_alt_texts.{$index}", '');
                    
                    NewsImage::create([
                        'news_id' => $news->id,
                        'image_path' => $path,
                        'alt_text' => $altText,
                        'sort_order' => $index,
                    ]);
                }
            }
        }

        return redirect()->route('admin.news.index')
            ->with('success', 'Haber başarıyla oluşturuldu.');
    }

    /**
     * Display the specified resource.
     */
    public function show(News $news): View
    {
        $news->load(['translations', 'images']);
        return view('admin.news.show', compact('news'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, News $news): View
    {
        $news->load(['translations', 'images']);
        $locales = config('translatable.locales');
        
        // Store referer URL for redirect after update
        $referer = $request->header('referer');
        $returnUrl = $referer && str_contains($referer, route('admin.news.index')) 
            ? $referer 
            : route('admin.news.index', $request->query());
        
        return view('admin.news.edit', compact('news', 'locales', 'returnUrl'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, News $news): RedirectResponse
    {
        $request->validate([
            'news_date' => 'required|date',
            'images.*' => 'nullable|image|max:4096',
            'image_alt_texts.*' => 'nullable|string|max:255',
            'sort_order' => 'required|integer|min:0',
            'is_active' => 'boolean',
        ]);

        // Translations validation - at least one language title and content
        $hasAtLeastOne = false;
        $translationRules = [];
        foreach (config('translatable.locales') as $locale) {
            if ($request->filled("{$locale}.title") || $request->filled("{$locale}.content")) {
                $hasAtLeastOne = true;
                $translationRules["{$locale}.title"] = 'required|string|max:255';
                $translationRules["{$locale}.content"] = 'required|string';
            }
        }
        if (!$hasAtLeastOne) {
            return back()->withErrors(['translations' => 'En az bir dilde başlık ve içerik girilmelidir.'])->withInput();
        }
        if (!empty($translationRules)) {
            $request->validate($translationRules);
        }

        // Generate slug from first available title
        $slug = '';
        foreach (config('translatable.locales') as $locale) {
            if ($request->filled("{$locale}.title")) {
                $slug = Str::slug($request->input("{$locale}.title"));
                break;
            }
        }

        $news->update([
            'slug' => $slug,
            'news_date' => $request->news_date,
            'sort_order' => $request->sort_order,
            'is_active' => $request->boolean('is_active', true),
        ]);

        // Handle translations with auto-translation
        $this->handleTranslations($news, $request);
        $news->save();

        // Handle new images
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $image) {
                if ($image) {
                    $path = $image->store('news', 'public');
                    $altText = $request->input("image_alt_texts.{$index}", '');
                    
                    NewsImage::create([
                        'news_id' => $news->id,
                        'image_path' => $path,
                        'alt_text' => $altText,
                        'sort_order' => $news->images()->count() + $index,
                    ]);
                }
            }
        }

        // Redirect to return URL if provided, otherwise to index
        $returnUrl = $request->input('return_url', route('admin.news.index', $request->query()));
        return redirect($returnUrl)
            ->with('success', 'Haber başarıyla güncellendi.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(News $news): RedirectResponse
    {
        // Delete associated images from storage
        foreach ($news->images as $image) {
            Storage::disk('public')->delete($image->image_path);
        }

        $news->delete();

        return redirect()->route('admin.news.index')
            ->with('success', 'Haber başarıyla silindi.');
    }

    /**
     * Delete a specific image from news.
     */
    public function deleteImage(News $news, NewsImage $image): RedirectResponse
    {
        // Delete image from storage
        Storage::disk('public')->delete($image->image_path);
        
        // Delete image record
        $image->delete();

        return back()->with('success', 'Resim başarıyla silindi.');
    }

    /**
     * Handle translations with auto-translation
     */
    private function handleTranslations(News $news, Request $request): void
    {
        $locales = config('translatable.locales');
        $primaryLocale = $locales[0]; // First locale is primary (usually 'tr')
        $secondaryLocale = $locales[1] ?? 'en'; // Second locale (usually 'en')

        // Check if we have content in primary locale
        $hasPrimaryContent = $request->filled("{$primaryLocale}.title") && $request->filled("{$primaryLocale}.content");
        $hasSecondaryContent = $request->filled("{$secondaryLocale}.title") && $request->filled("{$secondaryLocale}.content");

        // If we have primary content but no secondary content, auto-translate
        if ($hasPrimaryContent && !$hasSecondaryContent && $this->translationService->isAvailable()) {
            $primaryTitle = $request->input("{$primaryLocale}.title");
            $primaryContent = $request->input("{$primaryLocale}.content");

            // Translate to secondary language
            $translatedTitle = $this->translationService->autoTranslate($primaryTitle, $primaryLocale);
            $translatedContent = $this->translationService->autoTranslate($primaryContent, $primaryLocale);

            // Set translated content in request
            $request->merge([
                "{$secondaryLocale}.title" => $translatedTitle,
                "{$secondaryLocale}.content" => $translatedContent,
            ]);
        }

        // Handle all translations
        foreach ($locales as $locale) {
            if ($request->filled("{$locale}.title") && $request->filled("{$locale}.content")) {
                $translation = $news->translateOrNew($locale);
                $translation->title = $request->input("{$locale}.title");
                $translation->content = $request->input("{$locale}.content");
            }
        }
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
