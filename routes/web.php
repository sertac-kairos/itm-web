<?php

use App\Http\Controllers\RoutingController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\RegionController as AdminRegionController;
use App\Http\Controllers\Admin\SubRegionController;
use App\Http\Controllers\Admin\ArchaeologicalSiteController;
use App\Http\Controllers\Admin\Model3dController;
use App\Http\Controllers\Admin\OnboardingSlideController;
use App\Http\Controllers\Admin\StoryController;
use App\Http\Controllers\Admin\ArticleController;
use App\Http\Controllers\Admin\NewsController;
use App\Http\Controllers\Admin\MemoryController;
use App\Http\Controllers\Admin\DeviceController;
use App\Http\Controllers\Admin\SiteSettingController;
use App\Http\Controllers\Admin\StatisticsController;
use App\Http\Controllers\Admin\SupportRequestController as AdminSupportRequestController;
use App\Http\Controllers\SupportController;
use App\Http\Controllers\PrivacyPolicyController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

// Admin Routes
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->middleware(\App\Http\Middleware\RedirectIfNotAuthenticated::class)->name('dashboard');
    
    // Regions Management
    Route::resource('regions', AdminRegionController::class)->middleware(\App\Http\Middleware\RedirectIfNotAuthenticated::class);
    Route::post('regions/translate', [AdminRegionController::class, 'translate'])->middleware(\App\Http\Middleware\RedirectIfNotAuthenticated::class)->name('regions.translate');
    Route::post('regions/{region}/move-up', [AdminRegionController::class, 'moveUp'])->middleware(\App\Http\Middleware\RedirectIfNotAuthenticated::class)->name('regions.move-up');
    Route::post('regions/{region}/move-down', [AdminRegionController::class, 'moveDown'])->middleware(\App\Http\Middleware\RedirectIfNotAuthenticated::class)->name('regions.move-down');
    
    // Sub Regions Management  
    Route::resource('sub-regions', SubRegionController::class)->middleware(\App\Http\Middleware\RedirectIfNotAuthenticated::class);
    Route::post('sub-regions/translate', [SubRegionController::class, 'translate'])->middleware(\App\Http\Middleware\RedirectIfNotAuthenticated::class)->name('sub-regions.translate');
    Route::post('sub-regions/{subRegion}/move-up', [SubRegionController::class, 'moveUp'])->middleware(\App\Http\Middleware\RedirectIfNotAuthenticated::class)->name('sub-regions.move-up');
    Route::post('sub-regions/{subRegion}/move-down', [SubRegionController::class, 'moveDown'])->middleware(\App\Http\Middleware\RedirectIfNotAuthenticated::class)->name('sub-regions.move-down');
    
    // Archaeological Sites Management
    Route::resource('archaeological-sites', ArchaeologicalSiteController::class)->middleware(\App\Http\Middleware\RedirectIfNotAuthenticated::class);
    Route::get('archaeological-sites/get-sub-regions/{region}', [ArchaeologicalSiteController::class, 'getSubRegionsByRegion'])
        ->name('archaeological-sites.get-sub-regions');
    Route::post('archaeological-sites/translate', [ArchaeologicalSiteController::class, 'translate'])->middleware(\App\Http\Middleware\RedirectIfNotAuthenticated::class)->name('archaeological-sites.translate');
    
    // 3D Models Management
    Route::resource('models-3d', Model3dController::class)->middleware(\App\Http\Middleware\RedirectIfNotAuthenticated::class)
        ->parameters(['models-3d' => 'model3d']);
    Route::post('models-3d/{model3d}/generate-qr', [Model3dController::class, 'generateQr'])->middleware(\App\Http\Middleware\RedirectIfNotAuthenticated::class)
        ->name('models-3d.generate-qr');
    Route::post('models-3d/{model3d}/move-up', [Model3dController::class, 'moveUp'])->middleware(\App\Http\Middleware\RedirectIfNotAuthenticated::class)->name('models-3d.move-up');
    Route::post('models-3d/{model3d}/move-down', [Model3dController::class, 'moveDown'])->middleware(\App\Http\Middleware\RedirectIfNotAuthenticated::class)->name('models-3d.move-down');
    
    // Onboarding Slides Management
    Route::resource('onboarding-slides', OnboardingSlideController::class)->middleware(\App\Http\Middleware\RedirectIfNotAuthenticated::class);
    Route::post('onboarding-slides/{onboarding_slide}/move-up', [OnboardingSlideController::class, 'moveUp'])->middleware(\App\Http\Middleware\RedirectIfNotAuthenticated::class)->name('onboarding-slides.move-up');
    Route::post('onboarding-slides/{onboarding_slide}/move-down', [OnboardingSlideController::class, 'moveDown'])->middleware(\App\Http\Middleware\RedirectIfNotAuthenticated::class)->name('onboarding-slides.move-down');

    // Stories Management
    Route::resource('stories', StoryController::class)->middleware(\App\Http\Middleware\RedirectIfNotAuthenticated::class);
    Route::post('stories/translate', [StoryController::class, 'translate'])->middleware(\App\Http\Middleware\RedirectIfNotAuthenticated::class)->name('stories.translate');
    Route::post('stories/{story}/move-up', [StoryController::class, 'moveUp'])->middleware(\App\Http\Middleware\RedirectIfNotAuthenticated::class)->name('stories.move-up');
    Route::post('stories/{story}/move-down', [StoryController::class, 'moveDown'])->middleware(\App\Http\Middleware\RedirectIfNotAuthenticated::class)->name('stories.move-down');

    // Articles Management
    Route::resource('articles', ArticleController::class)->middleware(\App\Http\Middleware\RedirectIfNotAuthenticated::class);
    Route::delete('articles/{article}/images/{image}', [ArticleController::class, 'deleteImage'])->middleware(\App\Http\Middleware\RedirectIfNotAuthenticated::class)->name('articles.delete-image');
    Route::post('articles/translate', [ArticleController::class, 'translate'])->middleware(\App\Http\Middleware\RedirectIfNotAuthenticated::class)->name('articles.translate');
    Route::post('articles/{article}/move-up', [ArticleController::class, 'moveUp'])->middleware(\App\Http\Middleware\RedirectIfNotAuthenticated::class)->name('articles.move-up');
    Route::post('articles/{article}/move-down', [ArticleController::class, 'moveDown'])->middleware(\App\Http\Middleware\RedirectIfNotAuthenticated::class)->name('articles.move-down');

    // News Management
    Route::resource('news', NewsController::class)->middleware(\App\Http\Middleware\RedirectIfNotAuthenticated::class);
    Route::delete('news/{news}/images/{image}', [NewsController::class, 'deleteImage'])->middleware(\App\Http\Middleware\RedirectIfNotAuthenticated::class)->name('news.delete-image');
    Route::post('news/translate', [NewsController::class, 'translate'])->middleware(\App\Http\Middleware\RedirectIfNotAuthenticated::class)->name('news.translate');
    Route::post('news/{news}/move-up', [NewsController::class, 'moveUp'])->middleware(\App\Http\Middleware\RedirectIfNotAuthenticated::class)->name('news.move-up');
    Route::post('news/{news}/move-down', [NewsController::class, 'moveDown'])->middleware(\App\Http\Middleware\RedirectIfNotAuthenticated::class)->name('news.move-down');
    
    // Test DeepL service
    Route::get('test-deepl', function() {
        try {
            $service = new \App\Services\DeepLTranslationService();
            $result = $service->translateToEnglish('Merhaba dünya');
            return response()->json(['success' => true, 'translated' => $result]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()]);
        }
    })->middleware(\App\Http\Middleware\RedirectIfNotAuthenticated::class);

    // Memories Management
    Route::resource('memories', MemoryController::class)->middleware(\App\Http\Middleware\RedirectIfNotAuthenticated::class);
    Route::post('memories/translate', [MemoryController::class, 'translate'])->middleware(\App\Http\Middleware\RedirectIfNotAuthenticated::class)->name('memories.translate');
    Route::post('memories/{memory}/move-up', [MemoryController::class, 'moveUp'])->middleware(\App\Http\Middleware\RedirectIfNotAuthenticated::class)->name('memories.move-up');
    Route::post('memories/{memory}/move-down', [MemoryController::class, 'moveDown'])->middleware(\App\Http\Middleware\RedirectIfNotAuthenticated::class)->name('memories.move-down');

    // Devices Management
    Route::get('devices/notifications', [DeviceController::class, 'notifications'])->middleware(\App\Http\Middleware\RedirectIfNotAuthenticated::class)->name('devices.notifications');
    Route::post('devices/notifications/send-all', [DeviceController::class, 'sendToAll'])->middleware(\App\Http\Middleware\RedirectIfNotAuthenticated::class)->name('devices.send-all');
    Route::post('devices/notifications/send-location', [DeviceController::class, 'sendToLocation'])->middleware(\App\Http\Middleware\RedirectIfNotAuthenticated::class)->name('devices.send-location');
    Route::post('devices/notifications/send-group', [DeviceController::class, 'sendToGroup'])->middleware(\App\Http\Middleware\RedirectIfNotAuthenticated::class)->name('devices.send-group');
    Route::resource('devices', DeviceController::class)->only(['index', 'show'])->middleware(\App\Http\Middleware\RedirectIfNotAuthenticated::class);
    Route::get('devices/{device}/address', [DeviceController::class, 'getAddress'])->middleware(\App\Http\Middleware\RedirectIfNotAuthenticated::class)->name('devices.address');

    // Statistics
    Route::get('statistics', [StatisticsController::class, 'index'])->middleware(\App\Http\Middleware\RedirectIfNotAuthenticated::class)->name('statistics');

    // Site Settings
    Route::get('settings', [SiteSettingController::class, 'edit'])->middleware(\App\Http\Middleware\RedirectIfNotAuthenticated::class)->name('settings.edit');
    Route::put('settings', [SiteSettingController::class, 'update'])->middleware(\App\Http\Middleware\RedirectIfNotAuthenticated::class)->name('settings.update');

    // Support Requests
    Route::get('support-requests', [AdminSupportRequestController::class, 'index'])->middleware(\App\Http\Middleware\RedirectIfNotAuthenticated::class)->name('support-requests.index');
    Route::get('support-requests/{supportRequest}', [AdminSupportRequestController::class, 'show'])->middleware(\App\Http\Middleware\RedirectIfNotAuthenticated::class)->name('support-requests.show');
    Route::put('support-requests/{supportRequest}', [AdminSupportRequestController::class, 'update'])->middleware(\App\Http\Middleware\RedirectIfNotAuthenticated::class)->name('support-requests.update');
});

// Public welcome route
Route::get('/', function () {
    return view('welcome');
})->name('root');

// Public Support page
Route::get('/support', [SupportController::class, 'showForm'])->name('support.form');
Route::post('/support', [SupportController::class, 'submit'])->name('support.submit');

// Privacy Policy page
Route::get('/privacy-policy', [PrivacyPolicyController::class, 'show'])->name('privacy-policy');

// Redirect for live site (can be changed later to external URL)
Route::get('/live', function () {
    return redirect()->route('any', ['any' => 'home']);
})->name('live');

// Auth routes
Route::post('login', [AuthController::class, 'login'])->name('login.attempt');
Route::post('logout', [AuthController::class, 'logout'])->name('logout');

// Template wildcard routes should be last and more specific
Route::get('{first}/{second}/{third}', [RoutingController::class, 'thirdLevel'])
    ->where(['first' => '^(?!admin|api).*', 'second' => '[^/]+', 'third' => '[^/]+'])
    ->name('third');
    
Route::get('{first}/{second}', [RoutingController::class, 'secondLevel'])
    ->where(['first' => '^(?!admin|api).*', 'second' => '[^/]+'])
    ->name('second');
    
Route::get('{any}', [RoutingController::class, 'root'])
    ->where('any', '^(?!admin|api).*')
    ->name('any');
