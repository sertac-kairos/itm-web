<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\NearbyArchaeologicalSitesService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class NearbyArchaeologicalSitesController extends Controller
{
    protected $nearbySitesService;

    public function __construct(NearbyArchaeologicalSitesService $nearbySitesService)
    {
        $this->nearbySitesService = $nearbySitesService;
    }

    /**
     * Yakındaki arkeolojik siteleri getirir
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        try {
            // Get locale from request header or default to app locale
            $locale = $request->header('Accept-Language', app()->getLocale());
            app()->setLocale($locale);

            // Validasyon kuralları
            $validated = $request->validate([
                'latitude' => 'required|numeric|between:-90,90',
                'longitude' => 'required|numeric|between:-180,180',
                'limit' => 'sometimes|integer|min:1|max:50',
                'max_distance_km' => 'sometimes|numeric|min:0.1|max:1000',
            ]);

            $latitude = (float) $validated['latitude'];
            $longitude = (float) $validated['longitude'];
            $limit = $validated['limit'] ?? 10;
            $maxDistanceKm = $validated['max_distance_km'] ?? 50;

            // Yakındaki siteleri getir
            $sites = $this->nearbySitesService->getFormattedNearbySites(
                $latitude,
                $longitude,
                $limit,
                $maxDistanceKm
            );

            return response()->json([
                'success' => true,
                'locale' => $locale,
                'data' => $sites
            ], 200);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Geçersiz parametreler.',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Yakındaki arkeolojik siteler getirilirken bir hata oluştu.',
                'error' => config('app.debug') ? $e->getMessage() : 'Sunucu hatası'
            ], 500);
        }
    }

    /**
     * Mesafe hesaplama endpoint'i
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function calculateDistance(Request $request): JsonResponse
    {
        try {
            // Get locale from request header or default to app locale
            $locale = $request->header('Accept-Language', app()->getLocale());
            app()->setLocale($locale);

            $validated = $request->validate([
                'lat1' => 'required|numeric|between:-90,90',
                'lon1' => 'required|numeric|between:-180,180',
                'lat2' => 'required|numeric|between:-90,90',
                'lon2' => 'required|numeric|between:-180,180',
            ]);

            $distance = $this->nearbySitesService->calculateDistance(
                $validated['lat1'],
                $validated['lon1'],
                $validated['lat2'],
                $validated['lon2']
            );

            return response()->json([
                'success' => true,
                'locale' => $locale,
                'data' => [
                    'distance_km' => round($distance, 2),
                    'distance_m' => round($distance * 1000, 0),
                    'point1' => [
                        'latitude' => $validated['lat1'],
                        'longitude' => $validated['lon1']
                    ],
                    'point2' => [
                        'latitude' => $validated['lat2'],
                        'longitude' => $validated['lon2']
                    ]
                ]
            ], 200);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Geçersiz parametreler.',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Mesafe hesaplanırken bir hata oluştu.',
                'error' => config('app.debug') ? $e->getMessage() : 'Sunucu hatası'
            ], 500);
        }
    }
}
