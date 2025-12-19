<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Device extends Model
{
    protected $fillable = [
        'gcm_id',
        'device_id',
        'latitude',
        'longitude',
        'platform',
        'app_version',
        'os_version',
        'is_active',
        'last_seen',
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'is_active' => 'boolean',
        'last_seen' => 'datetime',
    ];

    // Scopes
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeByPlatform(Builder $query, string $platform): Builder
    {
        return $query->where('platform', $platform);
    }

    public function scopeRecent(Builder $query, int $days = 7): Builder
    {
        return $query->where('last_seen', '>=', now()->subDays($days));
    }

    public function scopeInactive(Builder $query, int $days = 7): Builder
    {
        return $query->where(function($q) use ($days) {
            $q->whereNull('last_seen')
              ->orWhere('last_seen', '<', now()->subDays($days));
        });
    }

    public function scopeNeverUsed(Builder $query): Builder
    {
        return $query->whereNull('last_seen');
    }

    public function scopeWithLocation(Builder $query): Builder
    {
        return $query->whereNotNull('latitude')->whereNotNull('longitude');
    }

    public function scopeWithoutLocation(Builder $query): Builder
    {
        return $query->where(function($q) {
            $q->whereNull('latitude')->orWhereNull('longitude');
        });
    }

    public function scopeByAppVersion(Builder $query, string $version): Builder
    {
        return $query->where('app_version', $version);
    }

    public function scopeByOsVersion(Builder $query, string $osVersion): Builder
    {
        return $query->where('os_version', 'like', $osVersion . '%');
    }

    public function scopeInRadius(Builder $query, float $lat, float $lng, float $radiusKm): Builder
    {
        return $query->whereNotNull('latitude')
                    ->whereNotNull('longitude')
                    ->whereRaw("
                        (6371 * acos(cos(radians(?)) * cos(radians(latitude)) * 
                        cos(radians(longitude) - radians(?)) + sin(radians(?)) * 
                        sin(radians(latitude)))) <= ?
                    ", [$lat, $lng, $lat, $radiusKm]);
    }

    // Helper methods
    public function hasLocation(): bool
    {
        return !is_null($this->latitude) && !is_null($this->longitude);
    }

    public function getLocationAttribute(): ?array
    {
        if ($this->hasLocation()) {
            return [
                'latitude' => (float) $this->latitude,
                'longitude' => (float) $this->longitude,
            ];
        }
        return null;
    }

    public function updateLastSeen(): void
    {
        $this->update(['last_seen' => now()]);
    }

    public function isOnline(): bool
    {
        if (!$this->last_seen) {
            return false;
        }
        
        // Consider device online if last seen within last 24 hours
        return $this->last_seen->isAfter(now()->subDay());
    }

    public function getOnlineStatusAttribute(): string
    {
        return $this->isOnline() ? 'online' : 'offline';
    }

    public function getDistanceFromAttribute(float $lat, float $lng): ?float
    {
        if (!$this->hasLocation()) {
            return null;
        }

        // Haversine formula to calculate distance between two points
        $earthRadius = 6371; // Earth's radius in kilometers

        $latFrom = deg2rad($this->latitude);
        $lonFrom = deg2rad($this->longitude);
        $latTo = deg2rad($lat);
        $lonTo = deg2rad($lng);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
            cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));

        return $angle * $earthRadius;
    }

    public function getAddressAttribute(): ?string
    {
        if (!$this->hasLocation()) {
            return null;
        }

        // Cache the address for 24 hours to avoid repeated API calls
        $cacheKey = "device_address_{$this->id}_{$this->latitude}_{$this->longitude}";
        
        return cache()->remember($cacheKey, 86400, function () {
            try {
                $url = "https://nominatim.openstreetmap.org/reverse?format=json&lat={$this->latitude}&lon={$this->longitude}&zoom=18&addressdetails=1";
                
                $context = stream_context_create([
                    'http' => [
                        'header' => "User-Agent: IzmirTimeMachine/1.0\r\n",
                        'timeout' => 10
                    ]
                ]);
                
                $response = file_get_contents($url, false, $context);
                
                if ($response === false) {
                    return null;
                }
                
                $data = json_decode($response, true);
                
                if (!$data || !isset($data['display_name'])) {
                    return null;
                }
                
                // Extract relevant address components
                $address = $data['display_name'];
                
                // Try to get a shorter, more readable address
                if (isset($data['address'])) {
                    $addr = $data['address'];
                    $parts = [];
                    
                    if (isset($addr['house_number']) && isset($addr['road'])) {
                        $parts[] = $addr['house_number'] . ' ' . $addr['road'];
                    } elseif (isset($addr['road'])) {
                        $parts[] = $addr['road'];
                    }
                    
                    if (isset($addr['suburb']) || isset($addr['neighbourhood'])) {
                        $parts[] = $addr['suburb'] ?? $addr['neighbourhood'];
                    }
                    
                    if (isset($addr['city']) || isset($addr['town'])) {
                        $parts[] = $addr['city'] ?? $addr['town'];
                    }
                    
                    if (isset($addr['state'])) {
                        $parts[] = $addr['state'];
                    }
                    
                    if (isset($addr['country'])) {
                        $parts[] = $addr['country'];
                    }
                    
                    if (!empty($parts)) {
                        $address = implode(', ', $parts);
                    }
                }
                
                return $address;
                
            } catch (\Exception $e) {
                \Log::warning("Failed to get address for device {$this->id}: " . $e->getMessage());
                return null;
            }
        });
    }
}
