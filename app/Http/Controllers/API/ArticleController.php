<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ArticleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Article::with(['translations', 'images'])->active();

        // Apply search filter if provided
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('translations', function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }

        // Apply author filter if provided
        if ($request->filled('author')) {
            $query->where('author', 'like', "%{$request->author}%");
        }

        // Get articles ordered by sort order
        $articles = $query->ordered()->get();

        // Transform data for API response
        $articlesData = $articles->map(function ($article) use ($request) {
            $locale = $request->header('Accept-Language', config('translatable.fallback_locale'));
            $translation = $article->translate($locale, false);
            
            return [
                'id' => $article->id,
                'author' => $article->author,
                'sort_order' => $article->sort_order,
                'is_active' => $article->is_active,
                'created_at' => $article->created_at->toISOString(),
                'updated_at' => $article->updated_at->toISOString(),
                'title' => $translation->title ?? null,
                'content' => $translation->content ?? null,
                'images' => $article->images->map(function ($image) {
                    return [
                        'id' => $image->id,
                        'url' => $image->image_url,
                        'alt_text' => $image->alt_text,
                        'sort_order' => $image->sort_order,
                    ];
                }),
                'images_count' => $article->images_count,
                'locale' => $locale,
                'available_translations' => $article->translations->pluck('locale')->toArray(),
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $articlesData,
            'meta' => [
                'total' => $articlesData->count(),
                'locale' => $request->header('Accept-Language', config('translatable.fallback_locale')),
            ]
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Article $article): JsonResponse
    {
        if (!$article->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Article not found or inactive',
            ], 404);
        }

        $locale = $request->header('Accept-Language', config('translatable.fallback_locale'));
        $translation = $article->translate($locale, false);

        $articleData = [
            'id' => $article->id,
            'author' => $article->author,
            'sort_order' => $article->sort_order,
            'is_active' => $article->is_active,
            'created_at' => $article->created_at->toISOString(),
            'updated_at' => $article->updated_at->toISOString(),
            'title' => $translation->title ?? null,
            'content' => $translation->content ?? null,
            'images' => $article->images->map(function ($image) {
                return [
                    'id' => $image->id,
                    'url' => $image->image_url,
                    'alt_text' => $image->alt_text,
                    'sort_order' => $image->sort_order,
                ];
            }),
            'images_count' => $article->images_count,
            'locale' => $locale,
            'available_translations' => $article->translations->pluck('locale')->toArray(),
        ];

        return response()->json([
            'success' => true,
            'data' => $articleData,
            'meta' => [
                'locale' => $locale,
            ]
        ]);
    }

    /**
     * Get articles by IDs with full data
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function getByIds(Request $request): JsonResponse
    {
        $locale = $request->header('Accept-Language', config('translatable.fallback_locale'));
        app()->setLocale($locale);

        // Validate that ids parameter is provided and is an array
        $request->validate([
            'ids' => 'required|array|min:1',
            'ids.*' => 'integer|exists:articles,id'
        ]);

        $ids = $request->input('ids');

        $articles = Article::whereIn('id', $ids)
            ->active()
            ->with([
                'translations',
                'images'
            ])
            ->get()
            ->sortBy(function ($article) use ($ids) {
                return array_search($article->id, $ids);
            });

            $articlesData = $articles->map(function ($article) use ($request) {
                $locale = $request->header('Accept-Language', config('translatable.fallback_locale'));
                $translation = $article->translate($locale, false);
                
                return [
                    'id' => $article->id,
                    'author' => $article->author,
                    'sort_order' => $article->sort_order,
                    'is_active' => $article->is_active,
                    'created_at' => $article->created_at->toISOString(),
                    'updated_at' => $article->updated_at->toISOString(),
                    'title' => $translation->title ?? null,
                    'content' => $translation->content ?? null,
                    'images' => $article->images->map(function ($image) {
                        return [
                            'id' => $image->id,
                            'url' => $image->image_url,
                            'alt_text' => $image->alt_text,
                            'sort_order' => $image->sort_order,
                        ];
                    }),
                    'images_count' => $article->images_count,
                    'locale' => $locale,
                    'available_translations' => $article->translations->pluck('locale')->toArray(),
                ];
            });
    
            return response()->json([
                'success' => true,
                'data' => $articlesData,
                'meta' => [
                    'total' => $articlesData->count(),
                    'locale' => $request->header('Accept-Language', config('translatable.fallback_locale')),
                ]
            ]);
    }
}
