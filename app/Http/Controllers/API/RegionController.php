<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Region;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RegionController extends Controller
{
    /**
     * Display a listing of all active regions with translations
     */
    public function index(Request $request): JsonResponse
    {
        // Get locale from request header or default to app locale
        $locale = app()->getLocale();
        
        // Set application locale for this request

        $regions = Region::active()
            ->ordered()
            ->with([
                'translations',
                'subRegions' => function ($query) {
                    $query->active()->ordered()->with('translations');
                }
            ])
            ->get();

        return response()->json([
            'success' => true,
            'locale' => $locale,
            'data' => $regions->map(function ($region) use ($locale) {
                $translation = $region->translate($locale);
                
                return [
                    'id' => $region->id,
                    'name' => $translation?->name ?? '',
                    'subtitle' => $translation?->subtitle ?? '',
                    'description' => $translation?->description ?? '',
                    'color_code' => $region->color_code,
                    'main_image' => $region->main_image ? url('storage/' . $region->main_image) : null,
                    'hotspot_image' => $region->hotspot_image,
                    'audio_guide' => $region->audio_guide_path ? url('storage/' . $region->audio_guide_path) : null,
                    'sort_order' => $region->sort_order,
                    'latitude' => $region->latitude,
                    'longitude' => $region->longitude,  
                    'sub_regions_count' => $region->subRegions->count(),
                    'sub_regions' => $region->subRegions->map(function ($subRegion) use ($locale) {
                        $subTranslation = $subRegion->translate($locale);
                        
                        return [
                            'id' => $subRegion->id,
                            'name' => $subTranslation?->name ?? '',
                            'subtitle' => $subTranslation?->subtitle ?? '',
                            'description' => $subTranslation?->description ?? '',
                            'latitude' => $subRegion->latitude,
                            'longitude' => $subRegion->longitude,
                            'image' => $subRegion->image ? url('storage/' . $subRegion->image) : null,
                            'audio_guide' => $subRegion->audio_guide_path ? url('storage/' . $subRegion->audio_guide_path) : null,
                        ];
                    }),
                ];
            }),
        ]);
    }

    /**
     * Display the specified region with detailed information
     */
    public function show(Request $request, Region $region): JsonResponse
    {
        // Get locale from request header
        $locale = app()->getLocale();

        if (!$region->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Region not found or inactive'
            ], 404);
        }

        $region->load([
            'translations',
            'subRegions' => function ($query) {
                $query->active()->ordered()->with([
                    'translations',
                    'archaeologicalSites',
                    'models3d',
                    'audioGuides'
                ]);
            },
            // Onboarding slides are now independent; remove eager loading from region
        ]);

        $translation = $region->translate($locale);
        
        return response()->json([
            'success' => true,
            'locale' => $locale,
            'data' => [
                'id' => $region->id,
                'name' => $translation?->name ?? '',
                'subtitle' => $translation?->subtitle ?? '',
                'description' => $translation?->description ?? '',
                'color_code' => $region->color_code,
                'main_image' => $region->main_image ? url('storage/' . $region->main_image) : null,
                'hotspot_image' => $region->hotspot_image,
                'audio_guide' => $region->audio_guide_path ? url('storage/' . $region->audio_guide_path) : null,
                // Onboarding slides no longer tied to region
                'sub_regions' => $region->subRegions->map(function ($subRegion) use ($locale) {
                    $subTranslation = $subRegion->translate($locale);
                    
                    return [
                        'id' => $subRegion->id,
                        'name' => $subTranslation?->name ?? '',
                        'subtitle' => $subTranslation?->subtitle ?? '',
                        'description' => $subTranslation?->description ?? '',
                        'latitude' => $subRegion->latitude,
                        'longitude' => $subRegion->longitude,
                        'image' => $subRegion->image ? url('storage/' . $subRegion->image) : null,
                        'audio_guide' => $subRegion->audio_guide_path ? url('storage/' . $subRegion->audio_guide_path) : null,
                        'archaeological_sites_count' => $subRegion->archaeologicalSites->where('is_active', true)->count(),
                        'models_3d_count' => $subRegion->models3d->where('is_active', true)->count(),
                        'has_audio_guide' => $subRegion->audioGuides->where('is_active', true)->count() > 0,
                    ];
                }),
            ],
        ]);
    }

    /**
     * Get regions by IDs with full data (same format as index)
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function getByIds(Request $request): JsonResponse
    {
        $locale = app()->getLocale();

        // Validate that ids parameter is provided and is an array
        $request->validate([
            'ids' => 'required|array|min:1',
            'ids.*' => 'integer|exists:regions,id'
        ]);

        $ids = $request->input('ids');

        $regions = Region::whereIn('id', $ids)
            ->active()
            ->ordered()
            ->with([
                'translations',
                'subRegions' => function ($query) {
                    $query->active()->ordered()->with('translations');
                }
            ])
            ->get()
            ->sortBy(function ($region) use ($ids) {
                return array_search($region->id, $ids);
            });

        return response()->json([
            'success' => true,
            'locale' => $locale,
            'data' => $regions->map(function ($region) use ($locale) {
                $translation = $region->translate($locale);
                
                return [
                    'id' => $region->id,
                    'name' => $translation?->name ?? '',
                    'subtitle' => $translation?->subtitle ?? '',
                    'description' => $translation?->description ?? '',
                    'color_code' => $region->color_code,
                    'main_image' => $region->main_image ? url('storage/' . $region->main_image) : null,
                    'hotspot_image' => $region->hotspot_image,
                    'audio_guide' => $region->audio_guide_path ? url('storage/' . $region->audio_guide_path) : null,
                    'sort_order' => $region->sort_order,
                    'latitude' => $region->latitude,
                    'longitude' => $region->longitude,  
                    'sub_regions_count' => $region->subRegions->count(),
                    'sub_regions' => $region->subRegions->map(function ($subRegion) use ($locale) {
                        $subTranslation = $subRegion->translate($locale);
                        
                        return [
                            'id' => $subRegion->id,
                            'name' => $subTranslation?->name ?? '',
                            'subtitle' => $subTranslation?->subtitle ?? '',
                            'description' => $subTranslation?->description ?? '',
                            'latitude' => $subRegion->latitude,
                            'longitude' => $subRegion->longitude,
                            'image' => $subRegion->image ? url('storage/' . $subRegion->image) : null,
                            'audio_guide' => $subRegion->audio_guide_path ? url('storage/' . $subRegion->audio_guide_path) : null,
                        ];
                    }),
                ];
            }),
        ]);
    }
}
