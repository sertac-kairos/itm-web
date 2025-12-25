<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Memory;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class MemoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Memory::with(['translations'])->active();

        // Apply search filter if provided
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('translations', function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }

        // Apply link filter if provided
        if ($request->filled('has_link')) {
            if ($request->has_link === 'yes') {
                $query->whereNotNull('link')->where('link', '!=', '');
            } else {
                $query->whereNull('link')->orWhere('link', '');
            }
        }

        // Get memories ordered by sort order
        $memories = $query->ordered()->get();

        $memoriesData = $memories->map(function ($memory) {
            return [
                'id' => $memory->id,
                'image' => $memory->image_url,
                'link' => $memory->formatted_link,
                'has_link' => $memory->hasLink(),
                'author' => $memory->author,
                'sort_order' => $memory->sort_order,
                'is_active' => $memory->is_active,
                'created_at' => $memory->created_at->toISOString(),
                'updated_at' => $memory->updated_at->toISOString(),
                'title' => $memory->title ?: '',
                'content' => $memory->content ?: '',
                'locale' => app()->getLocale(),
                'available_translations' => $memory->translations->pluck('locale')->toArray(),
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $memoriesData,
            'meta' => [
                'total' => $memoriesData->count(),
                'locale' => app()->getLocale(),
            ]
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Memory $memory): JsonResponse
    {
        if (!$memory->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Memory not found or inactive',
            ], 404);
        }

        $memoryData = [
            'id' => $memory->id,
            'image' => $memory->image_url,
            'link' => $memory->formatted_link,
            'has_link' => $memory->hasLink(),
            'sort_order' => $memory->sort_order,
            'is_active' => $memory->is_active,
            'created_at' => $memory->created_at->toISOString(),
            'updated_at' => $memory->updated_at->toISOString(),
            'title' => $memory->title ?: '',
            'content' => $memory->content ?: '',
            'locale' => app()->getLocale(),
            'available_translations' => $memory->translations->pluck('locale')->toArray(),
        ];

            return response()->json([
                'success' => true,
                'data' => $memoryData,
                'meta' => [
                    'locale' => app()->getLocale(),
                ]
            ]);
    }

    /**
     * Get memories by IDs with full data
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
                    'data' => [],
                    'meta' => [
                        'total' => 0,
                        'locale' => $locale,
                    ]
                ]);
            }

            $memories = Memory::whereIn('id', $ids)
                ->active()
                ->with([
                    'translations'
                ])
                ->get()
                ->sortBy(function ($memory) use ($ids) {
                    return array_search($memory->id, $ids);
                });

            $memoriesData = $memories->map(function ($memory) use ($locale) {
                return [
                    'id' => $memory->id,
                    'image' => $memory->image_url,
                    'link' => $memory->formatted_link,
                    'has_link' => $memory->hasLink(),
                    'author' => $memory->author,
                    'sort_order' => $memory->sort_order,
                    'is_active' => $memory->is_active,
                    'created_at' => $memory->created_at->toISOString(),
                    'updated_at' => $memory->updated_at->toISOString(),
                    'title' => $memory->title ?: '',
                    'content' => $memory->content ?: '',
                    'locale' => $locale,
                    'available_translations' => $memory->translations->pluck('locale')->toArray(),
                ];
            })->toArray();

            return response()->json([
                'success' => true,
                'data' => $memoriesData,
                'meta' => [
                    'total' => count($memoriesData),
                    'locale' => $locale,
                ]
            ]);
        } catch (\Exception $e) {
            \Log::error('MemoryController::getByIds error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => true,
                'data' => [],
                'meta' => [
                    'total' => 0,
                    'locale' => app()->getLocale(),
                ]
            ]);
        }
    }
}
