<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetalleVenta extends Model
{
    use HasFactory;

    protected $table = 'detalles_ventas';

    protected $fillable = [
        'cantidad',
        'precio_unitario',
        'subtotal',
        'venta_id',
        'producto_id'
    ];

    // ⭐ AGREGAR CASTS PARA DECIMALES
    protected $casts = [
        'precio_unitario' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    // ===== RELACIONES (con tipado) =====
    
    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }

    public function venta(): BelongsTo
    {
        return $this->belongsTo(Venta::class, 'venta_id');
    }

    // ===== MÉTODOS AUXILIARES =====
    
    public function calcularSubtotal(): float
    {
        return $this->cantidad * $this->precio_unitario;
    }

    // ===== VALIDACIONES =====
    
    protected static function boot()
    {
        parent::boot();
        
        static::saving(function ($detalle) {
            // Calcular subtotal automáticamente
            $detalle->subtotal = $detalle->cantidad * $detalle->precio_unitario;
        });
    }
}