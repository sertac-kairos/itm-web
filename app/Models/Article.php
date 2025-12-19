<?php

namespace App\Models;

use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Article extends Model implements TranslatableContract
{
    use Translatable;

    protected $fillable = [
        'author',
        'sort_order',
        'is_active',
    ];

    // Translatable fields
    public $translatedAttributes = [
        'title',
        'content',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    // Relationships
    public function images(): HasMany
    {
        return $this->hasMany(ArticleImage::class)->orderBy('sort_order');
    }

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
    public function getMainImageAttribute(): ?string
    {
        $mainImage = $this->images()->orderBy('sort_order')->first();
        return $mainImage ? asset('storage/' . $mainImage->image_path) : null;
    }

    public function getMainImagePathAttribute(): ?string
    {
        $mainImage = $this->images()->orderBy('sort_order')->first();
        return $mainImage ? $mainImage->image_path : null;
    }

    public function getImagesCountAttribute(): int
    {
        return $this->images()->count();
    }
}
