<?php

namespace App\Http\Controllers;

use App\Models\MetodoPago;
use Illuminate\Http\Request;

class MetodoPagoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Obtener todos los métodos de pago
        $metodosPago = MetodoPago::all();
        return response()->json($metodosPago);
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
            'metodo_pago' => 'required|string|max:50',
        ]);

        // Crear un nuevo método de pago
        $metodoPago = MetodoPago::create($validated);

        return response()->json([
            'message' => 'Método de pago creado exitosamente',
            'metodo_pago' => $metodoPago,
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(MetodoPago $metodoPago)
    {
        return response()->json($metodoPago);
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MetodoPago $metodoPago)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MetodoPago $metodoPago)
    {
        // Validar los datos del formulario
        $validated = $request->validate([
            'metodo_pago' => 'required|string|max:50',
        ]);

        // Actualizar el método de pago
        $metodoPago->update($validated);

        // Recargar el modelo actualizado
        $metodoPago = $metodoPago->fresh();

        return response()->json([
            'message' => 'Método de pago actualizado exitosamente',
            'metodo_pago' => $metodoPago,
        ]);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MetodoPago $metodoPago)
    {
        // Eliminar el método de pago
        $metodoPago->delete();

        return response()->json([
            'message' => 'Método de pago eliminado exitosamente',
        ]);
    }
}
