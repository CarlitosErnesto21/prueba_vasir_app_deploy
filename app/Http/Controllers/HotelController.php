<?php

namespace App\Http\Controllers;

use App\Models\Hotel;
use Illuminate\Http\Request;

class HotelController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Obtener todos los hoteles con sus relaciones
        $hoteles = Hotel::with(['provincia', 'categoriaHotel'])->get();
        return response()->json($hoteles);
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
        // Validar los datos del formulario
        $validated = $request->validate([
            'nombre' => 'required|string|max:50',
            'direccion' => 'required|string|max:200',
            'provincia_id' => 'required|exists:provincias,id',
            'categoria_id' => 'required|exists:categorias_hoteles,id',
        ]);

        // Crear un nuevo hotel
        $hotel = Hotel::create($validated);

        return response()->json([
            'message' => 'Hotel creado exitosamente',
            'hotel' => $hotel,
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Hotel $hotel)
    {
        // Mostrar los detalles de un hotel específico con sus relaciones
        $hotel->load(['provincia', 'categoriaHotel']);
        return response()->json($hotel);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Hotel $hotel)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Hotel $hotel)
    {
        // Validar los datos del formulario
        $validated = $request->validate([
            'nombre' => 'required|string|max:50',
            'direccion' => 'required|string|max:200',
            'provincia_id' => 'required|exists:provincias,id',
            'categoria_id' => 'required|exists:categorias_hoteles,id',
        ]);

        // Actualizar el hotel
        $hotel->update($validated);

        return response()->json([
            'message' => 'Hotel actualizado exitosamente',
            'hotel' => $hotel,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Hotel $hotel)
    {
        // Eliminar el hotel
        $hotel->delete();

        return response()->json([
            'message' => 'Hotel eliminado exitosamente',
        ]);
    }
}
