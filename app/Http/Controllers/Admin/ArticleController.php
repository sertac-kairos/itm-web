<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\ArticleImage;
use App\Services\DeepLTranslationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ArticleController extends Controller
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
        $query = Article::with(['translations', 'images']);

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

        if ($request->filled('author')) {
            $query->where('author', 'like', "%{$request->author}%");
        }

        $articles = $query->ordered()->paginate(15)->withQueryString();

        return view('admin.articles.index', compact('articles'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $locales = config('translatable.locales');
        return view('admin.articles.create', compact('locales'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'author' => 'required|string|max:255',
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

        $article = Article::create([
            'author' => $request->author,
            'sort_order' => $request->sort_order,
            'is_active' => $request->boolean('is_active', true),
        ]);

        // Handle translations
        foreach (config('translatable.locales') as $locale) {
            if ($request->filled("{$locale}.title") && $request->filled("{$locale}.content")) {
                $translation = $article->translateOrNew($locale);
                $translation->title = $request->input("{$locale}.title");
                $translation->content = $request->input("{$locale}.content");
            }
        }
        $article->save();

        // Handle images
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $image) {
                if ($image && $image->isValid()) {
                    $imagePath = $image->store('articles/images', 'public');
                    $altText = $request->input("image_alt_texts.{$index}", '');
                    
                    ArticleImage::create([
                        'article_id' => $article->id,
                        'image_path' => $imagePath,
                        'alt_text' => $altText,
                        'sort_order' => $index,
                    ]);
                }
            }
        }

        return redirect()->route('admin.articles.index')->with('success', 'Yazı başarıyla oluşturuldu.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Article $article): View
    {
        $article->load(['translations', 'images']);
        return view('admin.articles.show', compact('article'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, Article $article): View
    {
        $article->load(['translations', 'images']);
        $locales = config('translatable.locales');
        
        // Store referer URL for redirect after update
        $referer = $request->header('referer');
        $returnUrl = $referer && str_contains($referer, route('admin.articles.index')) 
            ? $referer 
            : route('admin.articles.index', $request->query());
        
        return view('admin.articles.edit', compact('article', 'locales', 'returnUrl'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Article $article): RedirectResponse
    {
        $request->validate([
            'author' => 'required|string|max:255',
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

        $article->update([
            'author' => $request->author,
            'sort_order' => $request->sort_order,
            'is_active' => $request->boolean('is_active', true),
        ]);

        // Handle translations
        foreach (config('translatable.locales') as $locale) {
            if ($request->filled("{$locale}.title") && $request->filled("{$locale}.content")) {
                $translation = $article->translateOrNew($locale);
                $translation->title = $request->input("{$locale}.title");
                $translation->content = $request->input("{$locale}.content");
            }
        }
        $article->save();

        // Handle new images
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $image) {
                if ($image && $image->isValid()) {
                    $imagePath = $image->store('articles/images', 'public');
                    $altText = $request->input("image_alt_texts.{$index}", '');
                    
                    ArticleImage::create([
                        'article_id' => $article->id,
                        'image_path' => $imagePath,
                        'alt_text' => $altText,
                        'sort_order' => $article->images()->count() + $index,
                    ]);
                }
            }
        }

        // Redirect to return URL if provided, otherwise to index
        $returnUrl = $request->input('return_url', route('admin.articles.index', $request->query()));
        return redirect($returnUrl)->with('success', 'Yazı başarıyla güncellendi.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Article $article): RedirectResponse
    {
        // Delete images
        foreach ($article->images as $image) {
            Storage::disk('public')->delete($image->image_path);
        }

        $article->delete();

        return redirect()->route('admin.articles.index')->with('success', 'Yazı başarıyla silindi.');
    }

    /**
     * Delete a specific image from an article.
     */
    public function deleteImage(Article $article, ArticleImage $image): RedirectResponse
    {
        if ($image->article_id === $article->id) {
            Storage::disk('public')->delete($image->image_path);
            $image->delete();
            return back()->with('success', 'Resim başarıyla silindi.');
        }

        return back()->with('error', 'Geçersiz resim.');
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
