<?php

namespace App\Http\Controllers;

use App\Models\Transporte;
use Illuminate\Http\Request;

class TransporteController extends Controller
{
    public function index()
    {
        $transportes = Transporte::all();
        return response()->json($transportes);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:50',
            'capacidad' => 'required|integer|min:1',
        ]);

        $transporte = Transporte::create($validated);

        return response()->json([
            'message' => 'Transporte creado exitosamente',
            'transporte' => $transporte,
        ]);
    }

    public function show(Transporte $transporte)
    {
        return response()->json($transporte);
    }

    public function update(Request $request, Transporte $transporte)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:50',
            'capacidad' => 'required|integer|min:1',
        ]);

        $transporte->update($validated);

        return response()->json([
            'message' => 'Transporte actualizado exitosamente',
            'transporte' => $transporte,
        ]);
    }

    public function destroy(Transporte $transporte)
    {
        $transporte->delete();

        return response()->json([
            'message' => 'Transporte eliminado exitosamente',
        ]);
    }
}