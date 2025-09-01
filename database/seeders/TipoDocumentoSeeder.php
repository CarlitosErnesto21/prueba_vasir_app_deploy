<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TipoDocumentoSeeder extends Seeder
{
    public function run(): void
    {
        $tipos = [
            ['nombre' => 'Cédula'],           // ✅ 6 caracteres
            ['nombre' => 'Pasaporte'],        // ✅ 9 caracteres  
            ['nombre' => 'Tarjeta ID'],       // ✅ 10 caracteres
            ['nombre' => 'NIT'],              // ✅ 3 caracteres
            ['nombre' => 'RUC'],              // ✅ 3 caracteres
        ];

        foreach ($tipos as $tipo) {
            DB::table('tipos_documentos')->insert([
                'nombre' => $tipo['nombre'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}