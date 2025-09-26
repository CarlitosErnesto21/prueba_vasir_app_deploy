<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use App\Models\Tour;
use App\Models\DetalleReservaTour;
use App\Models\Reserva;
use Illuminate\Support\Facades\DB;

class InformePDFController extends Controller
{
    public function descargarInforme(Request $request)
    {
        // Establecer el locale de Carbon a español ANTES de cualquier uso de translatedFormat
        Carbon::setLocale('es');

        $meses = $request->input('meses', []);
        if (empty($meses)) {
            $meses = [date('Y-m')];
        }

        $mesesData = [];
        $mesesSinDatos = [];
        
        foreach ($meses as $mes) {
            // Convertir el mes a formato Carbon
            $fechaInicio = Carbon::createFromFormat('Y-m', $mes)->startOfMonth();
            $fechaFin = Carbon::createFromFormat('Y-m', $mes)->endOfMonth();

            // Obtener los detalles de reservas de tours para el mes especificado
            $detallesReservas = DetalleReservaTour::with(['tour', 'reserva'])
                ->whereHas('tour', function($query) use ($fechaInicio, $fechaFin) {
                    $query->whereBetween('fecha_salida', [$fechaInicio, $fechaFin]);
                })
                ->whereHas('reserva', function($query) {
                    $query->where('estado', 'CONFIRMADA');
                })
                ->get();

            $tours = [];
            foreach ($detallesReservas as $detalle) {
                $tour = $detalle->tour;
                $reserva = $detalle->reserva;
                
                $tours[] = [
                    'fecha' => Carbon::parse($tour->fecha_salida)->format('d/m/Y'),
                    'nombre' => $tour->nombre,
                    'cupos_vendidos' => $detalle->cupos_reservados,
                    'menores' => $reserva->menores_edad,
                    'mayores' => $reserva->mayores_edad,
                    'precio' => $detalle->precio_unitario,
                    'subtotal' => $detalle->precio_total,
                ];
            }

            // Si no hay reservas confirmadas, verificar si hay tours disponibles
            if (empty($tours)) {
                $toursDisponibles = Tour::whereBetween('fecha_salida', [$fechaInicio, $fechaFin])
                    ->where('estado', 'DISPONIBLE')
                    ->get();

                // Si tampoco hay tours disponibles, agregar a meses sin datos
                if ($toursDisponibles->isEmpty()) {
                    $mesesSinDatos[] = Carbon::createFromFormat('Y-m', $mes)->translatedFormat('F Y');
                    continue;
                }

                // Si hay tours disponibles pero sin ventas, mostrarlos con ceros
                foreach ($toursDisponibles as $tour) {
                    $tours[] = [
                        'fecha' => Carbon::parse($tour->fecha_salida)->format('d/m/Y'),
                        'nombre' => $tour->nombre,
                        'cupos_vendidos' => 0,
                        'menores' => 0,
                        'mayores' => 0,
                        'precio' => $tour->precio,
                        'subtotal' => 0,
                    ];
                }
            }

            // Solo agregar el mes si tiene datos
            if (!empty($tours)) {
                $total = array_sum(array_column($tours, 'subtotal'));
                $mesesData[] = [
                    'mes' => $mes,
                    'tours' => $tours,
                    'total' => $total,
                ];
            }
        }

        // Si es un solo mes y no tiene datos, retornar error JSON
        if (count($meses) === 1 && empty($mesesData)) {
            $mesNombre = Carbon::createFromFormat('Y-m', $meses[0])->translatedFormat('F Y');
            return response()->json([
                'error' => true,
                'message' => "No se encontraron registros de ventas para el mes de {$mesNombre}."
            ], 404);
        }

        // Si es un rango y no hay datos en ningún mes
        if (count($meses) > 1 && empty($mesesData)) {
            return response()->json([
                'error' => true,
                'message' => 'No se encontraron registros de ventas para el rango de fechas seleccionado.'
            ], 404);
        }

        $fecha_emision = Carbon::now('America/El_Salvador')->format('d/m/Y');
        $fecha_hora = Carbon::now('America/El_Salvador')->format('Ymd_His');

        $data = [
            'titulo' => 'Informe de Cupos Vendidos Mensuales por Tour',
            'mesesData' => $mesesData,
            'mesesSinDatos' => $mesesSinDatos,
            'fecha_emision' => $fecha_emision,
            'direccion' => 'Chalatenango, El Salvador',
        ];

        $nombreArchivo = "informe_{$fecha_hora}.pdf";

        $pdf = Pdf::loadView('informes.informe', $data);
        // Si se recibe el parámetro preview=1, mostrar en navegador (inline), si no, descargar (attachment)
        $dispositionType = $request->input('preview') == '1' ? 'inline' : 'attachment';
        return response($pdf->output(), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', $dispositionType . '; filename="' . $nombreArchivo . '"');
    }
}
