<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Story;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class StoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Story::with(['translations', 'model3d.translations'])->active();

        // Apply search filter if provided
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('translations', function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Get stories ordered by sort order
        $stories = $query->ordered()->get();

        // Transform data for API response
        $storiesData = $stories->map(function ($story) use ($request) {
            $locale = $request->header('Accept-Language', config('translatable.fallback_locale'));
            $translation = $story->translate($locale, false);
            
            // Prepare 3D model data if exists
            $model3dData = null;
            if ($story->model3d) {
                $model3dTranslation = $story->model3d->translate($locale, false);
                $model3dData = [
                    'id' => $story->model3d->id,
                    'name' => $model3dTranslation->name ?? null,
                    'description' => $model3dTranslation->description ?? null,
                    'sketchfab_model_id' => $story->model3d->sketchfab_model_id,
                    'sketchfab_thumbnail_url' => $story->model3d->sketchfab_thumbnail_url,
                    'qr_uuid' => $story->model3d->qr_uuid,
                    'qr_image_path' => $story->model3d->qr_image_path ? asset('storage/' . $story->model3d->qr_image_path) : null,
                    'locale' => $locale,
                    'available_translations' => $story->model3d->translations->pluck('locale')->toArray(),
                ];
            }
            
            return [
                'id' => $story->id,
                'thumbnail' => $story->thumbnail_url,
                'sort_order' => $story->sort_order,
                'is_active' => $story->is_active,
                'created_at' => $story->created_at->toISOString(),
                'updated_at' => $story->updated_at->toISOString(),
                'title' => $translation->title ?? null,
                'description' => $translation->description ?? null,
                'image' => $translation && $translation->image ? asset('storage/' . $translation->image) : null,
                'locale' => $locale,
                'available_translations' => $story->translations->pluck('locale')->toArray(),
                'model_3d' => $model3dData,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $storiesData,
            'meta' => [
                'total' => $storiesData->count(),
                'locale' => $request->header('Accept-Language', config('translatable.fallback_locale')),
            ]
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Story $story): JsonResponse
    {
        if (!$story->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Story not found or inactive',
            ], 404);
        }

        $story->load(['translations', 'model3d.translations']);
        
        $locale = $request->header('Accept-Language', config('translatable.fallback_locale'));
        $translation = $story->translate($locale, false);

        // Prepare 3D model data if exists
        $model3dData = null;
        if ($story->model3d) {
            $model3dTranslation = $story->model3d->translate($locale, false);
            $model3dData = [
                'id' => $story->model3d->id,
                'name' => $model3dTranslation->name ?? null,
                'description' => $model3dTranslation->description ?? null,
                'sketchfab_model_id' => $story->model3d->sketchfab_model_id,
                'sketchfab_thumbnail_url' => $story->model3d->sketchfab_thumbnail_url,
                'qr_uuid' => $story->model3d->qr_uuid,
                'qr_image_path' => $story->model3d->qr_image_path ? asset('storage/' . $story->model3d->qr_image_path) : null,
                'locale' => $locale,
                'available_translations' => $story->model3d->translations->pluck('locale')->toArray(),
            ];
        }

        $storyData = [
            'id' => $story->id,
            'thumbnail' => $story->thumbnail_url,
            'sort_order' => $story->sort_order,
            'is_active' => $story->is_active,
            'created_at' => $story->created_at->toISOString(),
            'updated_at' => $story->updated_at->toISOString(),
            'title' => $translation->title ?? null,
            'description' => $translation->description ?? null,
            'image' => $translation && $translation->image ? asset('storage/' . $translation->image) : null,
            'locale' => $locale,
            'available_translations' => $story->translations->pluck('locale')->toArray(),
            'model_3d' => $model3dData,
        ];

        return response()->json([
            'success' => true,
            'data' => $storyData,
            'meta' => [
                'locale' => $locale,
            ]
        ]);
    }
}
