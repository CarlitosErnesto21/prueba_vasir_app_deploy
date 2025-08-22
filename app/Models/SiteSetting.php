<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

class SiteSetting extends Model
{
    protected $fillable = ['key', 'value', 'type', 'label', 'description', 'updated_by'];

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public static function get($key, $default = '')
    {
        $setting = static::where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }

    public static function set($key, $value, $userId = null)
    {
        return static::updateOrCreate(
            ['key' => $key],
            [
                'value' => $value,
                'label' => $key, // Usar la key como label por defecto
                'type' => 'text', // Tipo por defecto
                'updated_by' => $userId ?? (Auth::check() ? Auth::id() : null)
            ]
        );
    }
}
