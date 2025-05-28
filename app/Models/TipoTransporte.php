<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Symfony\Component\Mailer\Transport;

class TipoTransporte extends Model
{
    use HasFactory;
    protected $fillable = [
        'nombre',

    ];
    public function transportes()
    {
        return $this->hasMany(Transporte::class, 'tipo_transporte_id');
}
}
