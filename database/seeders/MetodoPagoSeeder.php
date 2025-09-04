<?php

namespace Database\Seeders;

use App\Models\MetodoPago;
use Illuminate\Database\Seeder;

class MetodoPagoSeeder extends Seeder
{
    public function run(): void
    {
        $metodos = [
            ['metodo_pago' => 'Efectivo'],                    // ✅ Usar metodo_pago, no nombre
            ['metodo_pago' => 'Tarjeta de Débito'],
            ['metodo_pago' => 'Tarjeta de Crédito'],
            ['metodo_pago' => 'Transferencia Bancaria'],
            ['metodo_pago' => 'PayPal'],
            ['metodo_pago' => 'Cheque'],
            ['metodo_pago' => 'Bitcoin'],
        ];

        foreach ($metodos as $metodo) {
            MetodoPago::create($metodo);
        }
    }
}