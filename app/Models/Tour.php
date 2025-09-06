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
        'categoria',
        'incluye',
        'no_incluye',
        'cupo_min',
        'cupo_max',
        'punto_salida',
        'fecha_salida',
        'fecha_regreso',
        'estado',
        'transporte_id',
        'precio'
    ];

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

    // Método para calcular cupos disponibles
    public function getCuposDisponiblesAttribute()
    {
        $cuposReservados = $this->detalleReservas()
            ->whereHas('reserva', function($query) {
                $query->where('estado', '!=', 'cancelada');
            })
            ->sum('cupos_reservados');
        
        return max(0, $this->cupo_max - $cuposReservados);
    }

    // Método para verificar si hay cupos suficientes
    public function tieneCuposDisponibles($cuposSolicitados)
    {
        return $this->cupos_disponibles >= $cuposSolicitados;
    }
}
