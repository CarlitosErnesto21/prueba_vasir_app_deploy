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
            abort(403, 'No autenticado.');
        }

        $user = Auth::user();

        // Si el usuario tiene rol admin, permite el acceso
        if ($user->hasRole('admin')) {
            return $next($request);
        }

        // Si el usuario tiene rol cliente, redirige a la página de inicio
        if ($user->hasRole('cliente')) {
            return redirect('/');   
        }

        // Para otros casos, aborta con error 403
        abort(403, 'No tienes permiso para acceder a esta página.');
    }
}
