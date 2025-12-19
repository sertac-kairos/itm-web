<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Region;
use App\Models\SubRegion;
use App\Models\ArchaeologicalSite;
use App\Models\BlogPost;
use App\Models\Reel;
use App\Models\OnboardingSlide;
use App\Models\QrCode;
use App\Models\Model3d;
use App\Models\Story;
use App\Models\Article;
use App\Models\Memory;
use App\Models\Device;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        // İstatistikler - Tabloların var olup olmadığını kontrol et
        $stats = [
            'regions_count' => $this->safeCount(Region::class),
            'active_regions_count' => $this->safeCount(Region::class, 'active'),
            'sub_regions_count' => $this->safeCount(SubRegion::class),
            'archaeological_sites_count' => $this->safeCount(ArchaeologicalSite::class),
            'blog_posts_count' => $this->safeCount(BlogPost::class),
            'reels_count' => $this->safeCount(Reel::class),
            'onboarding_slides_count' => $this->safeCount(OnboardingSlide::class),
            'stories_count' => $this->safeCount(Story::class),
            'articles_count' => $this->safeCount(Article::class),
            'memories_count' => $this->safeCount(Memory::class),
            'devices_count' => $this->safeCount(Device::class),
            'qr_codes_count' => $this->safeCount(QrCode::class),
            'models_3d_count' => $this->safeCount(Model3d::class),
        ];

        // Son eklenen içerikler
        $recent_regions = $this->safeRecentRecords(Region::class, ['translations']);
        $recent_sub_regions = $this->safeRecentRecords(SubRegion::class, ['translations', 'region']);

        return view('admin.dashboard', compact('stats', 'recent_regions', 'recent_sub_regions'));
    }

    /**
     * Güvenli count işlemi - tablo yoksa 0 döner
     */
    private function safeCount($modelClass, $scope = null)
    {
        try {
            if ($scope === 'active') {
                return $modelClass::active()->count();
            }
            return $modelClass::count();
        } catch (\Exception $e) {
            // Tablo henüz oluşturulmamışsa 0 döner
            return 0;
        }
    }

    /**
     * Güvenli recent records işlemi - tablo yoksa boş collection döner
     */
    private function safeRecentRecords($modelClass, $relations = [])
    {
        try {
            return $modelClass::with($relations)
                ->latest()
                ->take(5)
                ->get();
        } catch (\Exception $e) {
            // Tablo henüz oluşturulmamışsa boş collection döner
            return collect();
        }
    }
}
