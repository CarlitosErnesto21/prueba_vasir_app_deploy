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
            // Debug paso a paso
            $debugInfo = [];

            // Paso 1: Verificar request
            $debugInfo['step1_request'] = [
                'all_data' => $request->all(),
                'method' => $request->method()
            ];

            // Paso 2: Probar inserción directa en BD
            $nombre = 'Test_' . time();
            $result = DB::table('categorias_productos')->insert([
                'nombre' => $nombre,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            $debugInfo['step2_direct_insert'] = [
                'result' => $result,
                'nombre_used' => $nombre
            ];

            // Paso 3: Verificar inserción
            $count = DB::table('categorias_productos')->where('nombre', $nombre)->count();
            $debugInfo['step3_verification'] = [
                'count_after_insert' => $count
            ];

            return response()->json([
                'success' => true,
                'message' => 'Direct database insert test completed',
                'debug' => $debugInfo
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'type' => 'exception',
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => explode("\n", $e->getTraceAsString())
            ], 500);
        }
    }
}
