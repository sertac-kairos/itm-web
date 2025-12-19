<?php

namespace App\Models;

use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Memory extends Model implements TranslatableContract
{
    use Translatable;

    protected $fillable = [
        'image',
        'link',
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
    public function getImageUrlAttribute(): ?string
    {
        return $this->image ? asset('storage/' . $this->image) : null;
    }

    public function getThumbnailUrlAttribute(): ?string
    {
        return $this->image_url;
    }

    public function hasLink(): bool
    {
        return !empty($this->link);
    }

    public function getFormattedLinkAttribute(): ?string
    {
        if (!$this->hasLink()) {
            return null;
        }

        // Add http:// if protocol is missing
        if (!preg_match("~^(?:f|ht)tps?://~i", $this->link)) {
            return 'http://' . $this->link;
        }

        return $this->link;
    }
}
