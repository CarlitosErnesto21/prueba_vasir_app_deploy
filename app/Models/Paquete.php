<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Paquete extends Model
{
    protected $fillable = [
        'nombre',
        'fecha_salida',
        'fecha_regreso',
        'incluye',
        'condiciones',
        'recordatorio',
        'precio',
        'pais_id',
        'provincia_id',
    ];

    public function pais()
    {
        return $this->belongsTo(Pais::class);
    }

    public function provincia()
    {
        return $this->belongsTo(Provincia::class);
    }

    public function imagenes()
    {
        return $this->morphMany(Imagen::class, 'imageable');
    }
}
