<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class CategoriaProducto extends Model
{
    use HasFactory;
    protected $fillable = [
        'nombre'
    ];
    public function producto()
    {
        return $this->hasMany(Producto::class, 'categoria_id');
    }
}
