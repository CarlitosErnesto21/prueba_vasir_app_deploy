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

class InformeDatosPruebaSeeder extends Seeder
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

        // Crear tours para diferentes fechas (julio 2025, septiembre 2025, octubre 2025)
        $toursAdicionales = [
            // Julio 2025
            [
                'nombre' => 'Tour Suchitoto Colonial',
                'categoria' => 'NACIONAL',
                'incluye' => 'Transporte, Guía, Almuerzo típico',
                'no_incluye' => 'Bebidas, Compras personales',
                'cupo_min' => 8,
                'cupo_max' => 30,
                'punto_salida' => 'Terminal de Oriente',
                'fecha_salida' => Carbon::create(2025, 7, 13, 7, 0, 0),
                'fecha_regreso' => Carbon::create(2025, 7, 13, 18, 30, 0),
                'precio' => 28.00,
                'estado' => 'DISPONIBLE',
                'transporte_id' => $transporte->id
            ],
            [
                'nombre' => 'Tour Playa El Tunco',
                'categoria' => 'NACIONAL',
                'incluye' => 'Transporte, Guía de playa, Sombrilla',
                'no_incluye' => 'Almuerzo, Bebidas, Surf',
                'cupo_min' => 15,
                'cupo_max' => 50,
                'punto_salida' => 'Terminal de Occidente',
                'fecha_salida' => Carbon::create(2025, 7, 20, 6, 30, 0),
                'fecha_regreso' => Carbon::create(2025, 7, 20, 19, 0, 0),
                'precio' => 22.00,
                'estado' => 'DISPONIBLE',
                'transporte_id' => $transporte->id
            ],
            [
                'nombre' => 'Tour Cerro Verde y Volcán Izalco',
                'categoria' => 'NACIONAL',
                'incluye' => 'Transporte, Guía especializado, Entrada al parque',
                'no_incluye' => 'Almuerzo, Equipo de camping',
                'cupo_min' => 10,
                'cupo_max' => 35,
                'punto_salida' => 'Terminal de Occidente',
                'fecha_salida' => Carbon::create(2025, 7, 27, 5, 30, 0),
                'fecha_regreso' => Carbon::create(2025, 7, 27, 20, 0, 0),
                'precio' => 32.00,
                'estado' => 'DISPONIBLE',
                'transporte_id' => $transporte->id
            ],
            
            // Septiembre 2025
            [
                'nombre' => 'Tour Joya de Cerén (Pompeya de América)',
                'categoria' => 'NACIONAL',
                'incluye' => 'Transporte, Guía arqueólogo, Entrada al sitio',
                'no_incluye' => 'Almuerzo, Souvenirs',
                'cupo_min' => 8,
                'cupo_max' => 25,
                'punto_salida' => 'Terminal de Oriente',
                'fecha_salida' => Carbon::create(2025, 9, 7, 8, 0, 0),
                'fecha_regreso' => Carbon::create(2025, 9, 7, 16, 0, 0),
                'precio' => 26.00,
                'estado' => 'DISPONIBLE',
                'transporte_id' => $transporte->id
            ],
            [
                'nombre' => 'Tour Lago de Coatepeque',
                'categoria' => 'NACIONAL',
                'incluye' => 'Transporte, Guía, Paseo en lancha, Almuerzo',
                'no_incluye' => 'Bebidas alcohólicas, Actividades acuáticas',
                'cupo_min' => 12,
                'cupo_max' => 40,
                'punto_salida' => 'Terminal de Occidente',
                'fecha_salida' => Carbon::create(2025, 9, 14, 7, 0, 0),
                'fecha_regreso' => Carbon::create(2025, 9, 14, 18, 0, 0),
                'precio' => 35.00,
                'estado' => 'DISPONIBLE',
                'transporte_id' => $transporte->id
            ],
            [
                'nombre' => 'Tour Tazumal y Casa Blanca',
                'categoria' => 'NACIONAL',
                'incluye' => 'Transporte, Guía arqueólogo, Entradas, Museo',
                'no_incluye' => 'Almuerzo, Compras',
                'cupo_min' => 10,
                'cupo_max' => 30,
                'punto_salida' => 'Terminal de Occidente',
                'fecha_salida' => Carbon::create(2025, 9, 21, 8, 30, 0),
                'fecha_regreso' => Carbon::create(2025, 9, 21, 17, 0, 0),
                'precio' => 24.00,
                'estado' => 'DISPONIBLE',
                'transporte_id' => $transporte->id
            ],
            [
                'nombre' => 'Tour Montecristo - Bosque Nebuloso',
                'categoria' => 'NACIONAL',
                'incluye' => 'Transporte, Guía naturalista, Entrada al parque',
                'no_incluye' => 'Almuerzo, Equipo de senderismo',
                'cupo_min' => 8,
                'cupo_max' => 20,
                'punto_salida' => 'Terminal de Occidente',
                'fecha_salida' => Carbon::create(2025, 9, 28, 5, 0, 0),
                'fecha_regreso' => Carbon::create(2025, 9, 28, 19, 30, 0),
                'precio' => 38.00,
                'estado' => 'DISPONIBLE',
                'transporte_id' => $transporte->id
            ],

            // Octubre 2025
            [
                'nombre' => 'Tour El Pital y Miramundo',
                'categoria' => 'NACIONAL',
                'incluye' => 'Transporte, Guía, Desayuno típico, Entrada',
                'no_incluye' => 'Almuerzo, Ropa de abrigo',
                'cupo_min' => 10,
                'cupo_max' => 35,
                'punto_salida' => 'Terminal de Oriente',
                'fecha_salida' => Carbon::create(2025, 10, 5, 4, 30, 0),
                'fecha_regreso' => Carbon::create(2025, 10, 5, 20, 0, 0),
                'precio' => 40.00,
                'estado' => 'DISPONIBLE',
                'transporte_id' => $transporte->id
            ],
            [
                'nombre' => 'Tour Cascadas de Tamanique',
                'categoria' => 'NACIONAL',
                'incluye' => 'Transporte, Guía local, Almuerzo en rancho',
                'no_incluye' => 'Traje de baño, Zapatos de agua',
                'cupo_min' => 12,
                'cupo_max' => 30,
                'punto_salida' => 'Terminal de Occidente',
                'fecha_salida' => Carbon::create(2025, 10, 12, 6, 0, 0),
                'fecha_regreso' => Carbon::create(2025, 10, 12, 18, 30, 0),
                'precio' => 29.00,
                'estado' => 'DISPONIBLE',
                'transporte_id' => $transporte->id
            ],
            [
                'nombre' => 'Tour Ataco y Juayúa (Ruta de las Flores)',
                'categoria' => 'NACIONAL',
                'incluye' => 'Transporte, Guía, Degustación de café, Almuerzo',
                'no_incluye' => 'Compras artesanales, Bebidas adicionales',
                'cupo_min' => 15,
                'cupo_max' => 45,
                'punto_salida' => 'Terminal de Occidente',
                'fecha_salida' => Carbon::create(2025, 10, 19, 6, 30, 0),
                'fecha_regreso' => Carbon::create(2025, 10, 19, 19, 0, 0),
                'precio' => 33.00,
                'estado' => 'DISPONIBLE',
                'transporte_id' => $transporte->id
            ],
            [
                'nombre' => 'Tour Playa Los Cóbanos - Arrecife',
                'categoria' => 'NACIONAL',
                'incluye' => 'Transporte, Guía marino, Snorkel básico, Almuerzo',
                'no_incluye' => 'Equipo profesional de buceo, Bebidas',
                'cupo_min' => 10,
                'cupo_max' => 25,
                'punto_salida' => 'Terminal de Occidente',
                'fecha_salida' => Carbon::create(2025, 10, 26, 6, 0, 0),
                'fecha_regreso' => Carbon::create(2025, 10, 26, 18, 0, 0),
                'precio' => 36.00,
                'estado' => 'DISPONIBLE',
                'transporte_id' => $transporte->id
            ]
        ];

        // Crear los tours
        foreach ($toursAdicionales as $tourData) {
            $tour = Tour::create($tourData);

            // Crear 1-3 reservas aleatorias por tour
            $numReservas = rand(1, 3);
            
            for ($i = 0; $i < $numReservas; $i++) {
                $mayores = rand(1, 5);
                $menores = rand(0, 3);
                $cuposReservados = $mayores + $menores;
                $precioTotal = $cuposReservados * $tour->precio;
                
                $reserva = Reserva::create([
                    'fecha' => Carbon::now()->subDays(rand(1, 45)),
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

        $this->command->info('Datos adicionales de prueba creados exitosamente para julio, septiembre y octubre 2025');
    }
}
