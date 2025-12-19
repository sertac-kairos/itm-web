<?php

namespace App\Models;

use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ArchaeologicalSite extends Model implements TranslatableContract
{
    use Translatable;

    protected $fillable = [
        'sub_region_id',
        'latitude',
        'longitude',
        'image',
        'is_nearby_enabled',
        'is_active',
    ];

    // Translatable fields
    public $translatedAttributes = [
        'name',
        'description',
    ];

    protected $casts = [
        'is_nearby_enabled' => 'boolean',
        'is_active' => 'boolean',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
    ];

    // Relationships
    public function subRegion(): BelongsTo
    {
        return $this->belongsTo(SubRegion::class);
    }

    public function qrCodes(): HasMany
    {
        return $this->hasMany(QrCode::class);
    }

    public function models3d(): HasMany
    {
        return $this->hasMany(Model3d::class)->orderBy('sort_order');
    }

    public function audioGuides(): HasMany
    {
        return $this->hasMany(AudioGuide::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeNearbyEnabled($query)
    {
        return $query->where('is_nearby_enabled', true);
    }

    // Helper methods
    public function getRegionAttribute()
    {
        return $this->subRegion->region;
    }
}
