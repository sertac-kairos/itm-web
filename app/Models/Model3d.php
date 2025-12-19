<?php

namespace App\Models;

use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Model3d extends Model implements TranslatableContract
{
    use Translatable;

    protected $table = 'models_3d'; // Explicit table name
    
    // Custom translation table name
    public function getTranslationModelName()
    {
        return Model3dTranslation::class;
    }

    protected $fillable = [
        'sub_region_id',
        'archaeological_site_id',
        'sketchfab_model_id',
        'sketchfab_thumbnail_url',
        'qr_uuid',
        'qr_image_path',
        'is_active',
        'sort_order',
    ];

    // Translatable fields
    public $translatedAttributes = [
        'name',
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

    public function archaeologicalSite(): BelongsTo
    {
        return $this->belongsTo(ArchaeologicalSite::class);
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
