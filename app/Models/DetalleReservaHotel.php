<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetalleReservaHotel extends Model
{
    use HasFactory;
    protected $fillable = [
        'nombre',
        'fecha_entrada',
        'fecha_salida',
        'cantidad_personas',
        'cantidad_habitacion',
        'numero_habitacion',
        'subtotal',
        'reserva_id',
        'hotel_id'
    ];

    public function reserva()
    {
        return $this->belongsTo(Reserva::class, 'reserva_id');
    }

    public function hotel()
    {
        return $this->belongsTo(Hotel::class, 'hotel_id');
    }
}
