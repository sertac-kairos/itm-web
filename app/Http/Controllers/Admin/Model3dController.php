<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Model3d;
use App\Models\ArchaeologicalSite;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class Model3dController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $query = Model3d::with(['translations', 'archaeologicalSite.translations']);

        // Filter by archaeological site
        if ($request->filled('archaeological_site_id')) {
            $query->where('archaeological_site_id', $request->archaeological_site_id);
        }

        // Search functionality
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->whereHas('translations', function($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%");
            });
        }

        // Status filter
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        $models3d = $query->ordered()->paginate(15)->withQueryString();
        $archaeologicalSites = ArchaeologicalSite::with('translations')->active()->get();

        return view('admin.models-3d.index', compact('models3d', 'archaeologicalSites'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request): View
    {
        $locales = config('translatable.locales');
        $archaeologicalSites = ArchaeologicalSite::with('translations')->active()->get();
        $selectedArchaeologicalSiteId = $request->get('archaeological_site_id');

        return view('admin.models-3d.create', compact('locales', 'archaeologicalSites', 'selectedArchaeologicalSiteId'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'archaeological_site_id' => 'required|exists:archaeological_sites,id',
            'sketchfab_model_id' => 'required|string|max:255',
            'sketchfab_thumbnail_url' => 'nullable|url|max:255',
            'sort_order' => 'required|integer|min:0',
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
            }
        }

        if (!$hasAtLeastOneName) {
            return redirect()->back()->withErrors(['translations' => 'En az bir dilde 3D model adı girilmelidir.'])->withInput();
        }

        if (!empty($translationRules)) {
            $request->validate($translationRules);
        }

        // Get archaeological site to auto-set sub_region_id
        $archaeologicalSite = ArchaeologicalSite::findOrFail($request->archaeological_site_id);

        // Create Model3d
        $model3d = Model3d::create([
            'archaeological_site_id' => $request->archaeological_site_id,
            'sub_region_id' => $archaeologicalSite->sub_region_id,
            'sketchfab_model_id' => $request->sketchfab_model_id,
            'sketchfab_thumbnail_url' => $request->sketchfab_thumbnail_url,
            'sort_order' => $request->sort_order,
            'is_active' => $request->boolean('is_active', false),
        ]);

        // Save translations
        foreach (config('translatable.locales') as $locale) {
            if ($request->filled("{$locale}.name")) {
                $model3d->translateOrNew($locale)->name = $request->input("{$locale}.name");
                $model3d->translateOrNew($locale)->description = $request->input("{$locale}.description");
            }
        }

        $model3d->save();

        return redirect()->route('admin.models-3d.index')
            ->with('success', '3D Model başarıyla oluşturuldu.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Model3d $model3d): View
    {
        $model3d->load(['translations', 'archaeologicalSite.translations']);
        $locales = config('translatable.locales');

        return view('admin.models-3d.show', compact('model3d', 'locales'));
    }

    /**
     * Generate QR code for the model
     */
    public function generateQr(Model3d $model3d): RedirectResponse
    {
        // Generate UUID if missing
        if (!$model3d->qr_uuid) {
            $model3d->qr_uuid = (string) Str::uuid();
        }

        // Build QR content (could be deep link or public URL)
        $qrContent = url('/models-3d/scan/' . $model3d->qr_uuid);

        // Generate QR as SVG (avoids Imagick/GD requirements)
        $qrSvg = QrCode::format('svg')->size(600)->margin(1)->generate($qrContent);
        $relativePath = 'qr/models-3d/' . $model3d->id . '.svg';
        \Storage::disk('public')->put($relativePath, $qrSvg);
        $model3d->qr_image_path = $relativePath;

        $model3d->save();

        return redirect()->route('admin.models-3d.show', $model3d)
            ->with('success', 'QR kod başarıyla oluşturuldu.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, Model3d $model3d): View
    {
        $model3d->load(['translations', 'archaeologicalSite']);
        $locales = config('translatable.locales');
        $archaeologicalSites = ArchaeologicalSite::with('translations')->active()->get();
        
        // Store referer URL for redirect after update
        $referer = $request->header('referer');
        $returnUrl = $referer && str_contains($referer, route('admin.models-3d.index')) 
            ? $referer 
            : route('admin.models-3d.index', $request->query());

        return view('admin.models-3d.edit', compact('model3d', 'locales', 'archaeologicalSites', 'returnUrl'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Model3d $model3d): RedirectResponse
    {
        $request->validate([
            'archaeological_site_id' => 'required|exists:archaeological_sites,id',
            'sketchfab_model_id' => 'required|string|max:255',
            'sketchfab_thumbnail_url' => 'nullable|url|max:255',
            'sort_order' => 'required|integer|min:0',
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
            }
        }

        if (!$hasAtLeastOneName) {
            return redirect()->back()->withErrors(['translations' => 'En az bir dilde 3D model adı girilmelidir.'])->withInput();
        }

        if (!empty($translationRules)) {
            $request->validate($translationRules);
        }

        // Get archaeological site to auto-set sub_region_id
        $archaeologicalSite = ArchaeologicalSite::findOrFail($request->archaeological_site_id);

        // Update Model3d
        $model3d->update([
            'archaeological_site_id' => $request->archaeological_site_id,
            'sub_region_id' => $archaeologicalSite->sub_region_id,
            'sketchfab_model_id' => $request->sketchfab_model_id,
            'sketchfab_thumbnail_url' => $request->sketchfab_thumbnail_url,
            'sort_order' => $request->sort_order,
            'is_active' => $request->boolean('is_active', false),
        ]);

        // Update translations
        foreach (config('translatable.locales') as $locale) {
            if ($request->filled("{$locale}.name")) {
                $model3d->translateOrNew($locale)->name = $request->input("{$locale}.name");
                $model3d->translateOrNew($locale)->description = $request->input("{$locale}.description");
            }
        }

        $model3d->save();

        // Redirect to return URL if provided, otherwise to index
        $returnUrl = $request->input('return_url', route('admin.models-3d.index', $request->query()));
        return redirect($returnUrl)
            ->with('success', '3D Model başarıyla güncellendi.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Model3d $model3d): RedirectResponse
    {
        $model3d->delete();

        return redirect()->route('admin.models-3d.index')
            ->with('success', '3D Model başarıyla silindi.');
    }
}
