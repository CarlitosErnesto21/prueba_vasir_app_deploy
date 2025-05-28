<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Venta extends Model
{
    use HasFactory;
    protected $fillable = [
        'fecha',
        'total',
        'reserva_id',
        'cliente_id',
        'empleado_id',
        'metodo_pago_id',
    ];

    public function reserva()
    {
        return $this->belongsTo(Reserva::class, 'reserva_id');
    }
    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }
    public function empleado()
    {
        return $this->belongsTo(Empleado::class, 'empleado_id');
    }
    public function metodoPago()
    {
        return $this->belongsTo(MetodoPago::class, 'metodo_pago_id');
    }
    public function detalleVenta()
    {
        return $this->hasMany(DetalleVenta::class, 'venta_id');
    }
}
