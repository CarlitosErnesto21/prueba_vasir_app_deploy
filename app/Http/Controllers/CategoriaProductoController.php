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

    public function update(Request $request, $id)
    {
        $categoriaProducto = CategoriaProducto::findOrFail($id);
        
        $validated = $request->validate([
            'nombre' => 'required|string|max:50',
        ]);

        $categoriaProducto->nombre = $validated['nombre'];
        $categoriaProducto->save();

        return response()->json([
            'message' => 'Categoría de producto actualizada exitosamente',
            'categoria' => $categoriaProducto->fresh(),
            'success' => true
        ]);
    }

    public function destroy($id)
    {
        $categoriaProducto = CategoriaProducto::findOrFail($id);
        $categoriaProducto->delete();

        return response()->json([
            'message' => 'Categoría de producto eliminada exitosamente',
            'success' => true
        ]);

        return response()->json([
            'message' => 'Categoría de producto eliminada exitosamente',
        ]);
    }
}
