<?php

namespace App\Models;

use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;

class OnboardingSlide extends Model implements TranslatableContract
{
    use Translatable;

    protected $fillable = [
        'sort_order',
        'is_active',
    ];

    // Translatable fields
    public $translatedAttributes = [
        'title',
        'description',
        'image',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

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
