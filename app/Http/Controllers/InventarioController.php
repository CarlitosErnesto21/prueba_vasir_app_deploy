<?php

namespace App\Http\Controllers;

use App\Models\Inventario;
use Illuminate\Http\Request;

class InventarioController extends Controller
{
    public function index()
    {
        $inventarios = Inventario::with('producto')->get();
        return response()->json($inventarios);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'cantidad' => 'required|integer|min:0',
            'estado' => 'required|in:Disponible,Agotado',
            'producto_id' => 'required|exists:productos,id',
        ]);

        $inventario = Inventario::create($validated);

        return response()->json([
            'message' => 'Inventario creado exitosamente',
            'inventario' => $inventario,
        ]);
    }

    public function show(Inventario $inventario)
    {
        $inventario->load('producto');
        return response()->json($inventario);
    }

    public function update(Request $request, Inventario $inventario)
    {
        $validated = $request->validate([
            'cantidad' => 'required|integer|min:0',
            'estado' => 'required|in:Disponible,Agotado',
            'producto_id' => 'required|exists:productos,id',
        ]);

        $inventario->update($validated);

        return response()->json([
            'message' => 'Inventario actualizado exitosamente',
            'inventario' => $inventario,
        ]);
    }

    public function destroy(Inventario $inventario)
    {
        $inventario->delete();

        return response()->json([
            'message' => 'Inventario eliminado exitosamente',
        ]);
    }
}