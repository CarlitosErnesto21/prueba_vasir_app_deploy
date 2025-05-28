<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use function PHPUnit\Framework\returnSelf;

class CategoriaHotel extends Model
{
    use HasFactory;
    protected $fillable = ['estrella'];

    public function hoteles()
    {
        return $this->hasMany(Hotel::class, 'categoria_id');
    }
}
