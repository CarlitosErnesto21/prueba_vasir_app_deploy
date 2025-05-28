<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Psy\Readline\Hoa\FileLink;

class CategoriaTour extends Model
{
    use HasFactory;
    protected $fillable = ['nombre'];

    public function tours()
    {
        return $this->hasMany(Tour::class, 'categoria_tour_id');
    }
}
