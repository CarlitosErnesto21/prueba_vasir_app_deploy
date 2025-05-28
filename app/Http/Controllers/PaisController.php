<?php

namespace App\Http\Controllers;

use App\Models\Pais;
use Illuminate\Http\Request;

class PaisController extends Controller
{
    public function index()
    {
        $paises = Pais::all();
        return response()->json($paises);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:50',
        ]);

        $pais = Pais::create($validated);

        return response()->json([
            'message' => 'País creado exitosamente',
            'pais' => $pais,
        ]);
    }

    public function show(Pais $pais)
    {
        return response()->json($pais);
    }

    public function update(Request $request, Pais $pais)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:50',
        ]);

        $pais->update($validated);

        return response()->json([
            'message' => 'País actualizado exitosamente',
            'pais' => $pais,
        ]);
    }

    public function destroy(Pais $pais)
    {
        $pais->delete();

        return response()->json([
            'message' => 'País eliminado exitosamente',
        ]);
    }
}