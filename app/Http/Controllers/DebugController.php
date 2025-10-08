<?php

namespace App\Http\Controllers;

use App\Models\CategoriaProducto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class DebugController extends Controller
{
    public function testDatabase()
    {
        try {
            $data = [
                'database_connected' => true,
                'tables' => Schema::getTableListing(),
                'categorias_productos_exists' => Schema::hasTable('categorias_productos'),
                'categorias_productos_columns' => Schema::hasTable('categorias_productos') ?
                    Schema::getColumnListing('categorias_productos') : [],
                'categorias_count' => CategoriaProducto::count(),
                'sample_categorias' => CategoriaProducto::take(3)->get(),
            ];

            return response()->json([
                'success' => true,
                'debug_info' => $data
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }

    public function testCreateCategoria(Request $request)
    {
        try {
            $testData = ['nombre' => 'Test Category ' . time()];

            // Probar validación
            $validated = $request->validate([
                'nombre' => 'required|string|min:3|max:50|unique:categorias_productos,nombre',
            ]);

            // Probar creación
            $categoria = CategoriaProducto::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Test categoria created successfully',
                'categoria' => $categoria
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'type' => 'validation_error',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'type' => 'general_error',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ], 500);
        }
    }
}
