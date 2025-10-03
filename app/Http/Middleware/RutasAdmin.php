<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class RutasAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            // Si es API, devolver JSON
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'message' => 'No autenticado.'
                ], 401);
            }
            abort(403, 'No autenticado.');
        }

        $user = Auth::user();

        if ($user->hasRole('Administrador') || $user->hasRole('Empleado')) {
            return $next($request);
        }

        if ($user->hasRole('Cliente')) {
            // Si es API, devolver JSON 403
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'message' => 'Acceso denegado. Se requiere rol de Administrador o Empleado.',
                    'required_roles' => ['Administrador', 'Empleado'],
                    'user_role' => $user->roles->pluck('name')->toArray(),
                    'error_code' => 'INSUFFICIENT_PERMISSIONS'
                ], 403);
            }
            return redirect('/');
        }

        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json([
                'message' => 'No tienes permiso para acceder a esta página.',
                'error_code' => 'FORBIDDEN'
            ], 403);
        }

        abort(403, 'No tienes permiso para acceder a esta página.');
    }
}
