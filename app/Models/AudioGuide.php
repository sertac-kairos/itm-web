<?php

namespace App\Models;

use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AudioGuide extends Model implements TranslatableContract
{
    use Translatable;

    protected $fillable = [
        'sub_region_id',
        'audio_path',
        'duration',
        'is_active',
    ];

    // Translatable fields
    public $translatedAttributes = [
        'title',
        'description',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Relationships
    public function subRegion(): BelongsTo
    {
        return $this->belongsTo(SubRegion::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
