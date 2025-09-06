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

    /**
     * Obtener total de cupos reservados para este tour
     * Solo cuenta reservas que no estén rechazadas
     */
    public function getCuposReservadosAttribute()
    {
        return $this->detalleReservas()
            ->whereHas('reserva', function ($query) {
                $query->whereIn('estado', ['PENDIENTE', 'CONFIRMADA', 'REPROGRAMADA']);
            })
            ->sum('cupos_reservados');
    }

    /**
     * Obtener cupos disponibles para reservar
     */
    public function getCuposDisponiblesAttribute()
    {
        return max(0, $this->cupo_max - $this->cupos_reservados);
    }

    /**
     * Verificar si hay cupos disponibles
     */
    public function tieneDisponibilidad($cuposSolicitados = 1)
    {
        return $this->cupos_disponibles >= $cuposSolicitados;
    }
}
