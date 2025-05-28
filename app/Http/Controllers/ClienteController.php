<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\Request;

class ClienteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Obtener todos los clientes
        $clientes = Cliente::with(['user', 'tipoDocumento'])->get();
        return response()->json($clientes);
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
            'numero_identificacion' => 'required|string|max:25',
            'fecha_nacimiento' => 'required|date',
            'genero' => 'required|in:Masculino,Femenino',
            'direccion' => 'required|string|max:150',
            'telefono' => 'required|string|max:20',
            'user_id' => 'required|exists:users,id',
            'tipo_documento_id' => 'required|exists:tipos_documentos,id',
        ]);

        // Crear un nuevo cliente
        $cliente = Cliente::create($validated);

        return response()->json([
            'message' => 'Cliente creado exitosamente',
            'cliente' => $cliente,
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Cliente $cliente)
    {
        // Mostrar los detalles de un cliente específico
        $cliente->load(['user', 'tipoDocumento']);
        return response()->json($cliente);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Cliente $cliente)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Cliente $cliente)
    {
        // Validar los datos del formulario
        $validated = $request->validate([
            'numero_identificacion' => 'required|string|max:25',
            'fecha_nacimiento' => 'required|date',
            'genero' => 'required|in:Masculino,Femenino',
            'direccion' => 'required|string|max:150',
            'telefono' => 'required|string|max:20',
            'user_id' => 'required|exists:users,id',
            'tipo_documento_id' => 'required|exists:tipos_documentos,id',
        ]);

        // Actualizar el cliente
        $cliente->update($validated);

        return response()->json([
            'message' => 'Cliente actualizado exitosamente',
            'cliente' => $cliente,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Cliente $cliente)
    {
        // Eliminar el cliente
        $cliente->delete();

        return response()->json([
            'message' => 'Cliente eliminado exitosamente',
        ]);
    }
}
