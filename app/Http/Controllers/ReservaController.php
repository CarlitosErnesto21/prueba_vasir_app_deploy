<?php

namespace App\Http\Controllers;

use App\Models\Reserva;
use Illuminate\Http\Request;

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
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validar los datos del formulario
        $validated = $request->validate([
            'fecha' => 'required|date',
            'estado' => 'required|in:Pendiente,Confirmada,Cancelada',
            'cliente_id' => 'required|exists:clientes,id',
            'empleado_id' => 'required|exists:empleados,id',
        ]);

        // Crear una nueva reserva
        $reserva = Reserva::create($validated);

        return response()->json([
            'message' => 'Reserva creada exitosamente',
            'reserva' => $reserva,
        ]);
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
     * Show the form for editing the specified resource.
     */
    public function edit(Reserva $reserva)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Reserva $reserva)
    {
        // Validar los datos del formulario
        $validated = $request->validate([
            'fecha' => 'required|date',
            'estado' => 'required|in:Pendiente,Confirmada,Cancelada',
            'cliente_id' => 'required|exists:clientes,id',
            'empleado_id' => 'required|exists:empleados,id',
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
