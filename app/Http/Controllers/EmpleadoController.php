<?php

namespace App\Http\Controllers;

use App\Models\Empleado;
use Illuminate\Http\Request;

class EmpleadoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Obtener todos los empleados con sus relaciones
        $empleados = Empleado::with(['reservas', 'ventas'])->get();
        return response()->json($empleados);
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
            'nombre' => 'required|string|max:50',
            'cargo' => 'required|string|max:25',
            'telefono' => 'required|string|size:8',
            'user_id' => 'required|exists:users,id',
        ]);

        // Crear un nuevo empleado
        $empleado = Empleado::create($validated);

        return response()->json([
            'message' => 'Empleado creado exitosamente',
            'empleado' => $empleado,
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Empleado $empleado)
    {
        // Mostrar los detalles de un empleado específico con sus relaciones
        $empleado->load(['reservas', 'ventas']);
        return response()->json($empleado);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Empleado $empleado)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Empleado $empleado)
    {
        // Validar los datos del formulario
        $validated = $request->validate([
            'nombre' => 'required|string|max:50',
            'cargo' => 'required|string|max:25',
            'telefono' => 'required|string|size:8',
            'user_id' => 'required|exists:users,id',
        ]);

        // Actualizar el empleado
        $empleado->update($validated);

        return response()->json([
            'message' => 'Empleado actualizado exitosamente',
            'empleado' => $empleado,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Empleado $empleado)
    {
        // Eliminar el empleado
        $empleado->delete();

        return response()->json([
            'message' => 'Empleado eliminado exitosamente',
        ]);
    }
}
