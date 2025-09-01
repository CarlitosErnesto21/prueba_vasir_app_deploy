<?php

namespace Database\Seeders;

use App\Models\CategoriaProducto;
use Illuminate\Database\Seeder;

class CategoriaProductoSeeder extends Seeder
{
    public function run(): void
    {
        $categorias = [
            ['nombre' => 'Electrónicos'],
            ['nombre' => 'Ropa'],
            ['nombre' => 'Hogar'],
            ['nombre' => 'Deportes'],
            ['nombre' => 'Libros'],
            ['nombre' => 'Juguetes'],
            ['nombre' => 'Belleza'],
            ['nombre' => 'Automóvil'],
        ];

        foreach ($categorias as $categoria) {
            CategoriaProducto::create($categoria);
        }
    }
}