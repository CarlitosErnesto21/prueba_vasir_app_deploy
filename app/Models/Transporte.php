<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transporte extends Model
{
    use HasFactory;
    protected $table = 'transportes';
    protected $fillable = [
        'capacidad',
        'tipo_transporte_id',
    ];
    public function tipoTransporte()
    {
        return $this->belongsTo(TipoTransporte::class, 'tipo_transporte_id');
    }
    public function tours()
    {
        return $this->hasMany(Tour::class, 'transporte_id');
    }
}
