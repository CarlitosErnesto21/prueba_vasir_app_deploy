<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Aerolinea extends Model
{
    use HasFactory;
    protected $fillable = ['nombre', 'fecha'];

    public function detalleReservas()
    {
        return $this->hasMany(DetalleReservaAerolinea::class, 'aerolinea_id');
    }

    public function imagenes()
    {
        return $this->morphMany(\App\Models\Imagen::class, 'imageable');
    }
}
