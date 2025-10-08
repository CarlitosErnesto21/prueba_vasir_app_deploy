<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ServerDebugController extends Controller
{
    public function environmentInfo()
    {
        try {
            return response()->json([
                'success' => true,
                'environment' => [
                    'app_env' => config('app.env'),
                    'app_debug' => config('app.debug'),
                    'app_key_set' => !empty(config('app.key')),
                    'database_connection' => config('database.default'),
                    'php_version' => PHP_VERSION,
                    'laravel_version' => app()->version(),
                    'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
                    'request_method_allowed' => ['GET', 'POST', 'PUT', 'DELETE'],
                    'session_driver' => config('session.driver'),
                    'cache_driver' => config('cache.default'),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }

    public function forceDebugPost(Request $request)
    {
        // Forzar mostrar errores detallados
        config(['app.debug' => true]);

        try {
            Log::info('Force debug POST attempt', [
                'input' => $request->all(),
                'headers' => $request->headers->all()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Force debug POST successful',
                'input' => $request->all(),
                'debug_enabled' => config('app.debug')
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTrace()
            ], 500);
        }
    }

    public function basicPost(Request $request)
    {
        // Log todo para debugging
        Log::info('POST request received', [
            'method' => $request->method(),
            'url' => $request->fullUrl(),
            'headers' => $request->headers->all(),
            'input' => $request->all()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Basic POST successful',
            'received_data' => $request->all(),
            'timestamp' => now()
        ]);
    }
}
