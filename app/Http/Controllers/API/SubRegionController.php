<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\SubRegion;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SubRegionController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $locale = $request->header('Accept-Language', app()->getLocale());
        app()->setLocale($locale);

        $query = SubRegion::query()->active()->ordered()
            ->with(['translations', 'region.translations', 'archaeologicalSites' => function ($q) {
                $q->active()->with('translations')->with(['models3d' => function ($q) {
                    $q->where('is_active', true)->orderBy('sort_order')->with('translations');
                }]);
            }]);

        if ($request->filled('region_id')) {
            $query->where('region_id', $request->integer('region_id'));
        }

        $subRegions = $query->get();

        return response()->json([
            'success' => true,
            'locale' => $locale,
            'data' => $subRegions->map(function (SubRegion $subRegion) {
                return [
                    'id' => $subRegion->id,
                    'region_id' => $subRegion->region_id,
                    'region_name' => $subRegion->region?->name,
                    'name' => $subRegion->name,
                    'subtitle' => $subRegion->subtitle,
                    'description' => $subRegion->description,
                    'latitude' => $subRegion->latitude,
                    'longitude' => $subRegion->longitude,
                    'image' => $subRegion->image ? url('storage/' . $subRegion->image) : null,
                    'color' => $subRegion->color ?? '#1a4a9f',
                    'audio_guide' => $subRegion->audio_guide_path ? url('storage/' . $subRegion->audio_guide_path) : null,
                    'sort_order' => $subRegion->sort_order,
                    'archaeological_sites' => $subRegion->archaeologicalSites->map(function ($site) {
                        return [
                            'id' => $site->id,
                            'name' => $site->name,
                            'description' => $site->description,
                            'latitude' => $site->latitude,
                            'longitude' => $site->longitude,
                            'image' => $site->image ? url('storage/' . $site->image) : null,
                            'models_3d' => $site->models3d->map(function ($model) {
                                return [
                                    'id' => $model->id,
                                    'name' => $model->name,
                                    'description' => $model->description,
                                    'sketchfab_model_id' => $model->sketchfab_model_id,
                                    'thumbnail' => $model->sketchfab_thumbnail_url,
                                    'sort_order' => $model->sort_order,
                                ];
                            }),
                        ];
                    }),
                ];
            }),
        ]);
    }

    public function show(Request $request, SubRegion $subRegion): JsonResponse
    {
        $locale = $request->header('Accept-Language', app()->getLocale());
        app()->setLocale($locale);

        if (!$subRegion->is_active) {
            return response()->json(['success' => false, 'message' => 'Sub-region not found or inactive'], 404);
        }

        $subRegion->load([
            'translations',
            'region.translations',
            'archaeologicalSites' => function ($q) {
                $q->active()->with('translations')->with(['models3d' => function ($q) {
                    $q->where('is_active', true)->orderBy('sort_order')->with('translations');
                }]);
            },
            'audioGuides' => function ($q) {
                $q->where('is_active', true)->with('translations');
            },
        ]);

        return response()->json([
            'success' => true,
            'locale' => $locale,
            'data' => [
                'id' => $subRegion->id,
                'region' => [
                    'id' => $subRegion->region?->id,
                    'name' => $subRegion->region?->name,
                ],
                'name' => $subRegion->name,
                'subtitle' => $subRegion->subtitle,
                'description' => $subRegion->description,
                'latitude' => $subRegion->latitude,
                'longitude' => $subRegion->longitude,
                'image' => $subRegion->image ? url('storage/' . $subRegion->image) : null,
                'color' => $subRegion->color ?? '#1a4a9f',
                'audio_guide' => $subRegion->audio_guide_path ? url('storage/' . $subRegion->audio_guide_path) : null,
                'sort_order' => $subRegion->sort_order,
                'archaeological_sites' => $subRegion->archaeologicalSites->map(function ($site) {
                    return [
                        'id' => $site->id,
                        'name' => $site->name,
                        'description' => $site->description,
                        'latitude' => $site->latitude,
                        'longitude' => $site->longitude,
                        'image' => $site->image ? url('storage/' . $site->image) : null,
                        'models_3d' => $site->models3d->map(function ($model) {
                            return [
                                'id' => $model->id,
                                'name' => $model->name,
                                'description' => $model->description,
                                'sketchfab_model_id' => $model->sketchfab_model_id,
                                'thumbnail' => $model->sketchfab_thumbnail_url,
                                'sort_order' => $model->sort_order,
                            ];
                        }),
                    ];
                }),
            ],
        ]);
    }

    /**
     * Get sub-regions by IDs with full data (same format as index)
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function getByIds(Request $request): JsonResponse
    {
        $locale = $request->header('Accept-Language', app()->getLocale());
        app()->setLocale($locale);

        // Validate that ids parameter is provided and is an array
        $request->validate([
            'ids' => 'required|array|min:1',
            'ids.*' => 'integer|exists:sub_regions,id'
        ]);

        $ids = $request->input('ids');

        $subRegions = SubRegion::whereIn('id', $ids)
            ->active()
            ->ordered()
            ->with([
                'translations', 
                'region.translations', 
                'archaeologicalSites' => function ($q) {
                    $q->active()->with('translations')->with(['models3d' => function ($q) {
                        $q->where('is_active', true)->orderBy('sort_order')->with('translations');
                    }]);
                }
            ])
            ->get()
            ->sortBy(function ($subRegion) use ($ids) {
                return array_search($subRegion->id, $ids);
            });

        return response()->json([
            'success' => true,
            'locale' => $locale,
            'data' => $subRegions->map(function (SubRegion $subRegion) {
                return [
                    'id' => $subRegion->id,
                    'region_id' => $subRegion->region_id,
                    'region_name' => $subRegion->region?->name,
                    'name' => $subRegion->name,
                    'subtitle' => $subRegion->subtitle,
                    'description' => $subRegion->description,
                    'latitude' => $subRegion->latitude,
                    'longitude' => $subRegion->longitude,
                    'image' => $subRegion->image ? url('storage/' . $subRegion->image) : null,
                    'color' => $subRegion->color ?? '#1a4a9f',
                    'audio_guide' => $subRegion->audio_guide_path ? url('storage/' . $subRegion->audio_guide_path) : null,
                    'sort_order' => $subRegion->sort_order,
                    'archaeological_sites' => $subRegion->archaeologicalSites->map(function ($site) {
                        return [
                            'id' => $site->id,
                            'name' => $site->name,
                            'description' => $site->description,
                            'latitude' => $site->latitude,
                            'longitude' => $site->longitude,
                            'image' => $site->image ? url('storage/' . $site->image) : null,
                            'models_3d' => $site->models3d->map(function ($model) {
                                return [
                                    'id' => $model->id,
                                    'name' => $model->name,
                                    'description' => $model->description,
                                    'sketchfab_model_id' => $model->sketchfab_model_id,
                                    'thumbnail' => $model->sketchfab_thumbnail_url,
                                    'sort_order' => $model->sort_order,
                                ];
                            }),
                        ];
                    }),
                ];
            }),
        ]);
    }
}


