<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tour extends Model
{
    use HasFactory;
    protected $table = 'tours';

    protected $fillable = [
        'nombre',
        'descripcion',
        'punto_salida',
        'fecha',
        'precio',
        'hora_regreso',
        'categoria_tour_id',
        'transporte_id'
    ];

    public function categoria()
    {
        return $this->belongsTo(CategoriaTour::class, 'categoria_tour_id');
    }

    public function transporte()
    {
        return $this->belongsTo(Transporte::class);
    }
    public function detalleReservas()
    {
        return $this->hasMany(DetalleReservaTour::class, 'tour_id');
    }

    public function imagenes()
    {
        return $this->morphMany(Imagen::class, 'imageable');
    }
}
