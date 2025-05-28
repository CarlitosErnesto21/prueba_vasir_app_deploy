<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MetodoPago extends Model
{
    use HasFactory;

    // Especifica el nombre correcto de la tabla
    protected $table = 'metodos_pagos';

    protected $fillable = [
        'metodo_pago',
    ];

    public function ventas()
    {
        return $this->hasMany(Venta::class, 'metodo_pago_id');
    }
}
