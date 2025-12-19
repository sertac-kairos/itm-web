<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeaturedContent extends Model
{
    protected $fillable = [
        'content_type',
        'content_id',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Polymorphic relationship
    public function content()
    {
        return $this->morphTo();
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
