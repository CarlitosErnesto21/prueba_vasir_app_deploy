<?php

namespace Database\Seeders;

use App\Models\Producto;
use App\Models\CategoriaProducto;
use Illuminate\Database\Seeder;

class ProductoSeeder extends Seeder
{
    public function run(): void
    {
        $electronicos = CategoriaProducto::where('nombre', 'Electrónicos')->first();
        $ropa = CategoriaProducto::where('nombre', 'Ropa')->first();
        $hogar = CategoriaProducto::where('nombre', 'Hogar')->first();
        $deportes = CategoriaProducto::where('nombre', 'Deportes')->first();

        $productos = [
            // Electrónicos
            [
                'nombre' => 'Laptop Dell Inspiron 15',
                'descripcion' => 'Laptop Dell con procesador Intel i5, 8GB RAM, 256GB SSD',
                'precio' => 899.99,
                'stock_actual' => 15,
                'stock_minimo' => 5,
                'categoria_id' => $electronicos->id,
            ],
            [
                'nombre' => 'iPhone 14 Pro',
                'descripcion' => 'Smartphone Apple iPhone 14 Pro 128GB',
                'precio' => 1099.99,
                'stock_actual' => 25,
                'stock_minimo' => 10,
                'categoria_id' => $electronicos->id,
            ],
            [
                'nombre' => 'Samsung Galaxy S23',
                'descripcion' => 'Smartphone Samsung Galaxy S23 256GB',
                'precio' => 799.99,
                'stock_actual' => 20,
                'stock_minimo' => 8,
                'categoria_id' => $electronicos->id,
            ],
            [
                'nombre' => 'Auriculares Sony WH-1000XM4',
                'descripcion' => 'Auriculares inalámbricos con cancelación de ruido',
                'precio' => 299.99,
                'stock_actual' => 30,
                'stock_minimo' => 12,
                'categoria_id' => $electronicos->id,
            ],

            // Ropa
            [
                'nombre' => 'Camiseta Nike Dri-FIT',
                'descripcion' => 'Camiseta deportiva Nike talla M',
                'precio' => 29.99,
                'stock_actual' => 50,
                'stock_minimo' => 20,
                'categoria_id' => $ropa->id,
            ],
            [
                'nombre' => 'Jeans Levi\'s 501',
                'descripcion' => 'Jeans clásicos Levi\'s talla 32',
                'precio' => 89.99,
                'stock_actual' => 35,
                'stock_minimo' => 15,
                'categoria_id' => $ropa->id,
            ],

            // Hogar
            [
                'nombre' => 'Aspiradora Dyson V11',
                'descripcion' => 'Aspiradora inalámbrica Dyson V11 Absolute',
                'precio' => 599.99,
                'stock_actual' => 12,
                'stock_minimo' => 5,
                'categoria_id' => $hogar->id,
            ],
            [
                'nombre' => 'Cafetera Nespresso',
                'descripcion' => 'Cafetera de cápsulas Nespresso Vertuo',
                'precio' => 199.99,
                'stock_actual' => 18,
                'stock_minimo' => 8,
                'categoria_id' => $hogar->id,
            ],

            // Deportes
            [
                'nombre' => 'Zapatillas Nike Air Max',
                'descripcion' => 'Zapatillas deportivas Nike Air Max talla 42',
                'precio' => 129.99,
                'stock_actual' => 40,
                'stock_minimo' => 18,
                'categoria_id' => $deportes->id,
            ],
            [
                'nombre' => 'Bicicleta Trek Mountain',
                'descripcion' => 'Bicicleta de montaña Trek aro 29',
                'precio' => 1299.99,
                'stock_actual' => 8,
                'stock_minimo' => 3,
                'categoria_id' => $deportes->id,
            ],

            // Productos con stock bajo para pruebas
            [
                'nombre' => 'Tablet iPad Air',
                'descripcion' => 'Tablet Apple iPad Air 64GB',
                'precio' => 599.99,
                'stock_actual' => 3, // Stock bajo
                'stock_minimo' => 5,
                'categoria_id' => $electronicos->id,
            ],
            [
                'nombre' => 'Smartwatch Apple Watch',
                'descripcion' => 'Apple Watch Series 8 GPS 45mm',
                'precio' => 429.99,
                'stock_actual' => 0, // Agotado
                'stock_minimo' => 6,
                'categoria_id' => $electronicos->id,
            ],
        ];

        foreach ($productos as $producto) {
            Producto::create($producto);
        }
    }
}