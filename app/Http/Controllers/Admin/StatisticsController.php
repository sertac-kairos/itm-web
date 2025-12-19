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
use Carbon\Carbon;

class StatisticsController extends Controller
{
    public function index(): View
    {
        // Temel istatistikler
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

        // Yeni istatistikler
        $advancedStats = [
            'active_users_last_hour' => $this->getActiveUsersLastHour(),
            'active_users_last_24h' => $this->getActiveUsersLast24Hours(),
            'total_active_users' => $this->getTotalActiveUsers(),
            'new_devices_today' => $this->getNewDevicesToday(),
            'new_devices_this_week' => $this->getNewDevicesThisWeek(),
            'new_devices_this_month' => $this->getNewDevicesThisMonth(),
            'content_created_today' => $this->getContentCreatedToday(),
            'content_created_this_week' => $this->getContentCreatedThisWeek(),
            'content_created_this_month' => $this->getContentCreatedThisMonth(),
            'most_active_regions' => $this->getMostActiveRegions(),
            'recent_activity' => $this->getRecentActivity(),
        ];

        // Grafik verileri
        $chartData = [
            'devices_over_time' => $this->getDevicesOverTime(),
            'content_creation_trend' => $this->getContentCreationTrend(),
            'user_activity_by_hour' => $this->getUserActivityByHour(),
        ];

        return view('admin.statistics.index', compact('stats', 'advancedStats', 'chartData'));
    }

    /**
     * Son 1 saat içinde aktif olan kullanıcılar
     */
    private function getActiveUsersLastHour()
    {
        try {
            return Device::where('updated_at', '>=', Carbon::now()->subHour())->count();
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Son 24 saat içinde aktif olan kullanıcılar
     */
    private function getActiveUsersLast24Hours()
    {
        try {
            return Device::where('updated_at', '>=', Carbon::now()->subDay())->count();
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Toplam aktif kullanıcılar (son 7 gün)
     */
    private function getTotalActiveUsers()
    {
        try {
            return Device::where('updated_at', '>=', Carbon::now()->subWeek())->count();
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Bugün kayıt olan yeni cihazlar
     */
    private function getNewDevicesToday()
    {
        try {
            return Device::whereDate('created_at', Carbon::today())->count();
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Bu hafta kayıt olan yeni cihazlar
     */
    private function getNewDevicesThisWeek()
    {
        try {
            return Device::where('created_at', '>=', Carbon::now()->startOfWeek())->count();
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Bu ay kayıt olan yeni cihazlar
     */
    private function getNewDevicesThisMonth()
    {
        try {
            return Device::where('created_at', '>=', Carbon::now()->startOfMonth())->count();
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Bugün oluşturulan içerik sayısı
     */
    private function getContentCreatedToday()
    {
        try {
            $today = Carbon::today();
            return Region::whereDate('created_at', $today)->count() +
                   SubRegion::whereDate('created_at', $today)->count() +
                   ArchaeologicalSite::whereDate('created_at', $today)->count() +
                   Story::whereDate('created_at', $today)->count() +
                   Article::whereDate('created_at', $today)->count() +
                   Memory::whereDate('created_at', $today)->count();
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Bu hafta oluşturulan içerik sayısı
     */
    private function getContentCreatedThisWeek()
    {
        try {
            $weekStart = Carbon::now()->startOfWeek();
            return Region::where('created_at', '>=', $weekStart)->count() +
                   SubRegion::where('created_at', '>=', $weekStart)->count() +
                   ArchaeologicalSite::where('created_at', '>=', $weekStart)->count() +
                   Story::where('created_at', '>=', $weekStart)->count() +
                   Article::where('created_at', '>=', $weekStart)->count() +
                   Memory::where('created_at', '>=', $weekStart)->count();
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Bu ay oluşturulan içerik sayısı
     */
    private function getContentCreatedThisMonth()
    {
        try {
            $monthStart = Carbon::now()->startOfMonth();
            return Region::where('created_at', '>=', $monthStart)->count() +
                   SubRegion::where('created_at', '>=', $monthStart)->count() +
                   ArchaeologicalSite::where('created_at', '>=', $monthStart)->count() +
                   Story::where('created_at', '>=', $monthStart)->count() +
                   Article::where('created_at', '>=', $monthStart)->count() +
                   Memory::where('created_at', '>=', $monthStart)->count();
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * En aktif bölgeler (cihaz sayısına göre)
     */
    private function getMostActiveRegions()
    {
        try {
            return Region::withCount('subRegions')
                ->withCount(['subRegions as archaeological_sites_count' => function($query) {
                    $query->withCount('archaeologicalSites');
                }])
                ->orderBy('sub_regions_count', 'desc')
                ->take(5)
                ->get();
        } catch (\Exception $e) {
            return collect();
        }
    }

    /**
     * Son aktiviteler
     */
    private function getRecentActivity()
    {
        try {
            $activities = collect();
            
            // Son eklenen bölgeler
            $recentRegions = Region::with('translations')
                ->latest()
                ->take(3)
                ->get()
                ->map(function($region) {
                    return [
                        'type' => 'region',
                        'name' => $region->name,
                        'created_at' => $region->created_at,
                        'icon' => 'mdi-map-marker-multiple'
                    ];
                });
            
            // Son eklenen ören yerleri
            $recentSites = ArchaeologicalSite::with(['translations', 'subRegion.region'])
                ->latest()
                ->take(3)
                ->get()
                ->map(function($site) {
                    return [
                        'type' => 'archaeological_site',
                        'name' => $site->name,
                        'created_at' => $site->created_at,
                        'icon' => 'mdi-ancient-sword'
                    ];
                });
            
            // Son eklenen hikayeler
            $recentStories = Story::with('translations')
                ->latest()
                ->take(3)
                ->get()
                ->map(function($story) {
                    return [
                        'type' => 'story',
                        'name' => $story->name,
                        'created_at' => $story->created_at,
                        'icon' => 'mdi-book-open-page-variant'
                    ];
                });
            
            return $activities->merge($recentRegions)
                ->merge($recentSites)
                ->merge($recentStories)
                ->sortByDesc('created_at')
                ->take(10);
        } catch (\Exception $e) {
            return collect();
        }
    }

    /**
     * Cihazların zaman içindeki dağılımı
     */
    private function getDevicesOverTime()
    {
        try {
            $data = [];
            for ($i = 6; $i >= 0; $i--) {
                $date = Carbon::now()->subDays($i);
                $count = Device::whereDate('created_at', $date)->count();
                $data[] = [
                    'date' => $date->format('Y-m-d'),
                    'count' => $count
                ];
            }
            return $data;
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * İçerik oluşturma trendi
     */
    private function getContentCreationTrend()
    {
        try {
            $data = [];
            for ($i = 6; $i >= 0; $i--) {
                $date = Carbon::now()->subDays($i);
                $count = Region::whereDate('created_at', $date)->count() +
                        SubRegion::whereDate('created_at', $date)->count() +
                        ArchaeologicalSite::whereDate('created_at', $date)->count() +
                        Story::whereDate('created_at', $date)->count() +
                        Article::whereDate('created_at', $date)->count() +
                        Memory::whereDate('created_at', $date)->count();
                $data[] = [
                    'date' => $date->format('Y-m-d'),
                    'count' => $count
                ];
            }
            return $data;
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Saatlik kullanıcı aktivitesi
     */
    private function getUserActivityByHour()
    {
        try {
            $data = [];
            for ($i = 23; $i >= 0; $i--) {
                $hour = Carbon::now()->subHours($i);
                $count = Device::where('updated_at', '>=', $hour->startOfHour())
                    ->where('updated_at', '<=', $hour->endOfHour())
                    ->count();
                $data[] = [
                    'hour' => $hour->format('H:i'),
                    'count' => $count
                ];
            }
            return $data;
        } catch (\Exception $e) {
            return [];
        }
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
            return 0;
        }
    }
}