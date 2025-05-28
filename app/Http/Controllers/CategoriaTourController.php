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

    public function update(Request $request, CategoriaTour $categoriaTour)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:50',
        ]);

        $categoriaTour->update($validated);

        return response()->json([
            'message' => 'Categoría de tour actualizada exitosamente',
            'categoria' => $categoriaTour,
        ]);
    }

    public function destroy(CategoriaTour $categoriaTour)
    {
        $categoriaTour->delete();

        return response()->json([
            'message' => 'Categoría de tour eliminada exitosamente',
        ]);
    }
}