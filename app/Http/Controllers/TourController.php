<?php

namespace App\Http\Controllers;

use App\Models\Tour;
use Illuminate\Http\Request;

class TourController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Obtener todos los tours con sus relaciones
        $tours = Tour::with(['categoria', 'transporte'])->get();
        return response()->json($tours);
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
            'descripcion' => 'required|string|max:100',
            'punto_salida' => 'required|string|max:60',
            'fecha' => 'required|date',
            'precio' => 'required|numeric|min:0|max:999.99',
            'hora_regreso' => 'required|date_format:H:i:s',
            'categoria_tour_id' => 'required|exists:categorias_tours,id',
            'transporte_id' => 'required|exists:transportes,id',
        ]);

        // Crear un nuevo tour
        $tour = Tour::create($validated);

        return response()->json([
            'message' => 'Tour creado exitosamente',
            'tour' => $tour,
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Tour $tour)
    {
        // Mostrar los detalles de un tour específico con sus relaciones
        $tour->load(['categoria', 'transporte']);
        return response()->json($tour);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Tour $tour)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Tour $tour)
    {
        // Validar los datos del formulario
        $validated = $request->validate([
            'nombre' => 'required|string|max:50',
            'descripcion' => 'required|string|max:100',
            'punto_salida' => 'required|string|max:60',
            'fecha' => 'required|date',
            'precio' => 'required|numeric|min:0|max:999.99',
            'hora_regreso' => 'required|date_format:H:i:s',
            'categoria_tour_id' => 'required|exists:categorias_tours,id',
            'transporte_id' => 'required|exists:transportes,id',
        ]);

        // Actualizar el tour
        $tour->update($validated);

        return response()->json([
            'message' => 'Tour actualizado exitosamente',
            'tour' => $tour,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Tour $tour)
    {
        // Eliminar el tour
        $tour->delete();

        return response()->json([
            'message' => 'Tour eliminado exitosamente',
        ]);
    }
}
