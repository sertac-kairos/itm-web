<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MemoryTranslation extends Model
{
    protected $fillable = [
        'title',
        'content',
    ];

    public $timestamps = true;
}
