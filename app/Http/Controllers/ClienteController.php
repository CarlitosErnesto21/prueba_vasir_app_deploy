<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\Request;

class ClienteController extends Controller
{
    // Listar todos los clientes (vista Inertia)
    public function index()
    {
        return inertia('Configuracion/Clientes');
    }
    // API para obtener clientes (JSON)
    public function getClientes()
    {
        $clientes = Cliente::with(['user', 'tipoDocumento'])->get();
        return response()->json([
            'success' => true,
            'clientes' => $clientes
        ]);
    }

    // Mostrar un cliente específico
    public function show($id)
    {
        $cliente = Cliente::with(['user', 'tipoDocumento'])->findOrFail($id);
        return response()->json($cliente);
    }

    // Crear un nuevo cliente
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:80',
            'apellido' => 'required|string|max:80',
            'numero_identificacion' => 'required|string|max:25',
            'fecha_nacimiento' => 'required|date',
            'genero' => 'required|in:MASCULINO,FEMENINO',
            'direccion' => 'required|string|max:200',
            'telefono' => 'required|string|max:20',
            'user_id' => 'required|exists:users,id',
            'tipo_documento_id' => 'required|exists:tipos_documentos,id',
        ]);

        $cliente = Cliente::create($validated);
        return response()->json($cliente, 201);
    }

    // Actualizar un cliente existente
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:80',
            'apellido' => 'required|string|max:80',
            'numero_identificacion' => 'required|string|max:25',
            'fecha_nacimiento' => 'required|date',
            'genero' => 'required|in:MASCULINO,FEMENINO',
            'direccion' => 'required|string|max:200',
            'telefono' => 'required|string|max:20',
            'user_id' => 'required|exists:users,id',
            'tipo_documento_id' => 'required|exists:tipos_documentos,id',
        ]);

        $cliente = Cliente::findOrFail($id);
        $cliente->update($validated);
        return response()->json($cliente);
    }

    // Eliminar un cliente
    public function destroy($id)
    {
        $cliente = Cliente::findOrFail($id);
        $cliente->delete();
        return response()->json(['message' => 'Cliente eliminado correctamente']);
    }
}
