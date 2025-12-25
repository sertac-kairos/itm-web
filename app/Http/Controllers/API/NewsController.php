<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\News;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class NewsController extends Controller
{
    /**
     * Haberler listesini getirir
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $locale = app()->getLocale();

            $query = News::with(['images', 'translations'])
                ->active()
                ->ordered();

            // Pagination
            $perPage = $request->get('per_page', 10);
            $perPage = min($perPage, 50); // Maksimum 50

            $news = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'locale' => $locale,
                'data' => $news->map(function (News $newsItem) use ($locale) {
                    $translation = $newsItem->translate($locale);
                    
                    return [
                        'id' => $newsItem->id,
                        'slug' => $newsItem->slug,
                        'title' => $translation?->title ?? '',
                        'content' => $translation?->content ?? '',
                        'news_date' => $newsItem->news_date?->format('Y-m-d'),
                        'featured_image' => $newsItem->featured_image ? url('storage/' . $newsItem->featured_image) : null,
                        'main_image' => $newsItem->main_image ? url('storage/' . $newsItem->main_image) : null,
                        'images' => $newsItem->images->map(function ($image) {
                            return [
                                'id' => $image->id,
                                'image_path' => $image->image_path ? url('storage/' . $image->image_path) : null,
                                'alt_text' => $image->alt_text,
                                'sort_order' => $image->sort_order,
                            ];
                        }),
                        'sort_order' => $newsItem->sort_order,
                        'is_active' => $newsItem->is_active,
                        'created_at' => $newsItem->created_at?->format('Y-m-d H:i:s'),
                        'updated_at' => $newsItem->updated_at?->format('Y-m-d H:i:s'),
                    ];
                }),
                'pagination' => [
                    'current_page' => $news->currentPage(),
                    'last_page' => $news->lastPage(),
                    'per_page' => $news->perPage(),
                    'total' => $news->total(),
                    'from' => $news->firstItem(),
                    'to' => $news->lastItem(),
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Haberler getirilirken bir hata oluştu.',
                'error' => config('app.debug') ? $e->getMessage() : 'Sunucu hatası'
            ], 500);
        }
    }

    /**
     * Tek bir haberi getirir
     *
     * @param Request $request
     * @param News $news
     * @return JsonResponse
     */
    public function show(Request $request, News $news): JsonResponse
    {
        try {
            $locale = app()->getLocale();

            if (!$news->is_active) {
                return response()->json([
                    'success' => false,
                    'message' => 'Haber bulunamadı.'
                ], 404);
            }

            $news->load(['images', 'translations']);

            return response()->json([
                'success' => true,
                'locale' => $locale,
                'data' => [
                    'id' => $news->id,
                    'slug' => $news->slug,
                    'title' => $news->title ?: '',
                    'content' => $news->content ?: '',
                    'news_date' => $news->news_date?->format('Y-m-d'),
                    'featured_image' => $news->featured_image ? url('storage/' . $news->featured_image) : null,
                    'main_image' => $news->main_image ? url('storage/' . $news->main_image) : null,
                    'images' => $news->images->map(function ($image) {
                        return [
                            'id' => $image->id,
                            'image_path' => $image->image_path ? url('storage/' . $image->image_path) : null,
                            'alt_text' => $image->alt_text,
                            'sort_order' => $image->sort_order,
                        ];
                    }),
                    'sort_order' => $news->sort_order,
                    'is_active' => $news->is_active,
                    'created_at' => $news->created_at?->format('Y-m-d H:i:s'),
                    'updated_at' => $news->updated_at?->format('Y-m-d H:i:s'),
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Haber getirilirken bir hata oluştu.',
                'error' => config('app.debug') ? $e->getMessage() : 'Sunucu hatası'
            ], 500);
        }
    }

    /**
     * ID'lere göre haberleri getirir
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getByIds(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'ids' => 'required|array',
                'ids.*' => 'integer|min:1'
            ]);

            $locale = app()->getLocale();

            $news = News::with(['images', 'translations'])
                ->whereIn('id', $validated['ids'])
                ->active()
                ->ordered()
                ->get();

            $newsData = $news->map(function (News $newsItem) {
                    return [
                        'id' => $newsItem->id,
                        'slug' => $newsItem->slug,
                        'title' => $newsItem->title ?: '',
                        'content' => $newsItem->content ?: '',
                        'news_date' => $newsItem->news_date?->format('Y-m-d'),
                        'featured_image' => $newsItem->featured_image ? url('storage/' . $newsItem->featured_image) : null,
                        'main_image' => $newsItem->main_image ? url('storage/' . $newsItem->main_image) : null,
                        'images' => $newsItem->images->map(function ($image) {
                            return [
                                'id' => $image->id,
                                'image_path' => $image->image_path ? url('storage/' . $image->image_path) : null,
                                'alt_text' => $image->alt_text,
                                'sort_order' => $image->sort_order,
                            ];
                        }),
                        'sort_order' => $newsItem->sort_order,
                        'is_active' => $newsItem->is_active,
                        'created_at' => $newsItem->created_at?->format('Y-m-d H:i:s'),
                        'updated_at' => $newsItem->updated_at?->format('Y-m-d H:i:s'),
                    ];
                })->toArray();
            
            return response()->json([
                'success' => true,
                'locale' => $locale,
                'data' => $newsData
            ], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => true,
                'locale' => app()->getLocale(),
                'data' => []
            ]);

        } catch (\Exception $e) {
            \Log::error('NewsController::getByIds error: ' . $e->getMessage(), [
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
