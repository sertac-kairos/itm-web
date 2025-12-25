<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Device;
use App\Services\FCMService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DeviceController extends Controller
{
    protected $fcmService;

    public function __construct(FCMService $fcmService)
    {
        $this->fcmService = $fcmService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $query = Device::query();

        if ($request->filled('status')) {
            $request->status === 'active'
                ? $query->where('is_active', true)
                : ($request->status === 'inactive' ? $query->where('is_active', false) : null);
        }

        if ($request->filled('platform')) {
            $query->where('platform', $request->platform);
        }

        if ($request->filled('online_status')) {
            if ($request->online_status === 'online') {
                $query->where('last_seen', '>=', now()->subDay());
            } else {
                $query->where(function ($q) {
                    $q->whereNull('last_seen')
                      ->orWhere('last_seen', '<', now()->subDay());
                });
            }
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('device_id', 'like', "%{$search}%")
                  ->orWhere('gcm_id', 'like', "%{$search}%")
                  ->orWhere('platform', 'like', "%{$search}%");
            });
        }

        // Sorting
        $sortField = $request->get('sort', 'last_seen');
        $sortDirection = $request->get('direction', 'desc');
        
        if (in_array($sortField, ['id', 'device_id', 'gcm_id', 'platform', 'latitude', 'longitude', 'last_seen', 'created_at'])) {
            $query->orderBy($sortField, $sortDirection);
        } else {
            $query->orderBy('last_seen', 'desc');
        }

        $devices = $query->paginate(15)->withQueryString();

        return view('admin.devices.index', compact('devices'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Device $device): View
    {
        return view('admin.devices.show', compact('device'));
    }

    /**
     * Get address for a device via AJAX.
     */
    public function getAddress(Device $device)
    {
        try {
            if (!$device->hasLocation()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Device has no location data'
                ]);
            }

            $address = $device->address;
            
            return response()->json([
                'success' => true,
                'address' => $address,
                'coordinates' => [
                    'latitude' => $device->latitude,
                    'longitude' => $device->longitude
                ]
            ]);
            
        } catch (\Exception $e) {
            \Log::error("Failed to get address for device {$device->id}: " . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to get address'
            ], 500);
        }
    }

    /**
     * Get device statistics for dashboard.
     */
    public function getStats(): array
    {
        $totalDevices = Device::count();
        $activeDevices = Device::active()->count();
        $onlineDevices = Device::active()->where('last_seen', '>=', now()->subDay())->count();
        
        $platformStats = Device::selectRaw('platform, COUNT(*) as count')
            ->groupBy('platform')
            ->get()
            ->pluck('count', 'platform');

        $recentDevices = Device::where('last_seen', '>=', now()->subDays(7))->count();

        return [
            'total_devices' => $totalDevices,
            'active_devices' => $activeDevices,
            'online_devices' => $onlineDevices,
            'recent_devices' => $recentDevices,
            'platform_distribution' => $platformStats,
        ];
    }

    /**
     * Show notification sending page.
     */
    public function notifications(): View
    {
        $deviceStats = $this->getStats();
        $userGroups = $this->getUserGroups();
        return view('admin.devices.notifications', compact('deviceStats', 'userGroups'));
    }

    /**
     * Send notification to all devices.
     */
    public function sendToAll(Request $request): RedirectResponse
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string|max:1000',
            'data' => 'nullable|string|max:500',
        ]);

        $devices = Device::whereNotNull('gcm_id')->get();
        
        if ($devices->isEmpty()) {
            return redirect()->route('admin.devices.notifications')
                ->with('error', 'Bildirim gönderebilecek aktif cihaz bulunamadı.');
        }

        // Parse additional data if provided
        $additionalData = [];
        if ($request->filled('data')) {
            $additionalData = json_decode($request->data, true) ?? [];
        }

        // Get all FCM tokens
        $tokens = $devices->pluck('gcm_id')->toArray();

        try {
            // Send notifications using FCM service
            $results = $this->fcmService->sendToMultipleDevices(
                $tokens,
                $request->title,
                $request->message,
                $additionalData
            );

            $message = "Bildirim gönderimi tamamlandı. Başarılı: {$results['success']}, Başarısız: {$results['failed']}";
            
            if ($results['failed'] > 0) {
                $message .= " Hata detayları log dosyasında görülebilir.";
            }

            return redirect()->route('admin.devices.notifications')->with('success', $message);

        } catch (\Exception $e) {
            \Log::error('FCM Service Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('admin.devices.notifications')
                ->with('error', 'Bildirim gönderimi sırasında hata oluştu: ' . $e->getMessage());
        }
    }


    /**
     * Send notification to devices in location radius.
     */
    public function sendToLocation(Request $request): RedirectResponse
    {
        $request->validate([
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'radius' => 'required|numeric|min:100|max:50000', // 100m - 50km
            'title' => 'required|string|max:255',
            'message' => 'required|string|max:1000',
            'data' => 'nullable|string|max:500',
        ]);

        // Belirtilen koordinat çevresindeki cihazları bul
        $devices = Device::whereNotNull('gcm_id')
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->get()
            ->filter(function ($device) use ($request) {
                $distance = $device->getDistanceFromAttribute($request->latitude, $request->longitude);
                return $distance !== null && $distance <= ($request->radius / 1000); // km cinsinden karşılaştır
            });

        if ($devices->isEmpty()) {
            return redirect()->route('admin.devices.notifications')
                ->with('error', 'Belirtilen konum çevresinde bildirim gönderebilecek cihaz bulunamadı.');
        }

        // Parse additional data if provided
        $additionalData = [];
        if ($request->filled('data')) {
            $additionalData = json_decode($request->data, true) ?? [];
        }

        // Add location data to additional data
        $additionalData['location'] = [
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'radius' => $request->radius
        ];

        // Get FCM tokens for devices in range
        $tokens = $devices->pluck('gcm_id')->toArray();

        try {
            // Send notifications using FCM service
            $results = $this->fcmService->sendToMultipleDevices(
                $tokens,
                $request->title,
                $request->message,
                $additionalData
            );

            $message = "Konum bazlı bildirim gönderimi tamamlandı. {$devices->count()} cihaza gönderildi. Başarılı: {$results['success']}, Başarısız: {$results['failed']}";
            
            if ($results['failed'] > 0) {
                $message .= " Hata detayları log dosyasında görülebilir.";
            }

            return redirect()->route('admin.devices.notifications')->with('success', $message);

        } catch (\Exception $e) {
            \Log::error('FCM Location Service Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'location' => [
                    'latitude' => $request->latitude,
                    'longitude' => $request->longitude,
                    'radius' => $request->radius
                ]
            ]);

            return redirect()->route('admin.devices.notifications')
                ->with('error', 'Konum bazlı bildirim gönderimi sırasında hata oluştu: ' . $e->getMessage());
        }
    }

    /**
     * Get user groups for notification targeting.
     */
    public function getUserGroups(): array
    {
        return [
            'inactive_7_days' => [
                'name' => 'Son 7 gündür hiç uygulamaya girmemişler',
                'count' => Device::inactive(7)->whereNotNull('gcm_id')->count(),
                'description' => 'Son 7 gün içinde uygulamaya giriş yapmamış kullanıcılar'
            ],
            'inactive_30_days' => [
                'name' => 'Son 30 gündür hiç uygulamaya girmemişler',
                'count' => Device::inactive(30)->whereNotNull('gcm_id')->count(),
                'description' => 'Son 30 gün içinde uygulamaya giriş yapmamış kullanıcılar'
            ],
            'never_used' => [
                'name' => 'Hiç uygulamaya girmemişler',
                'count' => Device::neverUsed()->whereNotNull('gcm_id')->count(),
                'description' => 'Kayıt olduktan sonra hiç uygulamaya giriş yapmamış kullanıcılar'
            ],
            'with_location' => [
                'name' => 'Konum bilgisi olan kullanıcılar',
                'count' => Device::withLocation()->whereNotNull('gcm_id')->count(),
                'description' => 'Konum izni veren ve konum bilgisi mevcut olan kullanıcılar'
            ],
            'without_location' => [
                'name' => 'Konum bilgisi olmayan kullanıcılar',
                'count' => Device::withoutLocation()->whereNotNull('gcm_id')->count(),
                'description' => 'Konum izni vermeyen veya konum bilgisi olmayan kullanıcılar'
            ],
           
            'recent_users' => [
                'name' => 'Son 3 gün aktif kullanıcılar',
                'count' => Device::recent(3)->whereNotNull('gcm_id')->count(),
                'description' => 'Son 3 gün içinde uygulamaya giriş yapmış kullanıcılar'
            ],
            'weekly_users' => [
                'name' => 'Son 7 gün aktif kullanıcılar',
                'count' => Device::recent(7)->whereNotNull('gcm_id')->count(),
                'description' => 'Son 7 gün içinde uygulamaya giriş yapmış kullanıcılar'
            ],
            'monthly_users' => [
                'name' => 'Son 30 gün aktif kullanıcılar',
                'count' => Device::recent(30)->whereNotNull('gcm_id')->count(),
                'description' => 'Son 30 gün içinde uygulamaya giriş yapmış kullanıcılar'
            ]
        ];
    }

    /**
     * Send notification to specific user group.
     */
    public function sendToGroup(Request $request): RedirectResponse
    {
        $request->validate([
            'group' => 'required|string',
            'title' => 'required|string|max:255',
            'message' => 'required|string|max:1000',
            'data' => 'nullable|string|max:500',
        ]);

        $groups = $this->getUserGroups();
        
        if (!isset($groups[$request->group])) {
            return redirect()->route('admin.devices.notifications')
                ->with('error', 'Geçersiz kullanıcı grubu seçildi.');
        }

        $devices = $this->getDevicesByGroup($request->group);
        
        if ($devices->isEmpty()) {
            return redirect()->route('admin.devices.notifications')
                ->with('error', 'Seçilen grupta bildirim gönderebilecek cihaz bulunamadı.');
        }

        // Parse additional data if provided
        $additionalData = [];
        if ($request->filled('data')) {
            $additionalData = json_decode($request->data, true) ?? [];
        }

        // Get all FCM tokens
        $tokens = $devices->pluck('gcm_id')->toArray();

        try {
            // Send notifications using FCM service
            $results = $this->fcmService->sendToMultipleDevices(
                $tokens,
                $request->title,
                $request->message,
                $additionalData
            );

            $groupName = $groups[$request->group]['name'];
            $message = "{$groupName} grubuna bildirim gönderimi tamamlandı. Başarılı: {$results['success']}, Başarısız: {$results['failed']}";
            
            if ($results['failed'] > 0) {
                $message .= " Hata detayları log dosyasında görülebilir.";
            }

            return redirect()->route('admin.devices.notifications')->with('success', $message);

        } catch (\Exception $e) {
            \Log::error('FCM Group Notification Error', [
                'group' => $request->group,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('admin.devices.notifications')
                ->with('error', 'Bildirim gönderimi sırasında hata oluştu: ' . $e->getMessage());
        }
    }

    /**
     * Get devices by group type.
     */
    private function getDevicesByGroup(string $group): \Illuminate\Database\Eloquent\Collection
    {
        switch ($group) {
            case 'inactive_7_days':
                return Device::inactive(7)->whereNotNull('gcm_id')->get();
            case 'inactive_30_days':
                return Device::inactive(30)->whereNotNull('gcm_id')->get();
            case 'never_used':
                return Device::neverUsed()->whereNotNull('gcm_id')->get();
            case 'with_location':
                return Device::withLocation()->whereNotNull('gcm_id')->get();
            case 'without_location':
                return Device::withoutLocation()->whereNotNull('gcm_id')->get();
            case 'android_users':
                return Device::byPlatform('android')->whereNotNull('gcm_id')->get();
            case 'ios_users':
                return Device::byPlatform('ios')->whereNotNull('gcm_id')->get();
            case 'recent_users':
                return Device::recent(3)->whereNotNull('gcm_id')->get();
            case 'weekly_users':
                return Device::recent(7)->whereNotNull('gcm_id')->get();
            case 'monthly_users':
                return Device::recent(30)->whereNotNull('gcm_id')->get();
            default:
                return collect();
        }
    }
}
