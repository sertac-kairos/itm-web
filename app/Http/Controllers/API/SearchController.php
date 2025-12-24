<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Region;
use App\Models\SubRegion;
use App\Models\ArchaeologicalSite;
use App\Models\Article;
use App\Models\Memory;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class SearchController extends Controller
{
    /**
     * Search across multiple entities by title
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function search(Request $request): JsonResponse
    {
        $locale = $request->header('Accept-Language', config('translatable.fallback_locale'));
        app()->setLocale($locale);

        $request->validate([
            'q' => 'required|string|min:1|max:255'
        ]);

        $searchTerm = $request->input('q');
        $searchTerm = urldecode($searchTerm);
        $searchTerm = trim($searchTerm);
        $searchTerm = preg_replace('/\s+/', ' ', $searchTerm);
        
        if (strlen($searchTerm) < 2) {
            return response()->json([
                'success' => true,
                'locale' => $locale,
                'query' => $searchTerm,
                'counts' => [
                    'regions' => 0,
                    'sub_regions' => 0,
                    'archaeological_sites' => 0,
                    'articles' => 0,
                    'memories' => 0,
                    'total' => 0,
                ],
                'data' => [],
            ]);
        }

        // Search in regions
        $regions = Region::active()
            ->whereHas('translations', function ($query) use ($searchTerm) {
                $query->where('name', 'like', "%{$searchTerm}%")
                      ->orWhere('subtitle', 'like', "%{$searchTerm}%")
                      ->orWhere('description', 'like', "%{$searchTerm}%");
            })
            ->with(['translations'])
            ->get()
            ->map(function ($region) use ($locale) {
                $translation = $region->translate($locale, false);
                return [
                    'type' => 'region',
                    'id' => $region->id,
                    'name' => $translation->name ?? null,
                    'subtitle' => $translation->subtitle ?? null,
                    'description' => $translation->description ?? null,
                    'color_code' => $region->color_code,
                    'main_image' => $region->main_image ? url('storage/' . $region->main_image) : null,
                    'hotspot_image' => $region->hotspot_image,
                    'is_active' => $region->is_active,
                    'sort_order' => $region->sort_order,
                    'latitude' => $region->latitude,
                    'longitude' => $region->longitude,
                    'created_at' => $region->created_at,
                    'updated_at' => $region->updated_at,
                ];
            });

        // Search in sub-regions
        $subRegions = SubRegion::active()
            ->whereHas('translations', function ($query) use ($searchTerm) {
                $query->where('name', 'like', "%{$searchTerm}%")
                      ->orWhere('subtitle', 'like', "%{$searchTerm}%")
                      ->orWhere('description', 'like', "%{$searchTerm}%");
            })
            ->with(['translations', 'region.translations'])
            ->get()
            ->map(function ($subRegion) use ($locale) {
                $translation = $subRegion->translate($locale, false);
                $regionTranslation = $subRegion->region?->translate($locale, false);
                
                return [
                    'type' => 'sub_region',
                    'id' => $subRegion->id,
                    'region_id' => $subRegion->region_id,
                    'name' => $translation->name ?? null,
                    'subtitle' => $translation->subtitle ?? null,
                    'description' => $translation->description ?? null,
                    'latitude' => $subRegion->latitude,
                    'longitude' => $subRegion->longitude,
                    'image' => $subRegion->image ? url('storage/' . $subRegion->image) : null,
                    'is_active' => $subRegion->is_active,
                    'sort_order' => $subRegion->sort_order,
                    'created_at' => $subRegion->created_at,
                    'updated_at' => $subRegion->updated_at,
                    'region' => [
                        'id' => $subRegion->region?->id,
                        'name' => $regionTranslation->name ?? null,
                        'subtitle' => $regionTranslation->subtitle ?? null,
                        'description' => $regionTranslation->description ?? null,
                    ],
                ];
            });

        // Search in archaeological sites
        $archaeologicalSites = ArchaeologicalSite::active()
            ->whereHas('translations', function ($query) use ($searchTerm) {
                $query->where('name', 'like', "%{$searchTerm}%")
                      ->orWhere('description', 'like', "%{$searchTerm}%");
            })
            ->with(['translations', 'subRegion.translations', 'subRegion.region.translations'])
            ->get()
            ->map(function ($site) use ($locale) {
                $translation = $site->translate($locale, false);
                $subRegionTranslation = $site->subRegion?->translate($locale, false);
                $regionTranslation = $site->subRegion?->region?->translate($locale, false);
                
                return [
                    'type' => 'archaeological_site',
                    'id' => $site->id,
                    'sub_region_id' => $site->sub_region_id,
                    'name' => $translation->name ?? null,
                    'description' => $translation->description ?? null,
                    'latitude' => $site->latitude,
                    'longitude' => $site->longitude,
                    'image' => $site->image ? url('storage/' . $site->image) : null,
                    'is_nearby_enabled' => $site->is_nearby_enabled,
                    'is_active' => $site->is_active,
                    'created_at' => $site->created_at,
                    'updated_at' => $site->updated_at,
                    'sub_region' => [
                        'id' => $site->subRegion?->id,
                        'name' => $subRegionTranslation->name ?? null,
                        'description' => $subRegionTranslation->description ?? null,
                        'region' => [
                            'id' => $site->subRegion?->region?->id,
                            'name' => $regionTranslation->name ?? null,
                            'description' => $regionTranslation->description ?? null,
                        ],
                    ],
                ];
            });

        // Search in articles
        $articles = Article::active()
            ->whereHas('translations', function ($query) use ($searchTerm) {
                $query->where('title', 'like', "%{$searchTerm}%")
                      ->orWhere('content', 'like', "%{$searchTerm}%");
            })
            ->with(['translations', 'images'])
            ->get()
            ->map(function ($article) use ($locale) {
                $translation = $article->translate($locale, false);
                
                return [
                    'type' => 'article',
                    'id' => $article->id,
                    'author' => $article->author,
                    'title' => $translation->title ?? null,
                    'content' => $translation->content ?? null,
                    'main_image' => $article->main_image,
                    'main_image_path' => $article->main_image_path,
                    'images_count' => $article->images_count,
                    'sort_order' => $article->sort_order,
                    'is_active' => $article->is_active,
                    'created_at' => $article->created_at,
                    'updated_at' => $article->updated_at,
                    'images' => $article->images->map(function ($image) {
                        return [
                            'id' => $image->id,
                            'image_url' => $image->image_url,
                            'alt_text' => $image->alt_text,
                            'sort_order' => $image->sort_order,
                        ];
                    }),
                ];
            });

        // Search in memories
        $memories = Memory::active()
            ->whereHas('translations', function ($query) use ($searchTerm) {
                $query->where('title', 'like', "%{$searchTerm}%")
                      ->orWhere('content', 'like', "%{$searchTerm}%");
            })
            ->with(['translations'])
            ->get()
            ->map(function ($memory) use ($locale) {
                $translation = $memory->translate($locale, false);
                
                return [
                    'type' => 'memory',
                    'id' => $memory->id,
                    'title' => $translation->title ?? null,
                    'content' => $translation->content ?? null,
                    'image' => $memory->image_url,
                    'link' => $memory->formatted_link,
                    'has_link' => $memory->hasLink(),
                    'sort_order' => $memory->sort_order,
                    'is_active' => $memory->is_active,
                    'created_at' => $memory->created_at,
                    'updated_at' => $memory->updated_at,
                ];
            });

        // Combine all results
        $results = collect()
            ->merge($regions)
            ->merge($subRegions)
            ->merge($archaeologicalSites)
            ->merge($articles)
            ->merge($memories);

        // Count results by type
        $counts = [
            'regions' => $regions->count(),
            'sub_regions' => $subRegions->count(),
            'archaeological_sites' => $archaeologicalSites->count(),
            'articles' => $articles->count(),
            'memories' => $memories->count(),
            'total' => $results->count(),
        ];

        return response()->json([
            'success' => true,
            'locale' => $locale,
            'query' => $searchTerm,
            'counts' => $counts,
            'data' => $results->values(),
        ]);
    }
}
