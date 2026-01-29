<?php

namespace App\Models;

use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Story extends Model implements TranslatableContract
{
    use Translatable;

    protected $fillable = [
        'thumbnail',
        'image',
        'sort_order',
        'is_active',
        'model_3d_id',
    ];

    // Translatable fields
    public $translatedAttributes = [
        'title',
        'image',
        'description',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    // Scopes
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }

    // Helper methods
    public function getThumbnailUrlAttribute(): ?string
    {
        return $this->thumbnail ? asset('storage/' . $this->thumbnail) : null;
    }

    public function getImageUrlAttribute(): ?string
    {
        return $this->image ? asset('storage/' . $this->image) : null;
    }

    public function getTranslatedImageUrlAttribute(): ?string
    {
        $translatedImage = $this->translate(app()->getLocale())->image ?? null;
        return $translatedImage ? asset('storage/' . $translatedImage) : null;
    }

    /**
     * Get edited images for all locales
     */
    public function getEditedImagesAttribute(): array
    {
        $images = [];
        foreach ($this->translations as $translation) {
            if ($translation->image) {
                $images[$translation->locale] = asset('storage/' . $translation->image);
            }
        }
        return $images;
    }

    // Relationships
    public function model3d(): BelongsTo
    {
        return $this->belongsTo(Model3d::class, 'model_3d_id');
    }
}
