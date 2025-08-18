<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\User;
use App\Models\TipoDocumento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class ClienteController extends Controller
{
    /**
     * Display the client management page
     */
    public function index()
    {
        // Obtener todos los clientes con información del usuario
        $clientes = Cliente::with(['user', 'tipoDocumento'])->get();
        $tiposDocumento = TipoDocumento::all();
        
        return Inertia::render('Configuracion/Clientes', [
            'clientes' => $clientes,
            'tiposDocumento' => $tiposDocumento
        ]);
    }

    /**
     * Get all clients for API
     */
    public function getClientes()
    {
        $clientes = Cliente::with(['user', 'tipoDocumento'])->get()->map(function ($cliente) {
            return [
                'id' => $cliente->id,
                'name' => $cliente->user->name,
                'email' => $cliente->user->email,
                'identification' => $cliente->numero_identificacion,
                'phone' => $cliente->telefono,
                'address' => $cliente->direccion,
                'gender' => $cliente->genero,
                'birth_date' => $cliente->fecha_nacimiento,
                'document_type' => $cliente->tipoDocumento->nombre ?? 'N/A',
                'status' => $cliente->user->email_verified_at ? 'active' : 'pending',
                'created_at' => $cliente->created_at->format('d/m/Y')
            ];
        });

        return response()->json([
            'success' => true,
            'clientes' => $clientes
        ]);
    }

    /**
     * Store a newly created client and user
     */
    public function store(Request $request)
    {
        // Validar los datos del formulario
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'identification' => 'required|string|max:25|unique:clientes,numero_identificacion',
            'birth_date' => 'required|date',
            'gender' => 'required|in:Masculino,Femenino',
            'address' => 'required|string|max:150',
            'phone' => 'required|string|max:20',
            'tipo_documento_id' => 'required|exists:tipos_documentos,id',
        ]);

        try {
            DB::beginTransaction();

            // Crear el usuario primero
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'email_verified_at' => now(), // Verificado automáticamente
            ]);

            // Asignar rol de cliente
            $user->assignRole('cliente');

            // Crear el cliente
            $cliente = Cliente::create([
                'numero_identificacion' => $validated['identification'],
                'fecha_nacimiento' => $validated['birth_date'],
                'genero' => $validated['gender'],
                'direccion' => $validated['address'],
                'telefono' => $validated['phone'],
                'user_id' => $user->id,
                'tipo_documento_id' => $validated['tipo_documento_id'],
            ]);

            DB::commit();

            // Cargar las relaciones para la respuesta
            $cliente->load(['user', 'tipoDocumento']);

            return response()->json([
                'success' => true,
                'message' => 'Cliente creado exitosamente',
                'cliente' => [
                    'id' => $cliente->id,
                    'name' => $cliente->user->name,
                    'email' => $cliente->user->email,
                    'identification' => $cliente->numero_identificacion,
                    'phone' => $cliente->telefono,
                    'address' => $cliente->direccion,
                    'gender' => $cliente->genero,
                    'birth_date' => $cliente->fecha_nacimiento,
                    'document_type' => $cliente->tipoDocumento->nombre,
                    'status' => 'active',
                    'created_at' => $cliente->created_at->format('d/m/Y')
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Error al crear el cliente: ' . $e->getMessage()
            ], 500);
        }
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
     * Update the specified client
     */
    public function update(Request $request, Cliente $cliente)
    {
        // Validar los datos del formulario
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $cliente->user_id,
            'identification' => 'required|string|max:25|unique:clientes,numero_identificacion,' . $cliente->id,
            'birth_date' => 'required|date',
            'gender' => 'required|in:Masculino,Femenino',
            'address' => 'required|string|max:150',
            'phone' => 'required|string|max:20',
            'tipo_documento_id' => 'required|exists:tipos_documentos,id',
        ]);

        try {
            DB::beginTransaction();

            // Actualizar el usuario
            $cliente->user->update([
                'name' => $validated['name'],
                'email' => $validated['email'],
            ]);

            // Actualizar el cliente
            $cliente->update([
                'numero_identificacion' => $validated['identification'],
                'fecha_nacimiento' => $validated['birth_date'],
                'genero' => $validated['gender'],
                'direccion' => $validated['address'],
                'telefono' => $validated['phone'],
                'tipo_documento_id' => $validated['tipo_documento_id'],
            ]);

            DB::commit();

            // Cargar las relaciones para la respuesta
            $cliente->load(['user', 'tipoDocumento']);

            return response()->json([
                'success' => true,
                'message' => 'Cliente actualizado exitosamente',
                'cliente' => [
                    'id' => $cliente->id,
                    'name' => $cliente->user->name,
                    'email' => $cliente->user->email,
                    'identification' => $cliente->numero_identificacion,
                    'phone' => $cliente->telefono,
                    'address' => $cliente->direccion,
                    'gender' => $cliente->genero,
                    'birth_date' => $cliente->fecha_nacimiento,
                    'document_type' => $cliente->tipoDocumento->nombre,
                    'status' => $cliente->user->email_verified_at ? 'active' : 'pending',
                    'created_at' => $cliente->created_at->format('d/m/Y')
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el cliente: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get document types for the form
     */
    public function getTiposDocumento()
    {
        $tipos = TipoDocumento::all();
        return response()->json([
            'success' => true,
            'tipos' => $tipos
        ]);
    }

    /**
     * Toggle client status (activate/deactivate)
     */
    public function toggleStatus(Cliente $cliente)
    {
        try {
            // Toggle the email_verified_at field to simulate active/inactive status
            if ($cliente->user->email_verified_at) {
                $cliente->user->update(['email_verified_at' => null]);
                $status = 'inactive';
            } else {
                $cliente->user->update(['email_verified_at' => now()]);
                $status = 'active';
            }

            return response()->json([
                'success' => true,
                'message' => 'Estado del cliente actualizado exitosamente',
                'cliente' => [
                    'id' => $cliente->id,
                    'status' => $status
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cambiar el estado del cliente: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Cliente $cliente)
    {
        try {
            DB::beginTransaction();

            // Eliminar el cliente y el usuario asociado
            $cliente->user->delete(); // Esto también eliminará el cliente por cascada
            
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Cliente eliminado exitosamente',
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el cliente: ' . $e->getMessage()
            ], 500);
        }
    }
}
