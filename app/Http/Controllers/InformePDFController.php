<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class InformePDFController extends Controller
{
    public function descargarInforme(Request $request)
    {
        $meses = $request->input('meses', []);
        if (empty($meses)) {
            $meses = [date('Y-m')];
        }

        $nombresTours = [
            'Tour Volcán Masaya',
            'Tour Granada Colonial',
            'Tour Isletas de Granada',
        ];
        $preciosTours = [
            'Tour Volcán Masaya' => 25.00,
            'Tour Granada Colonial' => 30.00,
            'Tour Isletas de Granada' => 20.00,
        ];

        $mesesData = [];
        foreach ($meses as $mes) {
            // Obtener todos los domingos del mes
            $primerDia = Carbon::createFromFormat('Y-m', $mes)->startOfMonth();
            $ultimoDia = Carbon::createFromFormat('Y-m', $mes)->endOfMonth();
            $domingos = [];
            $fecha = $primerDia->copy()->next(Carbon::SUNDAY);
            if ($primerDia->isSunday()) {
                $fecha = $primerDia->copy();
            }
            while ($fecha->lte($ultimoDia)) {
                $domingos[] = $fecha->copy();
                $fecha->addWeek();
            }

            $tours = [];
            foreach ($domingos as $domingo) {
                // Selecciona un tour aleatorio para este domingo
                $nombreTour = $nombresTours[array_rand($nombresTours)];
                $precio = $preciosTours[$nombreTour];
                $cupos_vendidos = rand(15, 50);
                $menores = rand(3, min(15, $cupos_vendidos));
                $mayores = $cupos_vendidos - $menores;

                $tours[] = [
                    'fecha' => $domingo->format('d/m/Y'),
                    'nombre' => $nombreTour,
                    'cupos_vendidos' => $cupos_vendidos,
                    'menores' => $menores,
                    'mayores' => $mayores,
                    'precio' => $precio,
                    'subtotal' => $cupos_vendidos * $precio,
                ];
            }

            $total = array_sum(array_column($tours, 'subtotal'));

            $mesesData[] = [
                'mes' => $mes,
                'tours' => $tours,
                'total' => $total,
            ];
        }

        $fecha_emision = Carbon::now('America/El_Salvador')->format('d/m/Y');
        $fecha_hora = Carbon::now('America/El_Salvador')->format('Ymd_His');
        $correlativo = uniqid();

        $data = [
            'titulo' => 'Informe de Cupos Vendidos Mensuales por Tour',
            'mesesData' => $mesesData,
            'fecha_emision' => $fecha_emision,
            'direccion' => 'Chalatenango, El Salvador',
        ];

        // Establecer el locale de Carbon a español
        Carbon::setLocale('es');

        $nombreArchivo = "informe_{$correlativo}_{$fecha_hora}.pdf";

        $pdf = Pdf::loadView('informes.informe', $data);
        return $pdf->stream($nombreArchivo); // Cambia download() por stream()
    }
}
