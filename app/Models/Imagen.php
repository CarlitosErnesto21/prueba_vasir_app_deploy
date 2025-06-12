<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Imagen extends Model
{
    protected $table = 'imagenes';
    
    protected $fillable = [
        'imageable_id',
        'imageable_type',
        'nombre'
    ];

    public function imageable()
    {
        return $this->morphTo();
    }
}