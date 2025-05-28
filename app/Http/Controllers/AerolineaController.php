<?php

namespace App\Http\Controllers;

use App\Models\Aerolinea;
use Illuminate\Http\Request;

class AerolineaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Obtener todas las aerolíneas
        $aerolineas = Aerolinea::all();
        return response()->json($aerolineas);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Retornar una vista para crear una nueva aerolínea
        return view('aerolineas.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validar los datos del formulario
        $validated = $request->validate([
            'nombre' => 'required|string|max:50',
            'fecha' => 'required|date',
        ]);

        // Crear una nueva aerolínea
        $aerolinea = Aerolinea::create($validated);

        return response()->json([
            'message' => 'Aerolínea creada exitosamente',
            'aerolinea' => $aerolinea,
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Aerolinea $aerolinea)
    {
        // Mostrar los detalles de una aerolínea específica
        return response()->json($aerolinea);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Aerolinea $aerolinea)
    {
        // Retornar una vista para editar una aerolínea
        return view('aerolineas.edit', compact('aerolinea'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Aerolinea $aerolinea)
    {
        // Validar los datos del formulario
        $validated = $request->validate([
            'nombre' => 'required|string|max:50',
            'fecha' => 'required|date',
        ]);

        // Actualizar la aerolínea
        $aerolinea->update($validated);

        return response()->json([
            'message' => 'Aerolínea actualizada exitosamente',
            'aerolinea' => $aerolinea,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Aerolinea $aerolinea)
    {
        // Eliminar la aerolínea
        $aerolinea->delete();

        return response()->json([
            'message' => 'Aerolínea eliminada exitosamente',
        ]);
    }
}
