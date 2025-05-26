<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    use HasFactory;
    protected $fillable = ['nombre', 'descripcion', 'precio', 'inventario_id', 'categoria_id'];

    //relaciones pertenecen "a" o la inversa de la relación
    public function categoria()
    {
        return $this->belongsTo(CategoriaProducto::class);
    }
}
