<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Hotel extends Model
{
    use HasFactory;
    protected $fillable = ['nombre', 'direccion', 'provincia_id', 'categoria_id'];
    // referencias

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
}
