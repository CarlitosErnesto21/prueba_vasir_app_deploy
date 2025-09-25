<?php


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Paquete;

use App\Models\Imagen;
use Illuminate\Support\Facades\Storage;

class PaqueteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Traer todos los paquetes con imágenes, país y provincia
        $paquetes = Paquete::with(['imagenes', 'pais', 'provincia'])->orderByDesc('created_at')->get();
        return response()->json($paquetes);
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

        $data = $request->validate([
            'nombre' => 'required|string',
            'fecha_salida' => 'required|date',
            'fecha_regreso' => 'required|date',
            'incluye' => 'nullable|string',
            'condiciones' => 'required|string',
            'recordatorio' => 'required|string',
            'precio' => 'required|numeric|min:0|max:999999.99',
            'pais_id' => 'required|exists:paises,id',
            'provincia_id' => 'required|exists:provincias,id',
            'imagenes' => 'array',
            'imagenes.*' => 'file|image',
        ]);

        $paquete = Paquete::create($data);

        if ($request->hasFile('imagenes')) {
            foreach ($request->file('imagenes') as $img) {
                $path = $img->store('paquetes', 'public');
                $paquete->imagenes()->create([
                    'nombre' => $path
                ]);
            }
        }

        return response()->json($paquete->load('imagenes'), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $paquete = Paquete::with('imagenes')->findOrFail($id);
        return response()->json($paquete);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $paquete = Paquete::findOrFail($id);
        $data = $request->validate([
            'nombre' => 'required|string',
            'fecha_salida' => 'required|date',
            'fecha_regreso' => 'required|date',
            'incluye' => 'nullable|string',
            'condiciones' => 'required|string',
            'recordatorio' => 'required|string',
            'precio' => 'required|numeric|min:0|max:999999.99',
            'pais_id' => 'required|exists:paises,id',
            'provincia_id' => 'required|exists:provincias,id',
            'imagenes' => 'array',
            'imagenes.*' => 'file|image',
            'removed_images' => 'array',
            'removed_images.*' => 'string',
        ]);

        $paquete->update($data);

        // Eliminar imágenes marcadas
        if ($request->has('removed_images')) {
            foreach ($request->removed_images as $imgName) {
                $img = $paquete->imagenes()->where('nombre', $imgName)->first();
                if ($img) {
                    Storage::disk('public')->delete($img->nombre);
                    $img->delete();
                }
            }
        }

        // Agregar nuevas imágenes
        if ($request->hasFile('imagenes')) {
            foreach ($request->file('imagenes') as $img) {
                $path = $img->store('paquetes', 'public');
                $paquete->imagenes()->create([
                    'nombre' => $path
                ]);
            }
        }

        return response()->json($paquete->load(['imagenes', 'pais', 'provincia']));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $paquete = Paquete::findOrFail($id);
        // Eliminar imágenes físicas y registros
        foreach ($paquete->imagenes as $img) {
            Storage::disk('public')->delete($img->nombre);
            $img->delete();
        }
        $paquete->delete();
        return response()->json(['message' => 'Paquete eliminado']);
    }
}
