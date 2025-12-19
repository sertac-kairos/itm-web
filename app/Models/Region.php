<?php

namespace App\Models;

use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Region extends Model implements TranslatableContract
{
    use Translatable;

    protected $fillable = [
        'color_code',
        'main_image',
        'hotspot_image',
        'is_active',
        'sort_order',
        'latitude',
        'longitude',
    ];

    // Translatable fields - these will be in region_translations table
    public $translatedAttributes = [
        'name',
        'subtitle',
        'description',
        'audio_guide_path',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'hotspot_image' => 'array',
    ];

    // Relationships
    public function subRegions(): HasMany
    {
        return $this->hasMany(SubRegion::class)->orderBy('sort_order');
    }
    
    public function activeSubRegions(): HasMany
    {
        return $this->hasMany(SubRegion::class)->where('is_active', true)->orderBy('sort_order');
    }

    // Onboarding slides relationship removed - no longer region-specific

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }
}
