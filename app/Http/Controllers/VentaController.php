<?php

namespace App\Http\Controllers;

use App\Models\Venta;
use Illuminate\Http\Request;

class VentaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Obtener todas las ventas con sus relaciones
        $ventas = Venta::with(['reserva', 'cliente', 'empleado', 'metodoPago', 'detalleVenta'])->get();
        return response()->json($ventas);
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
            'total' => 'required|numeric|min:0',
            'reserva_id' => 'required|exists:reservas,id',
            'cliente_id' => 'required|exists:clientes,id',
            'empleado_id' => 'required|exists:empleados,id',
            'metodo_pago_id' => 'required|exists:metodos_pagos,id',
        ]);

        // Crear una nueva venta
        $venta = Venta::create($validated);

        return response()->json([
            'message' => 'Venta creada exitosamente',
            'venta' => $venta,
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Venta $venta)
    {
        // Mostrar los detalles de una venta específica con sus relaciones
        $venta->load(['reserva', 'cliente', 'empleado', 'metodoPago', 'detalleVenta']);
        return response()->json($venta);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Venta $venta)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Venta $venta)
    {
        // Validar los datos del formulario
        $validated = $request->validate([
            'fecha' => 'required|date',
            'total' => 'required|numeric|min:0',
            'reserva_id' => 'required|exists:reservas,id',
            'cliente_id' => 'required|exists:clientes,id',
            'empleado_id' => 'required|exists:empleados,id',
            'metodo_pago_id' => 'required|exists:metodos_pagos,id',
        ]);

        // Actualizar la venta
        $venta->update($validated);

        return response()->json([
            'message' => 'Venta actualizada exitosamente',
            'venta' => $venta,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Venta $venta)
    {
        // Eliminar la venta
        $venta->delete();

        return response()->json([
            'message' => 'Venta eliminada exitosamente',
        ]);
    }
}
