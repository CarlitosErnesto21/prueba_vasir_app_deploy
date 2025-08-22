<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use function PHPUnit\Framework\returnSelf;

class CategoriaHotel extends Model
{
    protected $table = 'categorias_hoteles';
    
    use HasFactory;
    protected $fillable = ['nombre'];

    public function hoteles()
    {
        return $this->hasMany(Hotel::class, 'categoria_id');
    }
}
