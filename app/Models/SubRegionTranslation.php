<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubRegionTranslation extends Model
{
    public $timestamps = false;
    
    protected $fillable = [
        'name',
        'subtitle',
        'description',
        'audio_guide_path',
    ];
}
