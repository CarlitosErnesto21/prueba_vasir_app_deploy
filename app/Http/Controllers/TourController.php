<?php

namespace App\Http\Controllers;

use App\Models\Tour;
use App\Models\Transporte;
use Illuminate\Http\Request;

class TourController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tours = Tour::with(['categoria', 'transporte', 'imagenes'])->get();
        return response()->json($tours);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:200',
            'incluye' => 'nullable|string',
            'no_incluye' => 'nullable|string',
            'cupo_min' => 'nullable|integer|min:1',
            'cupo_max' => 'nullable|integer|min:1',
            'punto_salida' => 'required|string|max:200',
            'fecha_salida' => 'required|date',
            'fecha_regreso' => 'required|date',
            'precio' => 'required|numeric|min:0|max:9999.99',
            'categoria_tour_id' => 'required|exists:categorias_tours,id',
            'transporte_id' => 'required|exists:transportes,id',
            'imagenes' => 'nullable|array',
            'imagenes.*' => 'image|max:2048',
        ]);

        // Preparar datos para crear el tour
        $tourData = $validated;
        unset($tourData['imagenes']); // Remover imagenes del array principal

        // Crear tour
        $tour = Tour::create($tourData);

        // Guardar imágenes nuevas
        if ($request->hasFile('imagenes')) {
            foreach ($request->file('imagenes') as $imagen) {
                if ($imagen instanceof \Illuminate\Http\UploadedFile && $imagen->isValid()) {
                    $nombreArchivo = uniqid() . '_' . $imagen->getClientOriginalName();
                    $destino = public_path('images/tours');
                    if (!file_exists($destino)) mkdir($destino, 0755, true);
                    $imagen->move($destino, $nombreArchivo);
                    $tour->imagenes()->create(['nombre' => $nombreArchivo]);
                }
            }
        }

        return response()->json([
            'message' => 'Tour creado exitosamente',
            'tour' => $tour->load(['imagenes', 'categoria', 'transporte']),
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Tour $tour)
    {
        $tour->load(['categoria', 'transporte', 'imagenes']);
        return response()->json($tour);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Tour $tour)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:200',
            'incluye' => 'nullable|string',
            'no_incluye' => 'nullable|string',
            'cupo_min' => 'nullable|integer|min:1',
            'cupo_max' => 'nullable|integer|min:1',
            'punto_salida' => 'required|string|max:200',
            'fecha_salida' => 'required|date',
            'fecha_regreso' => 'required|date',
            'precio' => 'required|numeric|min:0|max:9999.99',
            'categoria_tour_id' => 'required|exists:categorias_tours,id',
            'transporte_id' => 'required|exists:transportes,id',
            'imagenes' => 'nullable|array',
            'imagenes.*' => 'image|max:2048',
            'removed_images' => 'nullable|array',
        ]);

        // Preparar datos para actualizar el tour
        $tourData = $validated;
        unset($tourData['imagenes']); // Remover imagenes del array principal
        unset($tourData['removed_images']); // Remover removed_images del array principal

        // Actualizar tour
        $tour->update($tourData);

        // Guardar imágenes nuevas
        if ($request->hasFile('imagenes')) {
            foreach ($request->file('imagenes') as $imagen) {
                if ($imagen instanceof \Illuminate\Http\UploadedFile && $imagen->isValid()) {
                    $nombreArchivo = uniqid() . '_' . $imagen->getClientOriginalName();
                    $destino = public_path('images/tours');
                    if (!file_exists($destino)) mkdir($destino, 0755, true);
                    $imagen->move($destino, $nombreArchivo);
                    $tour->imagenes()->create(['nombre' => $nombreArchivo]);
                }
            }
        }

        // Eliminar imágenes seleccionadas
        if ($request->has('removed_images')) {
            foreach ($request->input('removed_images') as $imageName) {
                $imagen = $tour->imagenes()->where('nombre', $imageName)->first();
                if ($imagen) {
                    $rutaImagen = public_path('images/tours/' . $imagen->nombre);
                    if (file_exists($rutaImagen)) unlink($rutaImagen);
                    $imagen->forceDelete();
                }
            }
        }

        return response()->json([
            'message' => 'Tour actualizado exitosamente',
            'tour' => $tour->load(['imagenes', 'categoria', 'transporte']),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $tour = Tour::findOrFail($id);
        $tour->loadMissing(['imagenes', 'categoria', 'transporte']);

        // Eliminar imágenes físicas y registros
        foreach ($tour->imagenes as $imagen) {
            $rutaImagen = public_path('images/tours/' . $imagen->nombre);
            if (file_exists($rutaImagen)) unlink($rutaImagen);
            $imagen->forceDelete();
        }

        $tour->delete();

        return response()->json([
            'message' => 'Tour eliminado exitosamente',
        ]);
    }

    /**
     * Mostrar tours nacionales para la vista de clientes
     */
    public function toursNacionales()
    {
        $tours = Tour::with(['categoria', 'transporte', 'imagenes'])
            ->where('categoria_tour_id', 4) // ID de categoría Nacional
            ->where('fecha_salida', '>=', now())
            ->orderBy('fecha_salida', 'asc')
            ->get();

        return inertia('vistasClientes/ToursNacionales', [
            'tours' => $tours
        ]);
    }

    /**
     * Mostrar tours internacionales para la vista de clientes
     */
    public function toursInternacionales()
    {
        $tours = Tour::with(['categoria', 'transporte', 'imagenes'])
            ->where('categoria_tour_id', 3) // ID de categoría Internacional
            ->where('fecha_salida', '>=', now())
            ->orderBy('fecha_salida', 'asc')
            ->get();

        return inertia('vistasClientes/ToursInternacionales', [
            'tours' => $tours
        ]);
    }

    /**
     * Mostrar vista detallada de un tour nacional
     */
    public function mostrarTourNacional($id)
    {
        $tour = Tour::with(['categoria', 'transporte', 'imagenes'])
            ->where('id', $id)
            ->where('categoria_tour_id', 4) // ID de categoría Nacional
            ->firstOrFail();

        return inertia('vistasClientes/DetalleTour', [
            'tour' => $tour,
            'tipo' => 'nacional'
        ]);
    }

    /**
     * Mostrar vista detallada de un tour internacional
     */
    public function mostrarTourInternacional($id)
    {
        $tour = Tour::with(['categoria', 'transporte', 'imagenes'])
            ->where('id', $id)
            ->where('categoria_tour_id', 3) // ID de categoría Internacional
            ->firstOrFail();

        return inertia('vistasClientes/DetalleTour', [
            'tour' => $tour,
            'tipo' => 'internacional'
        ]);
    }
}
