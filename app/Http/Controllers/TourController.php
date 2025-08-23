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
        $tours = Tour::with(['categoria', 'transporte.tipoTransporte', 'imagenes'])->get();
        return response()->json($tours);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:50',
            'descripcion' => 'required|string|max:100',
            'punto_salida' => 'required|string|max:60',
            'fecha' => 'required|date',
            'precio' => 'required|numeric|min:0|max:9999.99',
            'hora' => 'required',
            'ampm' => 'required|in:am,pm',
            'categoria_tour_id' => 'required|exists:categorias_tours,id',
            'tipo_transporte_id' => 'required|exists:tipos_transportes,id',
            'imagenes' => 'nullable|array',
            'imagenes.*' => 'image|max:2048',
        ]);

        // Combinar hora y am/pm
        $validated['hora_regreso'] = $validated['hora'] . ' ' . $validated['ampm'];

        // Convertir a formato 24h H:i:s
        $date = \DateTime::createFromFormat('h:i a', $validated['hora_regreso']);
        if (!$date) {
            return response()->json(['message' => 'Formato de hora de regreso inválido.'], 422);
        }
        $validated['hora_regreso'] = $date->format('H:i:s');

        // Asignar transporte disponible
        $transporte = Transporte::where('tipo_transporte_id', $validated['tipo_transporte_id'])->first();
        if (!$transporte) {
            return response()->json(['message' => 'No hay transporte disponible para el tipo seleccionado.'], 422);
        }
        $validated['transporte_id'] = $transporte->id;

        // Crear tour
        $tour = Tour::create($validated);

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
            'tour' => $tour->load(['imagenes', 'categoria', 'transporte.tipoTransporte']),
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Tour $tour)
    {
        $tour->load(['categoria', 'transporte.tipoTransporte', 'imagenes']);
        return response()->json($tour);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Tour $tour)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:50',
            'descripcion' => 'required|string|max:100',
            'punto_salida' => 'required|string|max:60',
            'fecha' => 'required|date',
            'precio' => 'required|numeric|min:0|max:9999.99',
            'hora' => 'required',
            'ampm' => 'required|in:am,pm',
            'categoria_tour_id' => 'required|exists:categorias_tours,id',
            'tipo_transporte_id' => 'required|exists:tipos_transportes,id',
            'imagenes' => 'nullable|array',
            'imagenes.*' => 'image|max:2048',
            'removed_images' => 'nullable|array',
        ]);

        // Combinar hora y am/pm
        $validated['hora_regreso'] = $validated['hora'] . ' ' . $validated['ampm'];

        // Convertir a H:i:s
        $date = \DateTime::createFromFormat('h:i a', $validated['hora_regreso']);
        if (!$date) {
            return response()->json(['message' => 'Formato de hora de regreso inválido.'], 422);
        }
        $validated['hora_regreso'] = $date->format('H:i:s');

        // Asignar transporte disponible
        $transporte = Transporte::where('tipo_transporte_id', $validated['tipo_transporte_id'])->first();
        if (!$transporte) {
            return response()->json(['message' => 'No hay transporte disponible para el tipo seleccionado.'], 422);
        }
        $validated['transporte_id'] = $transporte->id;

        // Actualizar tour
        $tour->update($validated);

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
            'tour' => $tour->load(['imagenes', 'categoria', 'transporte.tipoTransporte']),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $tour = Tour::findOrFail($id);
        $tour->loadMissing(['imagenes', 'categoria', 'transporte.tipoTransporte']);

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
}
