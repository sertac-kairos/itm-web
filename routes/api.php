<?php

use App\Http\Controllers\API\RegionController;
use App\Http\Controllers\API\SubRegionController;
use App\Http\Controllers\API\ArchaeologicalSiteController;
use App\Http\Controllers\API\Model3dController;
use App\Http\Controllers\API\OnboardingController;
use App\Http\Controllers\API\StoryController;
use App\Http\Controllers\API\ArticleController;
use App\Http\Controllers\API\MemoryController;
use App\Http\Controllers\API\DeviceController;
use App\Http\Controllers\API\SiteSettingController;
use App\Http\Controllers\API\SearchController;
use App\Http\Controllers\API\SupportRequestController;
use App\Http\Controllers\API\QrCodeController;
use App\Http\Controllers\API\NearbyArchaeologicalSitesController;
use App\Http\Controllers\API\NewsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Auth endpoint
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Public API endpoints for mobile app
Route::prefix('v1')->group(function () {
    
    // Regions & Sub-regions (Multilingual)
    Route::apiResource('regions', RegionController::class)->only(['index', 'show']);
    Route::post('regions/by-ids', [RegionController::class, 'getByIds']);
    Route::apiResource('sub-regions', SubRegionController::class)->only(['index', 'show']);
    Route::post('sub-regions/by-ids', [SubRegionController::class, 'getByIds']);
    Route::apiResource('archaeological-sites', ArchaeologicalSiteController::class)->only(['index', 'show']);
    Route::post('archaeological-sites/by-ids', [ArchaeologicalSiteController::class, 'getByIds']);
    Route::apiResource('models-3d', Model3dController::class)->only(['index', 'show'])->parameters(['models-3d' => 'model3d']);
    Route::post('models-3d/by-ids', [Model3dController::class, 'getByIds']);
    Route::get('onboarding-slides', [OnboardingController::class, 'index']);    
    Route::apiResource('stories', StoryController::class)->only(['index', 'show']);
    Route::apiResource('articles', ArticleController::class)->only(['index', 'show']);
    Route::post('articles/by-ids', [ArticleController::class, 'getByIds']);
    Route::apiResource('memories', MemoryController::class)->only(['index', 'show']);
    Route::post('memories/by-ids', [MemoryController::class, 'getByIds']);
    
    // Device Management - Single endpoint for device sync
    Route::post('devices/sync', [DeviceController::class, 'sync']);
    
    // 3D Model File URLs
    Route::get('models/{sketchfabId}/download', [Model3dController::class, 'getModelFileUrl']);
    
    // Site Settings endpoint - returns all settings in one response
    Route::get('site-settings', [SiteSettingController::class, 'index']);
    
    // Search endpoint - search across multiple entities
    Route::get('search', [SearchController::class, 'search']);

    // Support Requests - submit from app
    Route::post('support-requests', [SupportRequestController::class, 'store']);
    
    // QR Code scanning endpoint - single route for UUID scanning
    Route::get('qr/{uuid}', [QrCodeController::class, 'scanByUuid']);
    
    // Nearby Archaeological Sites endpoints
    Route::get('nearby-archaeological-sites', [NearbyArchaeologicalSitesController::class, 'index']);
    Route::post('calculate-distance', [NearbyArchaeologicalSitesController::class, 'calculateDistance']);
    
    // News endpoints
    Route::apiResource('news', NewsController::class)->only(['index', 'show']);
    Route::post('news/by-ids', [NewsController::class, 'getByIds']);
        
    // Language switching endpoint
    Route::get('locales', function () {
        return response()->json([
            'success' => true,
            'data' => [
                'available_locales' => config('translatable.locales'),
                'default_locale' => config('translatable.fallback_locale'),
                'current_locale' => app()->getLocale(),
            ]
        ]);
    });
    
    // Test endpoint for multilingual content
    Route::get('test-multilingual', function (Request $request) {
        $locale = $request->header('Accept-Language', 'tr');
        app()->setLocale($locale);
        
        return response()->json([
            'success' => true,
            'locale' => $locale,
            'message' => 'Çok dilli sistem çalışıyor!',
            'regions' => \App\Models\Region::with('translations')->get()->map(function ($region) {
                return [
                    'id' => $region->id,
                    'name' => $region->name,
                    'description' => $region->description,
                    'available_translations' => $region->translations->pluck('locale')->toArray(),
                ];
            }),
        ]);
    });
});

// Backward-compatible alias (without version prefix)
