<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transporte extends Model
{
    use HasFactory;
    protected $table = 'transportes';
    protected $fillable = [
        'numero_placa',
        'nombre',
        'capacidad',
        'marca',
        'estado',
    ];
    public function tours()
    {
        return $this->hasMany(Tour::class, 'transporte_id');
    }
}
