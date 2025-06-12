<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use NunoMaduro\Collision\Provider;

class Pais extends Model
{
    protected $table = 'paises';
    
   use HasFactory;
   protected $fillable = ['nombre'];

   public function provincias()
   {
       return $this->hasMany(Provincia::class, 'pais_id');
   }
}
