<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QrCode extends Model
{
    protected $fillable = [
        'sub_region_id',
        'qr_content',
        'ar_model_path',
        'is_active',
        'is_coming_soon',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_coming_soon' => 'boolean',
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

    public function scopeReady($query)
    {
        return $query->where('is_coming_soon', false);
    }
}
