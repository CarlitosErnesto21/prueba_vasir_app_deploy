<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Empleado extends Model
{
    use HasFactory;
    protected $fillable = [
        'nombre',
        'cargo',
        'telefono',
        'user_id',
    ];

    public function reservas()
    {
        return $this->hasMany(Reserva::class, 'empleado_id');
    }
    public function ventas()
    {
        return $this->hasMany(Venta::class, 'empleado_id');
    }
}
