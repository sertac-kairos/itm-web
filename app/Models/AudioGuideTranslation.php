<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AudioGuideTranslation extends Model
{
    public $timestamps = false;
    
    protected $fillable = [
        'title',
        'description',
    ];
}
