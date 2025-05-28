<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetalleReservaAerolinea extends Model
{
    use HasFactory;
    protected $fillable = [
        'nombre',
        'fecha',
        'clase',
        'reserva_id',
        'aerolinea_id'
    ];

    public function reserva()
    {
        return $this->belongsTo(Reserva::class, 'reserva_id');
    }

    public function aerolinea()
    {
        return $this->belongsTo(Aerolinea::class, 'aerolinea_id');
    }
}
