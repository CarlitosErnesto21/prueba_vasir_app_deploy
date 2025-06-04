<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class HistorialReserva extends Model
{
    use HasFactory;
    protected $table = 'historial_reservas';
    protected $fillable = [
        'tipo_reserva',
        'nombre_servicio',
        'nombre_cliente',
        'fecha_reserva',
        'fecha_confirmacion',
        'fecha_completado',
        'cantidad',
        'precio_unitario',
        'precio_total',
        'fecha_registro',
    ];
}
