<?php

namespace App\Http\Controllers;

use App\Models\CategoriaTour;
use Illuminate\Http\Request;

class CategoriaTourController extends Controller
{
    public function index()
    {
        $categorias = CategoriaTour::all();
        return response()->json($categorias);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:50',
        ]);

        $categoria = CategoriaTour::create($validated);

        return response()->json([
            'message' => 'Categoría de tour creada exitosamente',
            'categoria' => $categoria,
        ]);
    }

    public function show(CategoriaTour $categoriaTour)
    {
        return response()->json($categoriaTour);
    }

    public function update(Request $request, $id)
    {
        $categoriaTour = CategoriaTour::findOrFail($id);
        
        $validated = $request->validate([
            'nombre' => 'required|string|max:50',
        ]);

        $categoriaTour->nombre = $validated['nombre'];
        $categoriaTour->save();

        return response()->json([
            'message' => 'Categoría de tour actualizada exitosamente',
            'categoria' => $categoriaTour,
        ]);
    }

    public function destroy($id)
    {
        $categoriaTour = CategoriaTour::findOrFail($id);
        $categoriaTour->delete();

        return response()->json([
            'message' => 'Categoría de tour eliminada exitosamente',
            'success' => true
        ]);
    }
}