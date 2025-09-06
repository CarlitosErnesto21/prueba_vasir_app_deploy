<?php

namespace App\Http\Controllers;

use App\Models\Reserva;
use App\Models\Cliente;
use App\Models\DetalleReservaTour;
use App\Models\Tour;
use App\Models\TipoDocumento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ReservaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Obtener todas las reservas con sus relaciones
        $reservas = Reserva::with(['cliente', 'empleado'])->get();
        return response()->json($reservas);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validar los datos del formulario
        $validated = $request->validate([
            'fecha' => 'required|date',
            'estado' => 'required|in:PENDIENTE,CONFIRMADA,RECHAZADA,REPROGRAMADA,FINALIZADA',
            'mayores_edad' => 'required|integer|min:1',
            'menores_edad' => 'nullable|integer|min:0',
            'cliente_id' => 'required|exists:clientes,id',
            'empleado_id' => 'required|exists:empleados,id',
            'total' => 'required|numeric|min:0'
        ]);

        // Crear una nueva reserva
        $reserva = Reserva::create($validated);

        return response()->json([
            'message' => 'Reserva creada exitosamente',
            'reserva' => $reserva,
        ]);
    }

    /**
     * Crear reserva de tour desde el modal del cliente
     */
    public function crearReservaTour(Request $request)
    {
        try {
            // Verificar que el usuario esté autenticado
            if (!Auth::check()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Debe iniciar sesión para realizar una reserva.'
                ], 401);
            }

            $user = Auth::user();

            // Verificar que el usuario tenga rol de cliente usando consulta directa
            $hasClienteRole = DB::table('model_has_roles')
                ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
                ->where('model_has_roles.model_type', 'App\\Models\\User')
                ->where('model_has_roles.model_id', $user->id)
                ->where('roles.name', 'cliente')
                ->exists();
            
            if (!$hasClienteRole) {
                return response()->json([
                    'success' => false,
                    'message' => 'Solo los usuarios con rol de cliente pueden generar reservas. Su rol actual no permite esta acción.'
                ], 403);
            }

            // Validar los datos de entrada
            $validated = $request->validate([
                'tour_id' => 'required|exists:tours,id',
                'cupos_adultos' => 'required|integer|min:1',
                'cupos_menores' => 'nullable|integer|min:0',
                'cliente_data' => 'required|array',
                'cliente_data.numero_identificacion' => 'required|string|max:25',
                'cliente_data.fecha_nacimiento' => 'required|date',
                'cliente_data.genero' => 'required|in:MASCULINO,FEMENINO',
                'cliente_data.direccion' => 'required|string|max:200',
                'cliente_data.telefono' => 'required|string|max:30',
                'cliente_data.tipo_documento_id' => 'required|exists:tipos_documentos,id'
            ]);

            // Validar que la fecha de nacimiento sea válida y no sea futura
            try {
                $fechaNacimientoCarbon = Carbon::parse($validated['cliente_data']['fecha_nacimiento']);
                if ($fechaNacimientoCarbon->isFuture()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'La fecha de nacimiento no puede ser futura.'
                    ], 422);
                }
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Formato de fecha de nacimiento inválido.'
                ], 422);
            }

            DB::beginTransaction();

            // 1. Verificar si ya existe un cliente para este usuario
            $cliente = Cliente::where('user_id', $user->id)->first();

            // Formatear la fecha de nacimiento para MySQL
            $fechaNacimiento = $fechaNacimientoCarbon->format('Y-m-d');

            if (!$cliente) {
                // 2. Crear el cliente si no existe
                $cliente = Cliente::create([
                    'numero_identificacion' => $validated['cliente_data']['numero_identificacion'],
                    'fecha_nacimiento' => $fechaNacimiento,
                    'genero' => strtoupper($validated['cliente_data']['genero']),
                    'direccion' => $validated['cliente_data']['direccion'],
                    'telefono' => $validated['cliente_data']['telefono'],
                    'user_id' => $user->id,
                    'tipo_documento_id' => $validated['cliente_data']['tipo_documento_id']
                ]);
            } else {
                // Actualizar datos del cliente existente
                $cliente->update([
                    'numero_identificacion' => $validated['cliente_data']['numero_identificacion'],
                    'fecha_nacimiento' => $fechaNacimiento,
                    'genero' => strtoupper($validated['cliente_data']['genero']),
                    'direccion' => $validated['cliente_data']['direccion'],
                    'telefono' => $validated['cliente_data']['telefono'],
                    'tipo_documento_id' => $validated['cliente_data']['tipo_documento_id']
                ]);
            }

            // 3. Obtener información del tour
            $tour = Tour::findOrFail($validated['tour_id']);

            // 4. Calcular totales
            $cupos_totales = $validated['cupos_adultos'] + ($validated['cupos_menores'] ?? 0);
            
            // 5. Verificar cupos disponibles
            $cuposDisponibles = $tour->cupos_disponibles;
            if ($cupos_totales > $cuposDisponibles) {
                return response()->json([
                    'success' => false,
                    'message' => "Solo hay {$cuposDisponibles} cupos disponibles para este tour. Usted está intentando reservar {$cupos_totales} cupos."
                ], 422);
            }
            
            $precio_total = $cupos_totales * $tour->precio;

            // 6. Crear la reserva (sin empleado asignado inicialmente)
            $reserva = Reserva::create([
                'fecha' => Carbon::now()->toDateString(),
                'estado' => 'PENDIENTE',
                'mayores_edad' => $validated['cupos_adultos'],
                'menores_edad' => $validated['cupos_menores'] ?? null,
                'total' => $precio_total,
                'cliente_id' => $cliente->id,
                'empleado_id' => null // El empleado será asignado posteriormente
            ]);

            // 7. Crear el detalle de reserva de tour
            $detalleReserva = DetalleReservaTour::create([
                'fecha' => Carbon::now()->toDateString(),
                'cupos_reservados' => $cupos_totales,
                'precio_unitario' => $tour->precio,
                'precio_total' => $precio_total,
                'reserva_id' => $reserva->id,
                'tour_id' => $tour->id
            ]);

            DB::commit();

            // Refrescar el tour para obtener los cupos actualizados
            $tour->refresh();
            
            // Recalcular cupos disponibles después de la reserva
            $cuposReservadosTotal = $tour->detalleReservas()
                ->whereHas('reserva', function($query) {
                    $query->where('estado', '!=', 'cancelada');
                })
                ->sum('cupos_reservados');
            
            $cuposDisponiblesActualizados = max(0, $tour->cupo_max - $cuposReservadosTotal);
            
            // Debug log
            Log::info("Después de reserva - Tour {$tour->id}: cupo_max={$tour->cupo_max}, reservados={$cuposReservadosTotal}, disponibles={$cuposDisponiblesActualizados}");

            return response()->json([
                'success' => true,
                'message' => 'Reserva creada exitosamente',
                'data' => [
                    'reserva' => $reserva,
                    'cliente' => $cliente,
                    'detalle' => $detalleReserva,
                    'tour' => $tour,
                    'cupos_disponibles_actualizados' => $cuposDisponiblesActualizados
                ]
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Reserva $reserva)
    {
        // Mostrar los detalles de una reserva específica con sus relaciones
        $reserva->load(['cliente', 'empleado']);
        return response()->json($reserva);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Reserva $reserva)
    {
        // Validar los datos del formulario
        $validated = $request->validate([
            'fecha' => 'required|date',
            'estado' => 'required|in:PENDIENTE,CONFIRMADA,RECHAZADA,REPROGRAMADA,FINALIZADA',
            'mayores_edad' => 'required|integer|min:1',
            'menores_edad' => 'nullable|integer|min:0',
            'cliente_id' => 'required|exists:clientes,id',
            'empleado_id' => 'required|exists:empleados,id',
            'total' => 'required|numeric|min:0'
        ]);

        // Actualizar la reserva
        $reserva->update($validated);

        return response()->json([
            'message' => 'Reserva actualizada exitosamente',
            'reserva' => $reserva,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Reserva $reserva)
    {
        // Eliminar la reserva
        $reserva->delete();

        return response()->json([
            'message' => 'Reserva eliminada exitosamente',
        ]);
    }
}
