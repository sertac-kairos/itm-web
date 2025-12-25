<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\ArchaeologicalSite;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ArchaeologicalSiteController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $locale = app()->getLocale();

        $query = ArchaeologicalSite::query()->active()
            ->with(['translations', 'subRegion.translations', 'models3d' => function ($q) {
                $q->where('is_active', true)->orderBy('sort_order')->with('translations');
            }]);

        if ($request->filled('sub_region_id')) {
            $query->where('sub_region_id', $request->integer('sub_region_id'));
        }

        $sites = $query->get();

        return response()->json([
            'success' => true,
            'locale' => $locale,
            'data' => $sites->map(function (ArchaeologicalSite $site) use ($locale) {
                $siteTranslation = $site->translate($locale);
                $subRegionTranslation = $site->subRegion?->translate($locale);
                
                return [
                    'id' => $site->id,
                    'sub_region' => [
                        'id' => $site->subRegion?->id,
                        'name' => $subRegionTranslation?->name ?? '',
                    ],
                    'name' => $siteTranslation?->name ?? '',
                    'model_key' => ($site->getTranslation('tr', false)?->name ?? ''),
                    'description' => $siteTranslation?->description ?? '',
                    'latitude' => $site->latitude,
                    'longitude' => $site->longitude,
                    'image' => $site->image ? url('storage/' . $site->image) : null,
                    'models_3d' => $site->models3d->map(function ($model) use ($site, $locale) {
                        $modelTranslation = $model->translate($locale);
                        
                        return [
                            'id' => $model->id,
                            'name' => $modelTranslation?->name ?? '',
                            'description' => $modelTranslation?->description ?? '',
                            'sketchfab_model_id' => $model->sketchfab_model_id,
                            'thumbnail' => $model->sketchfab_thumbnail_url,
                            'sort_order' => $model->sort_order,
                            'audio_guide_path' => $site->audio_guide_path ? url('storage/' . $site->audio_guide_path) : null,
                        ];
                    }),
                ];
            }),
        ]);
    }

    public function show(Request $request, ArchaeologicalSite $archaeologicalSite): JsonResponse
    {
        $locale = app()->getLocale();

        if (!$archaeologicalSite->is_active) {
            return response()->json(['success' => false, 'message' => 'Site not found or inactive'], 404);
        }

        $archaeologicalSite->load([
            'translations',
            'subRegion.translations',
            'models3d' => function ($q) {
                $q->where('is_active', true)->orderBy('sort_order')->with('translations');
            },
        ]);

        $siteTranslation = $archaeologicalSite->translate($locale);
        $subRegionTranslation = $archaeologicalSite->subRegion?->translate($locale);
        
        return response()->json([
            'success' => true,
            'locale' => $locale,
            'data' => [
                'id' => $archaeologicalSite->id,
                'sub_region' => [
                    'id' => $archaeologicalSite->subRegion?->id,
                    'name' => $subRegionTranslation?->name ?? '',
                ],
                'name' => $siteTranslation?->name ?? '',
                'model_key' => ($archaeologicalSite->getTranslation('tr', false)?->name ?? ''),
                'description' => $siteTranslation?->description ?? '',
                'latitude' => $archaeologicalSite->latitude,
                'longitude' => $archaeologicalSite->longitude,
                'image' => $archaeologicalSite->image ? url('storage/' . $archaeologicalSite->image) : null,
                'models_3d' => $archaeologicalSite->models3d->map(function ($model) use ($archaeologicalSite, $locale) {
                    $modelTranslation = $model->translate($locale);
                    
                    return [
                        'id' => $model->id,
                        'name' => $modelTranslation?->name ?? '',
                        'description' => $modelTranslation?->description ?? '',
                        'sketchfab_model_id' => $model->sketchfab_model_id,
                        'thumbnail' => $model->sketchfab_thumbnail_url,
                        'sort_order' => $model->sort_order,
                        'audio_guide_path' => $archaeologicalSite->audio_guide_path ? url('storage/' . $archaeologicalSite->audio_guide_path) : null,
                    ];
                }),
            ],
        ]);
    }

    /**
     * Get archaeological sites by IDs with full data
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function getByIds(Request $request): JsonResponse
    {
        try {
            $locale = app()->getLocale();

            // Validate that ids parameter is provided and is an array
            $validated = $request->validate([
                'ids' => 'required|array|min:1',
                'ids.*' => 'integer'
            ]);

            $ids = $validated['ids'];

            if (empty($ids)) {
                return response()->json([
                    'success' => true,
                    'locale' => $locale,
                    'data' => []
                ]);
            }

            $sites = ArchaeologicalSite::whereIn('id', $ids)
                ->active()
                ->with([
                    'translations',
                    'subRegion.translations',
                    'subRegion.region.translations',
                    'models3d' => function ($q) {
                        $q->where('is_active', true)
                          ->orderBy('sort_order')
                          ->with('translations');
                    },
                    'audioGuides' => function ($q) {
                        $q->where('is_active', true)
                          ->with('translations');
                    },
                    'qrCodes' => function ($q) {
                        $q->where('is_active', true);
                    }
                ])
                ->get()
                ->sortBy(function ($site) use ($ids) {
                    return array_search($site->id, $ids);
                });

            $sitesData = $sites->map(function (ArchaeologicalSite $site) use ($locale) {
                $siteTranslation = $site->translate($locale);
                $subRegionTranslation = $site->subRegion?->translate($locale);
                $regionTranslation = $site->subRegion?->region?->translate($locale);
                
                return [
                    'id' => $site->id,
                    'sub_region' => [
                        'id' => $site->subRegion?->id,
                        'name' => $subRegionTranslation?->name ?? '',
                        'description' => $subRegionTranslation?->description ?? '',
                        'region' => [
                            'id' => $site->subRegion?->region?->id,
                            'name' => $regionTranslation?->name ?? '',
                            'description' => $regionTranslation?->description ?? '',
                        ],
                    ],
                    'name' => $siteTranslation?->name ?? '',
                    'model_key' => ($site->getTranslation('tr', false)?->name ?? ''),
                    'description' => $siteTranslation?->description ?? '',
                    'latitude' => $site->latitude,
                    'longitude' => $site->longitude,
                    'image' => $site->image ? url('storage/' . $site->image) : null,
                    'is_nearby_enabled' => $site->is_nearby_enabled,
                    'is_active' => $site->is_active,
                    'created_at' => $site->created_at,
                    'updated_at' => $site->updated_at,
                    'models_3d' => $site->models3d->map(function ($model) use ($site, $locale) {
                        $modelTranslation = $model->translate($locale);
                        
                        return [
                            'id' => $model->id,
                            'name' => $modelTranslation?->name ?? '',
                            'description' => $modelTranslation?->description ?? '',
                            'sketchfab_model_id' => $model->sketchfab_model_id,
                            'thumbnail' => $model->sketchfab_thumbnail_url,
                            'qr_uuid' => $model->qr_uuid,
                            'qr_image_path' => $model->qr_image_path ? url('storage/' . $model->qr_image_path) : null,
                            'sort_order' => $model->sort_order,
                            'is_active' => $model->is_active,
                            'created_at' => $model->created_at,
                            'updated_at' => $model->updated_at,
                            'audio_guide_path' => $site->audio_guide_path ? url('storage/' . $site->audio_guide_path) : null,
                        ];
                    })->toArray(),
                    'audio_guides' => $site->audioGuides->map(function ($audioGuide) use ($locale) {
                        $audioGuideTranslation = $audioGuide->translate($locale);
                        
                        return [
                            'id' => $audioGuide->id,
                            'name' => $audioGuideTranslation?->name ?? '',
                            'description' => $audioGuideTranslation?->description ?? '',
                            'audio_file_path' => $audioGuide->audio_file_path ? url('storage/' . $audioGuide->audio_file_path) : null,
                            'duration' => $audioGuide->duration,
                            'sort_order' => $audioGuide->sort_order,
                            'is_active' => $audioGuide->is_active,
                            'created_at' => $audioGuide->created_at,
                            'updated_at' => $audioGuide->updated_at,
                        ];
                    })->toArray(),
                    'qr_codes' => $site->qrCodes->map(function ($qrCode) {
                        return [
                            'id' => $qrCode->id,
                            'uuid' => $qrCode->uuid,
                            'qr_image_path' => $qrCode->qr_image_path ? url('storage/' . $qrCode->qr_image_path) : null,
                            'is_active' => $qrCode->is_active,
                            'created_at' => $qrCode->created_at,
                            'updated_at' => $qrCode->updated_at,
                        ];
                    })->toArray(),
                ];
            })->toArray();

            return response()->json([
                'success' => true,
                'locale' => $locale,
                'data' => $sitesData
            ]);
        } catch (\Exception $e) {
            \Log::error('ArchaeologicalSiteController::getByIds error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => true,
                'locale' => app()->getLocale(),
                'data' => []
            ]);
        }
    }
}


