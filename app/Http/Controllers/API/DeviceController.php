<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Device;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class DeviceController extends Controller
{
    /**
     * Sync device information - create or update device record.
     * This endpoint should be called every time the app comes to foreground.
     * If device_id exists, update the record; otherwise create new one.
     */
    public function sync(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'gcm_id' => 'nullable|string|max:255',
            'device_id' => 'required|string|max:255',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'platform' => 'nullable|string|in:iOS,Android,Web',
            'app_version' => 'nullable|string|max:50',
            'os_version' => 'nullable|string|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            // Use updateOrCreate to handle race conditions
            $device = Device::updateOrCreate(
                ['device_id' => $request->device_id],
                [
                    'gcm_id' => $request->gcm_id,
                    'latitude' => $request->latitude,
                    'longitude' => $request->longitude,
                    'platform' => $request->platform,
                    'app_version' => $request->app_version,
                    'os_version' => $request->os_version,
                    'last_seen' => now(),
                    'is_active' => true, // Ensure device is marked as active
                ]
            );

            $message = 'Device synced successfully';
            $action = $device->wasRecentlyCreated ? 'created' : 'updated';

            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => [
                    'id' => $device->id,
                    'device_id' => $device->device_id,
                    'gcm_id' => $device->gcm_id,
                    'latitude' => $device->latitude,
                    'longitude' => $device->longitude,
                    'platform' => $device->platform,
                    'app_version' => $device->app_version,
                    'os_version' => $device->os_version,
                    'is_active' => $device->is_active,
                    'last_seen' => $device->last_seen?->toISOString(),
                    'online_status' => $device->online_status,
                    'action' => $action,
                ],
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to sync device',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get device information by device_id.
     */
    public function show(Request $request, string $deviceId): JsonResponse
    {
        $device = Device::where('device_id', $deviceId)->first();

        if (!$device) {
            return response()->json([
                'success' => false,
                'message' => 'Device not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $device->id,
                'device_id' => $device->device_id,
                'gcm_id' => $device->gcm_id,
                'latitude' => $device->latitude,
                'longitude' => $device->longitude,
                'platform' => $device->platform,
                'app_version' => $device->app_version,
                'os_version' => $device->os_version,
                'is_active' => $device->is_active,
                'last_seen' => $device->last_seen?->toISOString(),
                'online_status' => $device->online_status,
                'has_location' => $device->hasLocation(),
                'location' => $device->location,
            ],
        ]);
    }

    /**
     * Update device location.
     */
    public function updateLocation(Request $request, string $deviceId): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $device = Device::where('device_id', $deviceId)->first();

        if (!$device) {
            return response()->json([
                'success' => false,
                'message' => 'Device not found',
            ], 404);
        }

        $device->update([
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'last_seen' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Location updated successfully',
            'data' => [
                'device_id' => $device->device_id,
                'latitude' => $device->latitude,
                'longitude' => $device->longitude,
                'last_seen' => $device->last_seen->toISOString(),
            ],
        ]);
    }

    /**
     * Update device GCM ID.
     */
    public function updateGcmId(Request $request, string $deviceId): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'gcm_id' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $device = Device::where('device_id', $deviceId)->first();

        if (!$device) {
            return response()->json([
                'success' => false,
                'message' => 'Device not found',
            ], 404);
        }

        $device->update([
            'gcm_id' => $request->gcm_id,
            'last_seen' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'GCM ID updated successfully',
            'data' => [
                'device_id' => $device->device_id,
                'gcm_id' => $device->gcm_id,
                'last_seen' => $device->last_seen->toISOString(),
            ],
        ]);
    }

    /**
     * Get nearby devices within a certain radius.
     */
    public function nearby(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'radius' => 'nullable|numeric|min:0.1|max:100', // Radius in kilometers
            'limit' => 'nullable|integer|min:1|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $latitude = $request->latitude;
        $longitude = $request->longitude;
        $radius = $request->radius ?? 10; // Default 10km radius
        $limit = $request->limit ?? 20;

        // Get devices with location data
        $devices = Device::active()
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->get()
            ->map(function ($device) use ($latitude, $longitude) {
                $distance = $device->getDistanceFromAttribute($latitude, $longitude);
                return [
                    'id' => $device->id,
                    'device_id' => $device->device_id,
                    'latitude' => $device->latitude,
                    'longitude' => $device->longitude,
                    'platform' => $device->platform,
                    'distance_km' => round($distance, 2),
                    'last_seen' => $device->last_seen?->toISOString(),
                ];
            })
            ->filter(function ($device) use ($radius) {
                return $device['distance_km'] <= $radius;
            })
            ->sortBy('distance_km')
            ->take($limit)
            ->values();

        return response()->json([
            'success' => true,
            'data' => $devices,
            'meta' => [
                'center_latitude' => $latitude,
                'center_longitude' => $longitude,
                'radius_km' => $radius,
                'total_found' => $devices->count(),
            ],
        ]);
    }

    /**
     * Get device statistics.
     */
    public function stats(): JsonResponse
    {
        $totalDevices = Device::count();
        $activeDevices = Device::active()->count();
        $onlineDevices = Device::active()->where('last_seen', '>=', now()->subDay())->count();
        
        $platformStats = Device::selectRaw('platform, COUNT(*) as count')
            ->groupBy('platform')
            ->get()
            ->pluck('count', 'platform');

        $recentDevices = Device::where('last_seen', '>=', now()->subDays(7))->count();

        return response()->json([
            'success' => true,
            'data' => [
                'total_devices' => $totalDevices,
                'active_devices' => $activeDevices,
                'online_devices' => $onlineDevices,
                'recent_devices' => $recentDevices,
                'platform_distribution' => $platformStats,
            ],
        ]);
    }
}
