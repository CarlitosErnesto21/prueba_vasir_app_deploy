<?php

namespace App\Http\Controllers;

use App\Models\TipoTransporte;
use Illuminate\Http\Request;

class TipoTransporteController extends Controller
{
    /**
     * Muestra todos los tipos de transporte.
     */
    public function index()
    {
        // Devuelve todas los tipos de transporte
        return response()->json(TipoTransporte::all());
    }

    /**
     * Almacena un nuevo tipo de transporte.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:100',
        ]);

        $tipo = TipoTransporte::create($validated);

        return response()->json([
            'message' => 'Tipo de transporte creado exitosamente',
            'tipo_transporte' => $tipo,
        ]);
    }

    /**
     * Muestra un tipo de transporte específico.
     */
    public function show($id)
    {
        $tipo = TipoTransporte::findOrFail($id);
        return response()->json($tipo);
    }

    /**
     * Actualiza un tipo de transporte existente.
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:100',
        ]);

        $tipo = TipoTransporte::findOrFail($id);
        $tipo->update($validated);

        return response()->json([
            'message' => 'Tipo de transporte actualizado exitosamente',
            'tipo_transporte' => $tipo,
        ]);
    }

    /**
     * Elimina un tipo de transporte.
     */
    public function destroy($id)
    {
        $tipo = TipoTransporte::findOrFail($id);
        $tipo->delete();

        return response()->json([
            'message' => 'Tipo de transporte eliminado exitosamente',
        ]);
    }
}