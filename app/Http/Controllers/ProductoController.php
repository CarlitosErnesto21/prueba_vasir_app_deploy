<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use Illuminate\Http\Request;

class ProductoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Producto::with(['inventario', 'categoria'])->get(); // Carga las relaciones
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
        $validated = $request->validate([
            'nombre' => 'required|string|max:80',
            'descripcion' => 'required|string|max:255',
            'precio' => 'required|numeric|min:0',
            'inventario_id' => 'required|exists:inventarios,id',
            'categoria_id' => 'required|exists:categorias_productos,id'
        ]);

        $producto = Producto::create($validated);
        return response()->json($producto, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Producto $producto)
    {
        $producto->load('categoria');
        return response()->json($producto);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Producto $producto)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Producto $producto)
    {
        $validated = $request->validate([
            'nombre' => 'sometimes|required|string|max:80',
            'descripcion' => 'sometimes|required|string|max:255',
            'precio' => 'sometimes|required|numeric|min:0',
            'categoria_id' => 'sometimes|required|exists:categorias_productos,id',
            'inventario_id' => 'sometimes|required|exists:inventarios,id'
        ]);

        $producto->update($validated);
        return response()->json($producto);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Producto $producto)
    {
        $producto->delete();
        return response()->noContent();
    }
}
