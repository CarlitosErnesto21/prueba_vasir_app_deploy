<?php

namespace App\Http\Controllers;

use App\Models\TipoDocumento;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TipoDocumentoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Si es una petición AJAX, devolver JSON
        if ($request->expectsJson()) {
            $tiposDocumentos = TipoDocumento::orderBy('nombre')->get();
            return response()->json([
                'success' => true,
                'tipos' => $tiposDocumentos
            ]);
        }

        // Si es una petición normal, devolver la vista Inertia
        $tiposDocumentos = TipoDocumento::orderBy('nombre')->get();
        return Inertia::render('Configuracion/TiposDocumento', [
            'tiposDocumentos' => $tiposDocumentos
        ]);
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
            'nombre' => 'required|string|max:20|unique:tipos_documentos,nombre',
        ]);

        try {
            // Crear un nuevo tipo de documento
            $tipoDocumento = TipoDocumento::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Tipo de documento creado exitosamente',
                'tipo_documento' => $tipoDocumento,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear el tipo de documento: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(TipoDocumento $tipoDocumento)
    {
        // Mostrar los detalles de un tipo de documento específico
        return response()->json($tipoDocumento);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TipoDocumento $tipoDocumento)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TipoDocumento $tipoDocumento)
    {
        // Validar los datos del formulario
        $validated = $request->validate([
            'nombre' => 'required|string|max:20|unique:tipos_documentos,nombre,' . $tipoDocumento->id,
        ]);

        try {
            // Actualizar el tipo de documento
            $tipoDocumento->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Tipo de documento actualizado exitosamente',
                'tipo_documento' => $tipoDocumento,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el tipo de documento: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TipoDocumento $tipoDocumento)
    {
        try {
            // Verificar si hay clientes usando este tipo de documento
            if ($tipoDocumento->clientes()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se puede eliminar este tipo de documento porque está siendo usado por clientes.'
                ], 400);
            }

            // Eliminar el tipo de documento
            $tipoDocumento->delete();

            return response()->json([
                'success' => true,
                'message' => 'Tipo de documento eliminado exitosamente',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el tipo de documento: ' . $e->getMessage()
            ], 500);
        }
    }
}
