<?php

namespace App\Models;

use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;

class News extends Model implements TranslatableContract
{
    use Translatable;

    protected $fillable = [
        'slug',
        'news_date',
        'sort_order',
        'is_active',
        'featured_image',
    ];

    // Translatable fields
    public $translatedAttributes = [
        'title',
        'content',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
        'news_date' => 'date',
    ];

    // Relationships
    public function images(): HasMany
    {
        return $this->hasMany(NewsImage::class)->orderBy('sort_order');
    }

    // Scopes
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('news_date', 'desc')->orderBy('sort_order')->orderBy('id');
    }

    // Helper methods
    public function getMainImageAttribute(): ?string
    {
        return $this->images()->first()?->image_path ?? $this->featured_image;
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
