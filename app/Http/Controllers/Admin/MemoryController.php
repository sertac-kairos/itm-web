<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Memory;
use App\Services\DeepLTranslationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class MemoryController extends Controller
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
        $query = Memory::with(['translations']);

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

        if ($request->filled('has_link')) {
            if ($request->has_link === 'yes') {
                $query->whereNotNull('link')->where('link', '!=', '');
            } else {
                $query->whereNull('link')->orWhere('link', '');
            }
        }

        // Sorting
        $sortField = $request->get('sort', 'sort_order');
        $sortDirection = $request->get('direction', 'asc');
        
        if ($sortField === 'title') {
            // Sort by translated title
            $locale = app()->getLocale();
            $query->leftJoin('memory_translations', function($join) use ($locale) {
                $join->on('memories.id', '=', 'memory_translations.memory_id')
                     ->where('memory_translations.locale', '=', $locale);
            })
            ->orderBy('memory_translations.title', $sortDirection)
            ->select('memories.*');
        } elseif (in_array($sortField, ['id', 'author', 'link', 'image', 'sort_order', 'is_active', 'created_at', 'updated_at'])) {
            $query->orderBy($sortField, $sortDirection);
        } else {
            $query->ordered();
        }

        $memories = $query->paginate(15)->withQueryString();

        return view('admin.memories.index', compact('memories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $locales = config('translatable.locales');
        return view('admin.memories.create', compact('locales'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'image' => 'nullable|image|max:4096',
            'link' => 'nullable|url|max:500',
            'author' => 'nullable|string|max:255',
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

        $imagePath = null;
        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            $imagePath = $request->file('image')->store('memories', 'public');
        }

        $memory = Memory::create([
            'image' => $imagePath,
            'link' => $request->link,
            'author' => $request->author,
            'sort_order' => $request->sort_order,
            'is_active' => $request->boolean('is_active', false),
        ]);

        // Handle translations
        foreach (config('translatable.locales') as $locale) {
            if ($request->filled("{$locale}.title") && $request->filled("{$locale}.content")) {
                $translation = $memory->translateOrNew($locale);
                $translation->title = $request->input("{$locale}.title");
                $translation->content = $request->input("{$locale}.content");
            }
        }
        $memory->save();

        return redirect()->route('admin.memories.index')->with('success', 'Hafıza İzmir kaydı başarıyla oluşturuldu.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Memory $memory): View
    {
        $memory->load(['translations']);
        return view('admin.memories.show', compact('memory'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, Memory $memory): View
    {
        $memory->load(['translations']);
        $locales = config('translatable.locales');
        
        // Store referer URL for redirect after update
        $referer = $request->header('referer');
        $returnUrl = $referer && str_contains($referer, route('admin.memories.index')) 
            ? $referer 
            : route('admin.memories.index', $request->query());
        
        return view('admin.memories.edit', compact('memory', 'locales', 'returnUrl'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Memory $memory): RedirectResponse
    {
        $request->validate([
            'image' => 'nullable|image|max:4096',
            'link' => 'nullable|url|max:500',
            'author' => 'nullable|string|max:255',
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

        $imagePath = $memory->image;
        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            // Delete old image if exists
            if ($memory->image) {
                Storage::disk('public')->delete($memory->image);
            }
            $imagePath = $request->file('image')->store('memories', 'public');
        }

        $memory->update([
            'image' => $imagePath,
            'link' => $request->link,
            'author' => $request->author,
            'sort_order' => $request->sort_order,
            'is_active' => $request->boolean('is_active', false),
        ]);

        // Handle translations
        foreach (config('translatable.locales') as $locale) {
            if ($request->filled("{$locale}.title") && $request->filled("{$locale}.content")) {
                $translation = $memory->translateOrNew($locale);
                $translation->title = $request->input("{$locale}.title");
                $translation->content = $request->input("{$locale}.content");
            }
        }
        $memory->save();

        // Redirect to return URL if provided, otherwise to index
        $returnUrl = $request->input('return_url', route('admin.memories.index', $request->query()));
        return redirect($returnUrl)->with('success', 'Hafıza İzmir kaydı başarıyla güncellendi.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Memory $memory): RedirectResponse
    {
        // Delete image if exists
        if ($memory->image) {
            Storage::disk('public')->delete($memory->image);
        }

        $memory->delete();

        return redirect()->route('admin.memories.index')->with('success', 'Hafıza İzmir kaydı başarıyla silindi.');
    }

    /**
     * Move memory up in sort order
     */
    public function moveUp(Memory $memory): RedirectResponse
    {
        // Find the previous item (lower sort_order)
        $previous = Memory::where('sort_order', '<', $memory->sort_order)
            ->orderBy('sort_order', 'desc')
            ->first();
        
        if ($previous) {
            // Swap sort orders
            $tempOrder = $memory->sort_order;
            $memory->sort_order = $previous->sort_order;
            $previous->sort_order = $tempOrder;
            
            $memory->save();
            $previous->save();
            
            return redirect()->back()->with('success', 'Sıralama güncellendi.');
        }
        
        return redirect()->back()->with('error', 'Anı zaten en üstte.');
    }

    /**
     * Move memory down in sort order
     */
    public function moveDown(Memory $memory): RedirectResponse
    {
        // Find the next item (higher sort_order)
        $next = Memory::where('sort_order', '>', $memory->sort_order)
            ->orderBy('sort_order', 'asc')
            ->first();
        
        if ($next) {
            // Swap sort orders
            $tempOrder = $memory->sort_order;
            $memory->sort_order = $next->sort_order;
            $next->sort_order = $tempOrder;
            
            $memory->save();
            $next->save();
            
            return redirect()->back()->with('success', 'Sıralama güncellendi.');
        }
        
        return redirect()->back()->with('error', 'Anı zaten en altta.');
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
