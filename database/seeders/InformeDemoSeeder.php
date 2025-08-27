<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Tour;
use App\Models\Reserva;
use App\Models\DetalleReservaTour;
use App\Models\Cliente;
use App\Models\Empleado;
use App\Models\Transporte;
use Carbon\Carbon;

class InformeDemoSeeder extends Seeder
{
    public function run(): void
    {
        // Verificar si ya existen datos
        if (DetalleReservaTour::count() > 0) {
            $this->command->info('Ya existen datos de demostración');
            return;
        }

        // Obtener un transporte existente o crear uno
        $transporte = Transporte::first();
        if (!$transporte) {
            $transporte = Transporte::create([
                'nombre' => 'Autobus Turístico',
                'capacidad' => 50
            ]);
        }

        // Crear tours de ejemplo para agosto 2025
        $tours = [
            [
                'nombre' => 'Tour Volcán Masaya',
                'categoria' => 'NACIONAL',
                'incluye' => 'Transporte, Guía, Entrada',
                'no_incluye' => 'Comida, Bebidas',
                'cupo_min' => 10,
                'cupo_max' => 40,
                'punto_salida' => 'Terminal de Oriente',
                'fecha_salida' => Carbon::create(2025, 8, 10, 8, 0, 0),
                'fecha_regreso' => Carbon::create(2025, 8, 10, 18, 0, 0),
                'precio' => 25.00,
                'estado' => 'DISPONIBLE',
                'transporte_id' => $transporte->id
            ],
            [
                'nombre' => 'Tour Granada Colonial',
                'categoria' => 'NACIONAL',
                'incluye' => 'Transporte, Guía, Almuerzo',
                'no_incluye' => 'Bebidas alcohólicas',
                'cupo_min' => 8,
                'cupo_max' => 35,
                'punto_salida' => 'Terminal de Oriente',
                'fecha_salida' => Carbon::create(2025, 8, 17, 7, 30, 0),
                'fecha_regreso' => Carbon::create(2025, 8, 17, 19, 0, 0),
                'precio' => 30.00,
                'estado' => 'DISPONIBLE',
                'transporte_id' => $transporte->id
            ],
            [
                'nombre' => 'Tour Ruta de las Flores',
                'categoria' => 'NACIONAL',
                'incluye' => 'Transporte, Guía, Degustación café',
                'no_incluye' => 'Almuerzo, Souvenirs',
                'cupo_min' => 12,
                'cupo_max' => 45,
                'punto_salida' => 'Terminal de Occidente',
                'fecha_salida' => Carbon::create(2025, 8, 24, 6, 0, 0),
                'fecha_regreso' => Carbon::create(2025, 8, 24, 20, 0, 0),
                'precio' => 35.00,
                'estado' => 'DISPONIBLE',
                'transporte_id' => $transporte->id
            ]
        ];

        foreach ($tours as $tourData) {
            Tour::create($tourData);
        }

        // Obtener un cliente y empleado existente o crear uno
        $cliente = Cliente::first();
        if (!$cliente) {
            // Necesito crear un tipo de documento y usuario primero
            $tipoDocumento = \App\Models\TipoDocumento::first();
            if (!$tipoDocumento) {
                $tipoDocumento = \App\Models\TipoDocumento::create([
                    'nombre' => 'DUI'
                ]);
            }

            $user = \App\Models\User::create([
                'name' => 'Juan Pérez',
                'email' => 'juan.perez@example.com',
                'password' => bcrypt('password123')
            ]);

            $cliente = Cliente::create([
                'nombre' => 'Juan',
                'apellido' => 'Pérez',
                'numero_identificacion' => '12345678-9',
                'fecha_nacimiento' => Carbon::create(1990, 5, 15),
                'genero' => 'MASCULINO',
                'direccion' => 'San Salvador',
                'telefono' => '77777777',
                'user_id' => $user->id,
                'tipo_documento_id' => $tipoDocumento->id
            ]);
        }

        $empleado = Empleado::first();
        if (!$empleado) {
            $userEmpleado = \App\Models\User::create([
                'name' => 'María González',
                'email' => 'maria.gonzalez@agencia.com',
                'password' => bcrypt('password123')
            ]);

            $empleado = Empleado::create([
                'cargo' => 'Agente de Ventas',
                'telefono' => '22222222',
                'user_id' => $userEmpleado->id
            ]);
        }

        // Crear reservas y detalles para los tours
        $toursCreados = Tour::all();
        
        foreach ($toursCreados as $tour) {
            // Crear 1-2 reservas por tour
            $numReservas = rand(1, 2);
            
            for ($i = 0; $i < $numReservas; $i++) {
                $mayores = rand(1, 4);
                $menores = rand(0, 2);
                $cuposReservados = $mayores + $menores;
                $precioTotal = $cuposReservados * $tour->precio;
                
                $reserva = Reserva::create([
                    'fecha' => Carbon::now()->subDays(rand(1, 30)),
                    'estado' => 'CONFIRMADA',
                    'mayores_edad' => $mayores,
                    'menores_edad' => $menores,
                    'total' => $precioTotal,
                    'cliente_id' => $cliente->id,
                    'empleado_id' => $empleado->id
                ]);

                DetalleReservaTour::create([
                    'fecha' => Carbon::parse($tour->fecha_salida)->format('Y-m-d'),
                    'cupos_reservados' => $cuposReservados,
                    'precio_unitario' => $tour->precio,
                    'precio_total' => $precioTotal,
                    'reserva_id' => $reserva->id,
                    'tour_id' => $tour->id
                ]);
            }
        }

        $this->command->info('Datos de demostración creados exitosamente');
    }
}
