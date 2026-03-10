<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Story;
use App\Models\Model3d;
use App\Services\DeepLTranslationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class StoryController extends Controller
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
        $query = Story::with(['translations']);

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
            $query->leftJoin('story_translations', function($join) use ($locale) {
                $join->on('stories.id', '=', 'story_translations.story_id')
                     ->where('story_translations.locale', '=', $locale);
            })
            ->orderBy('story_translations.title', $sortDirection)
            ->select('stories.*');
        } elseif (in_array($sortField, ['id', 'model_3d_id', 'sort_order', 'is_active', 'created_at', 'updated_at'])) {
            $query->orderBy($sortField, $sortDirection);
        } else {
            $query->ordered();
        }

        $stories = $query->paginate(15)->withQueryString();

        return view('admin.stories.index', compact('stories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $locales = config('translatable.locales');
        $models3d = Model3d::active()->ordered()->with('translations')->get();
        return view('admin.stories.create', compact('locales', 'models3d'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'thumbnail' => 'nullable|image|max:2048',
            'image' => 'nullable|image|max:4096',
            'sort_order' => 'required|integer|min:0',
            'is_active' => 'boolean',
            'model_3d_id' => 'nullable|exists:models_3d,id',
        ]);

        // Translations validation - at least one language title
        $hasAtLeastOne = false;
        $translationRules = [];
        foreach (config('translatable.locales') as $locale) {
            if ($request->filled("{$locale}.title")) {
                $hasAtLeastOne = true;
                $translationRules["{$locale}.title"] = 'required|string|max:255';
                $translationRules["{$locale}.description"] = 'nullable|string';
                $translationRules["{$locale}.image"] = 'nullable|image|max:4096';
                $translationRules["{$locale}.edited_image_data"] = 'nullable|string';
            }
        }
        if (!$hasAtLeastOne) {
            return back()->withErrors(['translations' => 'En az bir dilde başlık girilmelidir.'])->withInput();
        }
        if (!empty($translationRules)) {
            $request->validate($translationRules);
        }

        $thumbnailPath = null;
        if ($request->hasFile('thumbnail')) {
            $thumbnailPath = $request->file('thumbnail')->store('stories/thumbnails', 'public');
        }

        $story = Story::create([
            'thumbnail' => $thumbnailPath,
            'sort_order' => $request->sort_order,
            'is_active' => $request->boolean('is_active', false),
            'model_3d_id' => $request->model_3d_id,
        ]);

        foreach (config('translatable.locales') as $locale) {
            if ($request->filled("{$locale}.title")) {
                $translation = $story->translateOrNew($locale);
                $translation->title = $request->input("{$locale}.title");
                $translation->description = $request->input("{$locale}.description");
                
                // Handle translatable image
                // Priority: edited canvas data > uploaded file
                if ($request->filled("{$locale}.edited_image_data")) {
                    $imagePath = $this->saveCanvasAsImage($request->input("{$locale}.edited_image_data"), $locale);
                    $translation->image = $imagePath;
                } elseif ($request->hasFile("{$locale}.image")) {
                    $translation->image = $request->file("{$locale}.image")->store("stories/images/{$locale}", 'public');
                }
            }
        }
        $story->save();

        return redirect()->route('admin.stories.index')->with('success', 'Hikaye başarıyla oluşturuldu.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Story $story): View
    {
        $story->load('translations');
        return view('admin.stories.show', compact('story'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, Story $story): View
    {
        $story->load('translations', 'model3d');
        $locales = config('translatable.locales');
        $models3d = Model3d::active()->ordered()->with('translations')->get();
        
        // Store referer URL for redirect after update
        $referer = $request->header('referer');
        $returnUrl = $referer && str_contains($referer, route('admin.stories.index')) 
            ? $referer 
            : route('admin.stories.index', $request->query());
        
        return view('admin.stories.edit', compact('story', 'locales', 'models3d', 'returnUrl'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Story $story): RedirectResponse
    {
        $request->validate([
            'thumbnail' => 'nullable|image|max:2048',
            'sort_order' => 'required|integer|min:0',
            'is_active' => 'boolean',
            'model_3d_id' => 'nullable|exists:models_3d,id',
        ]);

        // Translations validation - at least one language title
        $hasAtLeastOne = false;
        $translationRules = [];
        foreach (config('translatable.locales') as $locale) {
            if ($request->filled("{$locale}.title")) {
                $hasAtLeastOne = true;
                $translationRules["{$locale}.title"] = 'required|string|max:255';
                $translationRules["{$locale}.description"] = 'nullable|string';
                $translationRules["{$locale}.image"] = 'nullable|image|max:4096';
                $translationRules["{$locale}.edited_image_data"] = 'nullable|string';
            }
        }
        if (!$hasAtLeastOne) {
            return back()->withErrors(['translations' => 'En az bir dilde başlık girilmelidir.'])->withInput();
        }
        if (!empty($translationRules)) {
            $request->validate($translationRules);
        }

        // Handle main thumbnail
        if ($request->hasFile('thumbnail')) {
            // Delete old thumbnail
            if ($story->thumbnail) {
                Storage::disk('public')->delete($story->thumbnail);
            }
            $thumbnailPath = $request->file('thumbnail')->store('stories/thumbnails', 'public');
            $story->thumbnail = $thumbnailPath;
        }

        $story->update([
            'sort_order' => $request->sort_order,
            'is_active' => $request->boolean('is_active', false),
            'model_3d_id' => $request->model_3d_id,
        ]);

        // Handle translations
        foreach (config('translatable.locales') as $locale) {
            if ($request->filled("{$locale}.title")) {
                $translation = $story->translateOrNew($locale);
                $translation->title = $request->input("{$locale}.title");
                $translation->description = $request->input("{$locale}.description");
                
                // Handle translatable image
                // Priority: edited canvas data > uploaded file
                if ($request->filled("{$locale}.edited_image_data")) {
                    if ($translation->image) {
                        Storage::disk('public')->delete($translation->image);
                    }
                    $translation->image = $this->saveCanvasAsImage($request->input("{$locale}.edited_image_data"), $locale);
                } elseif ($request->hasFile("{$locale}.image")) {
                    if ($translation->image) {
                        Storage::disk('public')->delete($translation->image);
                    }
                    $translation->image = $request->file("{$locale}.image")->store("stories/images/{$locale}", 'public');
                }
            }
        }
        $story->save();

        // Redirect to return URL if provided, otherwise to index
        $returnUrl = $request->input('return_url', route('admin.stories.index', $request->query()));
        return redirect($returnUrl)->with('success', 'Hikaye başarıyla güncellendi.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Story $story): RedirectResponse
    {
        \Log::info('Story destroy method called for story ID: ' . $story->id);
        
        try {
            // Delete files
            if ($story->thumbnail) {
                Storage::disk('public')->delete($story->thumbnail);
                \Log::info('Deleted thumbnail: ' . $story->thumbnail);
            }
            
            foreach ($story->translations as $translation) {
                if ($translation->image) {
                    Storage::disk('public')->delete($translation->image);
                    \Log::info('Deleted translation image: ' . $translation->image);
                }
            }

            $story->delete();
            \Log::info('Story deleted successfully');

            return redirect()->route('admin.stories.index')->with('success', 'Hikaye başarıyla silindi.');
        } catch (\Exception $e) {
            \Log::error('Error deleting story: ' . $e->getMessage());
            return redirect()->route('admin.stories.index')->with('error', 'Hikaye silinirken bir hata oluştu.');
        }
    }

    /**
     * Save base64 canvas data URL as image file under stories path per locale.
     */
    private function saveCanvasAsImage(string $dataURL, string $locale): string
    {
        $commaPos = strpos($dataURL, ',');
        $data = $commaPos !== false ? substr($dataURL, $commaPos + 1) : $dataURL;
        $binary = base64_decode($data);
        $directory = "stories/images/{$locale}";
        $filename = $directory . '/' . uniqid('canvas_') . '.png';
        Storage::disk('public')->put($filename, $binary);
        return $filename;
    }

    /**
     * Move story up in sort order
     */
    public function moveUp(Story $story): RedirectResponse
    {
        // Find the previous item (lower sort_order)
        $previous = Story::where('sort_order', '<', $story->sort_order)
            ->orderBy('sort_order', 'desc')
            ->first();
        
        if ($previous) {
            // Swap sort orders
            $tempOrder = $story->sort_order;
            $story->sort_order = $previous->sort_order;
            $previous->sort_order = $tempOrder;
            
            $story->save();
            $previous->save();
            
            return redirect()->back()->with('success', 'Sıralama güncellendi.');
        }
        
        return redirect()->back()->with('error', 'Hikaye zaten en üstte.');
    }

    /**
     * Move story down in sort order
     */
    public function moveDown(Story $story): RedirectResponse
    {
        // Find the next item (higher sort_order)
        $next = Story::where('sort_order', '>', $story->sort_order)
            ->orderBy('sort_order', 'asc')
            ->first();
        
        if ($next) {
            // Swap sort orders
            $tempOrder = $story->sort_order;
            $story->sort_order = $next->sort_order;
            $next->sort_order = $tempOrder;
            
            $story->save();
            $next->save();
            
            return redirect()->back()->with('success', 'Sıralama güncellendi.');
        }
        
        return redirect()->back()->with('error', 'Hikaye zaten en altta.');
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
