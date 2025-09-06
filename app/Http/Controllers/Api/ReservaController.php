<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Reserva;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReservaController extends Controller
{
    /**
     * Obtener todas las reservas con filtros
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Reserva::with(['cliente', 'detallesTours.tour']);

            // Aplicar filtros por tipo (solo tours por ahora)
            if ($request->filled('tipo')) {
                switch ($request->tipo) {
                    case 'tours':
                        $query->whereHas('detallesTours');
                        break;
                    // Para hoteles y aerolíneas se pueden agregar más adelante
                    case 'hoteles':
                        // $query->whereHas('detallesHoteles');
                        break;
                    case 'aerolineas':
                        // $query->whereHas('detallesAerolineas');
                        break;
                }
            }

            if ($request->filled('estado')) {
                $query->where('estado', $request->estado);
            }

            if ($request->filled('fecha_inicio')) {
                $query->whereDate('fecha', '>=', $request->fecha_inicio);
            }

            if ($request->filled('fecha_fin')) {
                $query->whereDate('fecha', '<=', $request->fecha_fin);
            }

            if ($request->filled('busqueda')) {
                $busqueda = $request->busqueda;
                $query->whereHas('cliente', function ($q) use ($busqueda) {
                    $q->where('nombres', 'like', "%{$busqueda}%")
                      ->orWhere('correo', 'like', "%{$busqueda}%");
                })->orWhereHas('detallesTours.tour', function ($q) use ($busqueda) {
                    $q->where('nombre', 'like', "%{$busqueda}%");
                });
            }

            $reservas = $query->orderBy('fecha', 'desc')
                           ->paginate($request->get('per_page', 15));

            // Transformar los datos para la respuesta
            $transformedData = $reservas->getCollection()->map(function ($reserva) {
                $tourNombre = $reserva->detallesTours->first() ? 
                             $reserva->detallesTours->first()->tour->nombre : 'N/A';
                
                return [
                    'id' => $reserva->id,
                    'fecha_reserva' => $reserva->fecha, // Usar 'fecha' del modelo
                    'estado' => $reserva->estado,
                    'cliente' => [
                        'nombres' => $reserva->cliente ? $reserva->cliente->nombres : 'Cliente no asignado',
                        'correo' => $reserva->cliente ? $reserva->cliente->correo : 'Sin correo'
                    ],
                    'entidad_nombre' => $tourNombre,
                    'tipo' => 'Tour',
                    'total' => $reserva->total,
                    'mayores_edad' => $reserva->mayores_edad,
                    'menores_edad' => $reserva->menores_edad
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $transformedData,
                'pagination' => [
                    'current_page' => $reservas->currentPage(),
                    'total_pages' => $reservas->lastPage(),
                    'per_page' => $reservas->perPage(),
                    'total' => $reservas->total()
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener las reservas',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener resumen de reservas agrupadas
     */
    public function resumen(Request $request): JsonResponse
    {
        try {
            // Resumen de tours usando la estructura correcta de BD
            $toursResumen = DB::table('reservas')
                ->join('detalles_reservas_tours', 'reservas.id', '=', 'detalles_reservas_tours.reserva_id')
                ->join('tours', 'detalles_reservas_tours.tour_id', '=', 'tours.id')
                ->select(
                    'tours.nombre',
                    DB::raw("'tours' as tipo"),
                    DB::raw('COUNT(CASE WHEN reservas.estado = "Pendiente" THEN 1 END) as total_pendientes'),
                    DB::raw('COUNT(CASE WHEN reservas.estado = "Confirmado" THEN 1 END) as total_confirmadas'),
                    DB::raw('COUNT(CASE WHEN reservas.estado = "Rechazada" THEN 1 END) as total_rechazadas'),
                    DB::raw('COUNT(*) as total_reservas')
                )
                ->groupBy('tours.id', 'tours.nombre')
                ->get();

            // Por ahora solo manejamos tours, pero se puede extender para hoteles y aerolíneas
            $resumen = $toursResumen;

            return response()->json([
                'success' => true,
                'data' => $resumen
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener el resumen de reservas',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Confirmar una reserva
     */
    public function confirmar(Request $request, $id): JsonResponse
    {
        try {
            $reserva = Reserva::findOrFail($id);
            
            // Log para debug más detallado
            \Log::info('Confirmando reserva - INICIO', [
                'id' => $id,
                'estado_actual' => $reserva->estado,
                'reserva_completa' => $reserva->toArray()
            ]);
            
            // Validar que se pueda confirmar
            if ($reserva->estado !== 'PENDIENTE') {
                return response()->json([
                    'success' => false,
                    'message' => "Solo se pueden confirmar reservas pendientes. Estado actual: {$reserva->estado}"
                ], 400);
            }
            
            \Log::info('Intentando actualizar estado a CONFIRMADA...');
            
            $resultado = $reserva->update([
                'estado' => 'CONFIRMADA' // Cambiar a CONFIRMADA en lugar de CONFIRMADO
            ]);
            
            \Log::info('Resultado de la actualización', [
                'resultado' => $resultado,
                'estado_después' => $reserva->fresh()->estado
            ]);

            // Recargar la reserva con relaciones
            $reserva = $reserva->fresh(['cliente', 'detallesTours.tour']);

            return response()->json([
                'success' => true,
                'message' => 'Reserva confirmada exitosamente',
                'data' => $reserva
            ]);

        } catch (\Exception $e) {
            \Log::error('Error al confirmar reserva', [
                'id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error al confirmar la reserva',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Rechazar una reserva
     */
    public function rechazar(Request $request, $id): JsonResponse
    {
        try {
            $reserva = Reserva::findOrFail($id);
            
            // Log para debug
            \Log::info('Rechazando reserva', [
                'id' => $id,
                'estado_actual' => $reserva->estado,
                'motivo' => $request->input('motivo')
            ]);
            
            // Ser más flexible con los estados
            $estadosPermitidos = ['PENDIENTE', 'Pendiente', 'pendiente', 'CONFIRMADO', 'Confirmado', 'confirmado'];
            if (!in_array($reserva->estado, $estadosPermitidos)) {
                return response()->json([
                    'success' => false,
                    'message' => "No se puede rechazar una reserva en estado: {$reserva->estado}"
                ], 400);
            }

            $motivo = $request->input('motivo', 'Sin motivo especificado');

            $reserva->update([
                'estado' => 'RECHAZADA'
            ]);

            // Recargar la reserva con relaciones
            $reserva = $reserva->fresh(['cliente', 'detallesTours.tour']);

            return response()->json([
                'success' => true,
                'message' => 'Reserva rechazada exitosamente',
                'data' => $reserva
            ]);

        } catch (\Exception $e) {
            \Log::error('Error al rechazar reserva', [
                'id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error al rechazar la reserva',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reprogramar una reserva
     */
    public function reprogramar(Request $request, $id): JsonResponse
    {
        try {
            $reserva = Reserva::findOrFail($id);
            
            // Log para debug
            \Log::info('Reprogramando reserva', [
                'id' => $id,
                'estado_actual' => $reserva->estado,
                'nueva_fecha' => $request->input('fecha_nueva'),
                'motivo' => $request->input('motivo')
            ]);
            
            // Ser más flexible con los estados
            $estadosPermitidos = ['PENDIENTE', 'Pendiente', 'pendiente', 'CONFIRMADO', 'Confirmado', 'confirmado'];
            if (!in_array($reserva->estado, $estadosPermitidos)) {
                return response()->json([
                    'success' => false,
                    'message' => "No se puede reprogramar una reserva en estado: {$reserva->estado}"
                ], 400);
            }

            $request->validate([
                'fecha_nueva' => 'required|date|after:today',
                'motivo' => 'required|string|max:255',
                'observaciones' => 'nullable|string|max:1000'
            ]);

            $fechaAnterior = $reserva->fecha;
            
            $reserva->update([
                'fecha' => $request->fecha_nueva,
                'estado' => 'REPROGRAMADA'
            ]);

            // Recargar la reserva con relaciones
            $reserva = $reserva->fresh(['cliente', 'detallesTours.tour']);

            return response()->json([
                'success' => true,
                'message' => 'Reserva reprogramada exitosamente',
                'data' => $reserva
            ]);

        } catch (\Exception $e) {
            \Log::error('Error al reprogramar reserva', [
                'id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error al reprogramar la reserva',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener historial de una reserva
     */
    public function historial($id): JsonResponse
    {
        try {
            $reserva = Reserva::with(['cliente', 'detallesTours.tour'])->findOrFail($id);
            
            // Simulamos el historial básico con la información disponible
            $historial = [
                [
                    'tipo' => 'creacion',
                    'fecha' => $reserva->created_at,
                    'descripcion' => 'Reserva creada',
                    'usuario' => 'Sistema',
                    'detalles' => null
                ]
            ];

            // Agregar eventos basados en el estado actual
            if ($reserva->estado === 'Confirmado') {
                $historial[] = [
                    'tipo' => 'confirmacion',
                    'fecha' => $reserva->updated_at,
                    'descripcion' => 'Reserva confirmada',
                    'usuario' => 'Administrador',
                    'detalles' => null
                ];
            } elseif ($reserva->estado === 'Rechazada') {
                $historial[] = [
                    'tipo' => 'rechazo',
                    'fecha' => $reserva->updated_at,
                    'descripcion' => 'Reserva rechazada',
                    'usuario' => 'Administrador',
                    'detalles' => null
                ];
            } elseif ($reserva->estado === 'Reprogramada') {
                $historial[] = [
                    'tipo' => 'reprogramacion',
                    'fecha' => $reserva->updated_at,
                    'descripcion' => 'Reserva reprogramada',
                    'usuario' => 'Administrador',
                    'detalles' => null
                ];
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'reserva' => $reserva,
                    'historial' => $historial
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener el historial',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
