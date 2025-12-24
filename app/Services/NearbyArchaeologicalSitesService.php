<?php

namespace App\Services;

use App\Models\ArchaeologicalSite;
use Illuminate\Database\Eloquent\Collection;

class NearbyArchaeologicalSitesService
{
    /**
     * Yakındaki arkeolojik siteleri getirir
     *
     * @param float $latitude Kullanıcının enlem değeri
     * @param float $longitude Kullanıcının boylam değeri
     * @param int $limit Döndürülecek maksimum site sayısı (varsayılan: 10)
     * @param float $maxDistanceKm Maksimum mesafe (kilometre cinsinden, varsayılan: 50)
     * @return Collection
     */
    public function getNearbySites(float $latitude, float $longitude, int $limit = 10, float $maxDistanceKm = 50): Collection
    {
        // Haversine formülü ile mesafe hesaplama
        $haversine = "(6371 * acos(cos(radians(?)) 
                     * cos(radians(latitude)) 
                     * cos(radians(longitude) - radians(?)) 
                     + sin(radians(?)) 
                     * sin(radians(latitude))))";

        $sites = ArchaeologicalSite::selectRaw("*, {$haversine} AS distance", [$latitude, $longitude, $latitude])
            ->active()
            ->nearbyEnabled()
            ->whereRaw("{$haversine} <= ?", [$latitude, $longitude, $latitude, $maxDistanceKm])
            ->orderBy('distance')
            ->limit($limit)
            ->with(['subRegion.region', 'translations', 'models3d' => function ($q) {
                $q->where('is_active', true)->orderBy('sort_order')->with('translations');
            }])
            ->get();

        return $sites;
    }

    /**
     * Mesafe hesaplama için yardımcı fonksiyon (Haversine formülü)
     *
     * @param float $lat1 İlk noktanın enlemi
     * @param float $lon1 İlk noktanın boylamı
     * @param float $lat2 İkinci noktanın enlemi
     * @param float $lon2 İkinci noktanın boylamı
     * @return float Mesafe (kilometre cinsinden)
     */
    public function calculateDistance(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $earthRadius = 6371; // Dünya'nın yarıçapı (km)

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }

    /**
     * Yakındaki siteleri formatlanmış şekilde döndürür
     *
     * @param float $latitude
     * @param float $longitude
     * @param int $limit
     * @param float $maxDistanceKm
     * @return array
     */
    public function getFormattedNearbySites(float $latitude, float $longitude, int $limit = 10, float $maxDistanceKm = 50): array
    {
        $sites = $this->getNearbySites($latitude, $longitude, $limit, $maxDistanceKm);

        return $sites->map(function ($site) {
            return [
                'id' => $site->id,
                'sub_region' => [
                    'id' => $site->subRegion?->id,
                    'name' => $site->subRegion?->name,
                ],
                'name' => $site->name,
                'description' => $site->description,
                'latitude' => $site->latitude,
                'longitude' => $site->longitude,
                'image' => $site->image ? url('storage/' . $site->image) : null,
                'distance_km' => round($site->distance, 2),
                'distance_m' => round($site->distance * 1000, 0),
                'models_3d' => $site->models3d->map(function ($model) use ($site) {
                    return [
                        'id' => $model->id,
                        'name' => $model->name,
                        'description' => $model->description,
                        'sketchfab_model_id' => $model->sketchfab_model_id,
                        'thumbnail' => $model->sketchfab_thumbnail_url,
                        'sort_order' => $model->sort_order,
                        'audio_guide_path' => $site->audio_guide_path ? url('storage/' . $site->audio_guide_path) : null,
                    ];
                }),
            ];
        })->toArray();
    }
}
