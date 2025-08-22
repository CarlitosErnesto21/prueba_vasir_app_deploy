<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;
use Illuminate\Support\Facades\Auth;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return array_merge(parent::share($request), [
            // Compartir información de autenticación con todas las vistas de Inertia
            'auth' => [
                // 'user' será una función que retorna los datos del usuario autenticado
                'user' => fn () => (
                    $user = Auth::user() // Obtiene el usuario autenticado, si existe
                ) ? array_merge(
                    [
                        // Datos básicos del usuario
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                    ],
                    [
                        // Obtiene el primer rol del usuario (puede tener varios)
                        'role' => $user->roles->pluck('name')->first()
                    ]
                ) : null, // Si no hay usuario autenticado, retorna null
            ],
            
            // Compartir mensajes flash con todas las vistas de Inertia
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error' => fn () => $request->session()->get('error'),
                'info' => fn () => $request->session()->get('info'),
                'warning' => fn () => $request->session()->get('warning'),
            ],
        ]);
    }
}
