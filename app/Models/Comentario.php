<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comentario extends Model
{
    use HasFactory;
    protected $fillable = [
        'fecha_publicación',
        'mensaje',
        'user_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
