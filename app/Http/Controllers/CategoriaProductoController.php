<?php

namespace App\Http\Controllers;

use App\Models\CategoriaProducto;
use Illuminate\Http\Request;

class CategoriaProductoController extends Controller
{
    public function index()
    {
        return CategoriaProducto::all(); // Devuelve todas las categorías
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:50',
        ]);

        $categoria = CategoriaProducto::create($validated);

        return response()->json([
            'message' => 'Categoría de producto creada exitosamente',
            'categoria' => $categoria,
        ]);
    }

    public function show(CategoriaProducto $categoriaProducto)
    {
        return response()->json($categoriaProducto);
    }

    public function update(Request $request, CategoriaProducto $categoriaProducto)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:50',
        ]);

        $categoriaProducto->update($validated);

        return response()->json([
            'message' => 'Categoría de producto actualizada exitosamente',
            'categoria' => $categoriaProducto,
        ]);
    }

    public function destroy(CategoriaProducto $categoriaProducto)
    {
        $categoriaProducto->delete();

        return response()->json([
            'message' => 'Categoría de producto eliminada exitosamente',
        ]);
    }
}
