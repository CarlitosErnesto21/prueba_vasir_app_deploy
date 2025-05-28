<?php

namespace App\Http\Controllers;

use App\Models\CategoriaHotel;
use Illuminate\Http\Request;

class CategoriaHotelController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categorias = CategoriaHotel::all();
        return response()->json($categorias);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'estrella' => 'required|integer|min:1|max:5',
        ]);

        $categoria = CategoriaHotel::create($validated);

        return response()->json([
            'message' => 'Categoría de hotel creada exitosamente',
            'categoria' => $categoria,
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(CategoriaHotel $categoriaHotel)
    {
        return response()->json($categoriaHotel);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, CategoriaHotel $categoriaHotel)
    {
        $validated = $request->validate([
            'estrella' => 'required|integer|min:1|max:5',
        ]);

        $categoriaHotel->update($validated);

        return response()->json([
            'message' => 'Categoría de hotel actualizada exitosamente',
            'categoria' => $categoriaHotel,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CategoriaHotel $categoriaHotel)
    {
        $categoriaHotel->delete();

        return response()->json([
            'message' => 'Categoría de hotel eliminada exitosamente',
        ]);
    }
}
