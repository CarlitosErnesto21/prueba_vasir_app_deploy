<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Hotel extends Model
{
    protected $table = 'hoteles';

    use HasFactory;
    protected $fillable = [
        'nombre',
        'direccion',
        'descripcion',
        'estado',
        'provincia_id',
        'categoria_id'
    ];    // referencias

    public function provincia()
    {
        return $this->belongsTo(Provincia::class);
    }

    public function categoriaHotel()
    {
        return $this->belongsTo(CategoriaHotel::class, 'categoria_id');
    }
    public function detalleReservas()
    {
        return $this->hasMany(DetalleReservaHotel::class, 'hotel_id');
    }

    public function imagenes()
    {
        return $this->morphMany(Imagen::class, 'imageable');
    }
}
