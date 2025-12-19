<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Model3dTranslation extends Model
{
    protected $table = 'model3d_translations';
    public $timestamps = true;
    
    protected $fillable = [
        'model3d_id',
        'locale',
        'name',
        'description',
    ];
}
