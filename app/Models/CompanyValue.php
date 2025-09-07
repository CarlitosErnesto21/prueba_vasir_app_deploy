<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanyValue extends Model
{
    protected $fillable = [
        'titulo',
        'descripcion'
    ];

    /**
     * Obtener todos los valores corporativos
     */
    public static function getAllValues()
    {
        return static::orderBy('created_at')->get();
    }
}
