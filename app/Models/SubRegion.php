<?php

namespace App\Models;

use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SubRegion extends Model implements TranslatableContract
{
    use Translatable;

    protected $fillable = [
        'region_id',
        'latitude',
        'longitude',
        'image',
        'color',
        'is_active',
        'sort_order',
    ];

    // Translatable fields
    public $translatedAttributes = [
        'name',
        'subtitle',
        'description',
        'audio_guide_path',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
    ];

    // Relationships
    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class);
    }

    public function archaeologicalSites(): HasMany
    {
        return $this->hasMany(ArchaeologicalSite::class);
    }

    public function qrCodes(): HasMany
    {
        return $this->hasMany(QrCode::class);
    }



    public function audioGuides(): HasMany
    {
        return $this->hasMany(AudioGuide::class);
    }

    public function models3d(): HasMany
    {
        return $this->hasMany(Model3d::class);
    }

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
