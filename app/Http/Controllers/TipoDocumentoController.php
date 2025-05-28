<?php

namespace App\Http\Controllers;

use App\Models\TipoDocumento;
use Illuminate\Http\Request;

class TipoDocumentoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Obtener todos los tipos de documentos
        $tiposDocumentos = TipoDocumento::all();
        return response()->json($tiposDocumentos);
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
            'nombre' => 'required|string|max:20',
        ]);

        // Crear un nuevo tipo de documento
        $tipoDocumento = TipoDocumento::create($validated);

        return response()->json([
            'message' => 'Tipo de documento creado exitosamente',
            'tipo_documento' => $tipoDocumento,
        ]);
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
            'nombre' => 'required|string|max:20',
        ]);

        // Actualizar el tipo de documento
        $tipoDocumento->update($validated);

        return response()->json([
            'message' => 'Tipo de documento actualizado exitosamente',
            'tipo_documento' => $tipoDocumento,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TipoDocumento $tipoDocumento)
    {
        // Eliminar el tipo de documento
        $tipoDocumento->delete();

        return response()->json([
            'message' => 'Tipo de documento eliminado exitosamente',
        ]);
    }
}
