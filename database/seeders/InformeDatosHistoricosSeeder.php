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

class InformeDatosHistoricosSeeder extends Seeder
{
    public function run(): void
    {
        // Obtener el transporte, cliente y empleado existentes
        $transporte = Transporte::first();
        $cliente = Cliente::first();
        $empleado = Empleado::first();

        if (!$transporte || !$cliente || !$empleado) {
            $this->command->error('Ejecuta primero InformeDemoSeeder para crear los datos base');
            return;
        }

        // Crear tours para fechas pasadas (mayo 2025, junio 2025)
        $toursHistoricos = [
            // Mayo 2025
            [
                'nombre' => 'Tour Volcán Santa Ana (Ilamatepec)',
                'categoria' => 'NACIONAL',
                'incluye' => 'Transporte, Guía especializado, Entrada al parque, Desayuno',
                'no_incluye' => 'Almuerzo, Equipo de senderismo',
                'cupo_min' => 8,
                'cupo_max' => 25,
                'punto_salida' => 'Terminal de Occidente',
                'fecha_salida' => Carbon::create(2025, 5, 4, 5, 0, 0),
                'fecha_regreso' => Carbon::create(2025, 5, 4, 18, 0, 0),
                'precio' => 35.00,
                'estado' => 'COMPLETADO',
                'transporte_id' => $transporte->id
            ],
            [
                'nombre' => 'Tour Bahía de Jiquilisco',
                'categoria' => 'NACIONAL',
                'incluye' => 'Transporte, Guía, Paseo en lancha, Almuerzo de mariscos',
                'no_incluye' => 'Bebidas, Actividades extras',
                'cupo_min' => 10,
                'cupo_max' => 30,
                'punto_salida' => 'Terminal de Oriente',
                'fecha_salida' => Carbon::create(2025, 5, 11, 6, 30, 0),
                'fecha_regreso' => Carbon::create(2025, 5, 11, 19, 30, 0),
                'precio' => 42.00,
                'estado' => 'COMPLETADO',
                'transporte_id' => $transporte->id
            ],
            [
                'nombre' => 'Tour El Imposible - Parque Nacional',
                'categoria' => 'NACIONAL',
                'incluye' => 'Transporte, Guía naturalista, Entrada, Almuerzo típico',
                'no_incluye' => 'Equipo de camping, Bebidas',
                'cupo_min' => 8,
                'cupo_max' => 20,
                'punto_salida' => 'Terminal de Occidente',
                'fecha_salida' => Carbon::create(2025, 5, 18, 5, 30, 0),
                'fecha_regreso' => Carbon::create(2025, 5, 18, 20, 0, 0),
                'precio' => 38.00,
                'estado' => 'COMPLETADO',
                'transporte_id' => $transporte->id
            ],
            [
                'nombre' => 'Tour Playa El Zonte - Surf',
                'categoria' => 'NACIONAL',
                'incluye' => 'Transporte, Instructor de surf, Tabla básica, Almuerzo',
                'no_incluye' => 'Equipo profesional, Bebidas, Protector solar',
                'cupo_min' => 10,
                'cupo_max' => 25,
                'punto_salida' => 'Terminal de Occidente',
                'fecha_salida' => Carbon::create(2025, 5, 25, 6, 0, 0),
                'fecha_regreso' => Carbon::create(2025, 5, 25, 18, 30, 0),
                'precio' => 45.00,
                'estado' => 'COMPLETADO',
                'transporte_id' => $transporte->id
            ],

            // Junio 2025
            [
                'nombre' => 'Tour Perquín y Museo de la Revolución',
                'categoria' => 'NACIONAL',
                'incluye' => 'Transporte, Guía histórico, Entrada al museo, Almuerzo',
                'no_incluye' => 'Souvenirs, Bebidas adicionales',
                'cupo_min' => 8,
                'cupo_max' => 30,
                'punto_salida' => 'Terminal de Oriente',
                'fecha_salida' => Carbon::create(2025, 6, 1, 7, 0, 0),
                'fecha_regreso' => Carbon::create(2025, 6, 1, 19, 0, 0),
                'precio' => 32.00,
                'estado' => 'COMPLETADO',
                'transporte_id' => $transporte->id
            ],
            [
                'nombre' => 'Tour Lago de Ilopango',
                'categoria' => 'NACIONAL',
                'incluye' => 'Transporte, Guía, Paseo en lancha, Almuerzo con vista al lago',
                'no_incluye' => 'Deportes acuáticos, Bebidas alcohólicas',
                'cupo_min' => 12,
                'cupo_max' => 35,
                'punto_salida' => 'Terminal de Oriente',
                'fecha_salida' => Carbon::create(2025, 6, 8, 7, 30, 0),
                'fecha_regreso' => Carbon::create(2025, 6, 8, 17, 30, 0),
                'precio' => 28.00,
                'estado' => 'COMPLETADO',
                'transporte_id' => $transporte->id
            ],
            [
                'nombre' => 'Tour Cinquera - Bosque y Cascadas',
                'categoria' => 'NACIONAL',
                'incluye' => 'Transporte, Guía ecológico, Caminata, Almuerzo orgánico',
                'no_incluye' => 'Zapatos de senderismo, Repelente',
                'cupo_min' => 8,
                'cupo_max' => 22,
                'punto_salida' => 'Terminal de Oriente',
                'fecha_salida' => Carbon::create(2025, 6, 15, 6, 0, 0),
                'fecha_regreso' => Carbon::create(2025, 6, 15, 18, 0, 0),
                'precio' => 34.00,
                'estado' => 'COMPLETADO',
                'transporte_id' => $transporte->id
            ],
            [
                'nombre' => 'Tour Playa Costa del Sol',
                'categoria' => 'NACIONAL',
                'incluye' => 'Transporte, Guía, Sombrilla, Almuerzo de mariscos',
                'no_incluye' => 'Bebidas, Actividades acuáticas',
                'cupo_min' => 15,
                'cupo_max' => 40,
                'punto_salida' => 'Terminal de Oriente',
                'fecha_salida' => Carbon::create(2025, 6, 22, 7, 0, 0),
                'fecha_regreso' => Carbon::create(2025, 6, 22, 18, 0, 0),
                'precio' => 26.00,
                'estado' => 'COMPLETADO',
                'transporte_id' => $transporte->id
            ],
            [
                'nombre' => 'Tour San Ignacio y Termas de Santa Teresa',
                'categoria' => 'NACIONAL',
                'incluye' => 'Transporte, Guía, Entrada a termas, Almuerzo típico',
                'no_incluye' => 'Traje de baño, Toallas, Masajes',
                'cupo_min' => 10,
                'cupo_max' => 28,
                'punto_salida' => 'Terminal de Oriente',
                'fecha_salida' => Carbon::create(2025, 6, 29, 6, 30, 0),
                'fecha_regreso' => Carbon::create(2025, 6, 29, 19, 30, 0),
                'precio' => 36.00,
                'estado' => 'COMPLETADO',
                'transporte_id' => $transporte->id
            ]
        ];

        // Crear los tours históricos
        foreach ($toursHistoricos as $tourData) {
            $tour = Tour::create($tourData);

            // Crear 2-4 reservas por tour histórico (más datos para meses pasados)
            $numReservas = rand(2, 4);
            
            for ($i = 0; $i < $numReservas; $i++) {
                $mayores = rand(1, 6);
                $menores = rand(0, 4);
                $cuposReservados = $mayores + $menores;
                $precioTotal = $cuposReservados * $tour->precio;
                
                $reserva = Reserva::create([
                    'fecha' => Carbon::parse($tour->fecha_salida)->subDays(rand(7, 30)),
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

        $this->command->info('Datos históricos de prueba creados exitosamente para mayo y junio 2025');
    }
}
