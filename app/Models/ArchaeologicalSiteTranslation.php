<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ArchaeologicalSiteTranslation extends Model
{
    protected $fillable = [
        'name',
        'description',
        'audio_guide_path',
    ];

    public $timestamps = true;
}
