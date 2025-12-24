<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Model3d;
use App\Services\SketchfabService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class Model3dController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $locale = app()->getLocale();

        $query = Model3d::query()->where('is_active', true)->orderBy('sort_order')
            ->with(['translations', 'archaeologicalSite.translations']);

        if ($request->filled('archaeological_site_id')) {
            $query->where('archaeological_site_id', $request->integer('archaeological_site_id'));
        }

        $models = $query->get();

        return response()->json([
            'success' => true,
            'locale' => $locale,
            'data' => $models->map(function (Model3d $model) use ($locale) {
                $modelTranslation = $model->translate($locale);
                $siteTranslation = $model->archaeologicalSite?->translate($locale);
                
                return [
                    'id' => $model->id,
                    'archaeological_site' => [
                        'id' => $model->archaeologicalSite?->id,
                        'name' => $siteTranslation?->name ?? '',
                    ],
                    'name' => $modelTranslation?->name ?? '',
                    'description' => $modelTranslation?->description ?? '',
                    'sketchfab_model_id' => $model->sketchfab_model_id,
                    'thumbnail' => $model->sketchfab_thumbnail_url,
                    'sort_order' => $model->sort_order,
                    'qr_uuid' => $model->qr_uuid,
                    'qr_image_url' => $model->qr_image_path ? url('storage/'.$model->qr_image_path) : null,
                    'audio_guide_path' => $model->archaeologicalSite?->audio_guide_path ? url('storage/' . $model->archaeologicalSite->audio_guide_path) : null,
                ];
            }),
        ]);
    }

    public function show(Request $request, Model3d $model3d): JsonResponse
    {
        $locale = app()->getLocale();

        if (!$model3d->is_active) {
            return response()->json(['success' => false, 'message' => 'Model not found or inactive'], 404);
        }

        $model3d->load(['translations', 'archaeologicalSite.translations']);
        
        $modelTranslation = $model3d->translate($locale);
        $siteTranslation = $model3d->archaeologicalSite?->translate($locale);

        return response()->json([
            'success' => true,
            'locale' => $locale,
            'data' => [
                'id' => $model3d->id,
                'archaeological_site' => [
                    'id' => $model3d->archaeologicalSite?->id,
                    'name' => $siteTranslation?->name ?? '',
                ],
                'name' => $modelTranslation?->name ?? '',
                'description' => $modelTranslation?->description ?? '',
                'sketchfab_model_id' => $model3d->sketchfab_model_id,
                'thumbnail' => $model3d->sketchfab_thumbnail_url,
                'sort_order' => $model3d->sort_order,
                'qr_uuid' => $model3d->qr_uuid,
                'qr_image_url' => $model3d->qr_image_path ? url('storage/'.$model3d->qr_image_path) : null,
                'audioGuide' => $model3d->archaeologicalSite?->audio_guide_path ? url('storage/' . $model3d->archaeologicalSite->audio_guide_path) : null,
            ],
        ]);
    }

    /**
     * Get 3D models by IDs with full data
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function getByIds(Request $request): JsonResponse
    {
        $locale = app()->getLocale();

        // Validate that ids parameter is provided and is an array
        $validated = $request->validate([
            'ids' => 'required|array|min:1',
            'ids.*' => 'integer'
        ]);

        $ids = $validated['ids'];
        
        // Log the requested IDs and check which ones exist
        \Log::info('Requested IDs:', ['ids' => $ids]);
        $existingIds = Model3d::whereIn('id', $ids)->pluck('id')->toArray();
        $missingIds = array_diff($ids, $existingIds);
        if (!empty($missingIds)) {
            \Log::warning('Missing IDs in database:', ['missing_ids' => $missingIds]);
        }

        $models = Model3d::whereIn('id', $ids)
            ->where('is_active', true)
            ->with([
                'translations',
                'archaeologicalSite.translations',
                'archaeologicalSite.subRegion.translations',
                'archaeologicalSite.subRegion.region.translations',
                'subRegion.translations',
                'subRegion.region.translations'
            ])
            ->get()
            ->sortBy(function ($model) use ($ids) {
                return array_search($model->id, $ids);
            });

        return response()->json([
            'success' => true,
            'locale' => $locale,
            'data' => $models->map(function (Model3d $model) use ($locale) {
                $modelTranslation = $model->translate($locale);
                $siteTranslation = $model->archaeologicalSite?->translate($locale);
                $archSubRegionTranslation = $model->archaeologicalSite?->subRegion?->translate($locale);
                $archRegionTranslation = $model->archaeologicalSite?->subRegion?->region?->translate($locale);
                $subRegionTranslation = $model->subRegion?->translate($locale);
                $regionTranslation = $model->subRegion?->region?->translate($locale);
                
                return [
                    'id' => $model->id,
                    'sub_region_id' => $model->sub_region_id,
                    'archaeological_site_id' => $model->archaeological_site_id,
                    'sketchfab_model_id' => $model->sketchfab_model_id,
                    'sketchfab_thumbnail_url' => $model->sketchfab_thumbnail_url,
                    'qr_uuid' => $model->qr_uuid,
                    'qr_image_path' => $model->qr_image_path ? url('storage/' . $model->qr_image_path) : null,
                    'sort_order' => $model->sort_order,
                    'is_active' => $model->is_active,
                    'created_at' => $model->created_at,
                    'updated_at' => $model->updated_at,
                    'name' => $modelTranslation?->name ?? '',
                    'description' => $modelTranslation?->description ?? '',
                    'audio_guide_path' => $model->archaeologicalSite?->audio_guide_path ? url('storage/' . $model->archaeologicalSite->audio_guide_path) : null,
                    'archaeological_site' => [
                        'id' => $model->archaeologicalSite?->id,
                        'name' => $siteTranslation?->name ?? '',
                        'description' => $siteTranslation?->description ?? '',
                        'latitude' => $model->archaeologicalSite?->latitude,
                        'longitude' => $model->archaeologicalSite?->longitude,
                        'image' => $model->archaeologicalSite?->image ? url('storage/' . $model->archaeologicalSite?->image) : null,
                        'is_nearby_enabled' => $model->archaeologicalSite?->is_nearby_enabled,
                        'is_active' => $model->archaeologicalSite?->is_active,
                        'sub_region' => [
                            'id' => $model->archaeologicalSite?->subRegion?->id,
                            'name' => $archSubRegionTranslation?->name ?? '',
                            'description' => $archSubRegionTranslation?->description ?? '',
                            'region' => [
                                'id' => $model->archaeologicalSite?->subRegion?->region?->id,
                                'name' => $archRegionTranslation?->name ?? '',
                                'description' => $archRegionTranslation?->description ?? '',
                            ],
                        ],
                    ],
                    'sub_region' => [
                        'id' => $model->subRegion?->id,
                        'name' => $subRegionTranslation?->name ?? '',
                        'description' => $subRegionTranslation?->description ?? '',
                        'region' => [
                            'id' => $model->subRegion?->region?->id,
                            'name' => $regionTranslation?->name ?? '',
                            'description' => $regionTranslation?->description ?? '',
                        ],
                    ],
                ];
            }),
        ]);
    }

    /**
     * Get 3D model file URLs from Sketchfab API
     *
     * @param Request $request
     * @param string $sketchfabId
     * @return JsonResponse
     */
    public function getModelFileUrl(Request $request, string $sketchfabId): JsonResponse
    {
        $locale = app()->getLocale();

        $sketchfabService = new SketchfabService();
        $result = $sketchfabService->getModelFileUrl($sketchfabId);

        if (!$result) {
            return response()->json([
                'success' => false,
                'locale' => $locale,
                'message' => 'Model bulunamadı veya indirilebilir format yok',
                'sketchfab_id' => $sketchfabId
            ], 404);
        }

        return response()->json([
            'success' => true,
            'locale' => $locale,
            'data' => $result
        ]);
    }
}


