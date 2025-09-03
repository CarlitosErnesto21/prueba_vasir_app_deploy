<?php

namespace App\Http\Controllers;

use App\Models\Provincia;
use Illuminate\Http\Request;

class ProvinciaController extends Controller
{
    public function index()
    {
        $provincias = Provincia::with('pais')->get();
        return response()->json($provincias);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:50',
            'pais_id' => 'required|exists:paises,id',
        ]);

        $provincia = Provincia::create($validated);

        return response()->json([
            'message' => 'Provincia creada exitosamente',
            'provincia' => $provincia,
        ]);
    }

    public function show(Provincia $provincia)
    {
        $provincia->load('pais');
        return response()->json($provincia);
    }

    public function update(Request $request, Provincia $provincia)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:50',
            'pais_id' => 'required|exists:paises,id',
        ]);

        $provincia->update($validated);
        $provincia->refresh();
        $provincia->load('pais');

        return response()->json([
            'message' => 'Provincia actualizada exitosamente',
            'provincia' => $provincia,
        ]);
    }

    public function destroy(Provincia $provincia)
    {
        $provincia->delete();

        return response()->json([
            'message' => 'Provincia eliminada exitosamente',
        ]);
    }
}