<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Provincia extends Model
{
    use HasFactory;
    protected $fillable = ['nombre', 'pais_id'];
    // Relaciones pertenecen "a" o la inversa de la relación
    
    public function pais()
    {
        return $this->belongsTo(Pais::class);
    }
}
