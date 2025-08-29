<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\SiteSetting;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Inertia\Response;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): Response
    {
        return Inertia::render('Auth/Login');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:'.User::class,
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        //Implementando lógica de admin automático
        $this->assignUserRole($user);
        event(new Registered($user));
        Auth::login($user);

        // Enviar notificación de verificación de correo electrónico
        $user->sendEmailVerificationNotification();

        //Redireccionando según el rol asignado
        return $this->redirectBasedOnUserRole($user);
    }

    /**
    * Asignar rol al usuario (primer usuario = admin, resto = cliente)
    */
    private function assignUserRole(User $user): void
    {
        // Verificar si es el primer usuario
        $firstAdminCreated = SiteSetting::where('key', 'first_admin_created')->value('value') === 'true';
        $autoAdminEnabled = SiteSetting::where('key', 'auto_admin_registration')->value('value') === 'true';

        if (!$firstAdminCreated && $autoAdminEnabled) {
            // Primer usuario = Admin automático
            $user->assignRole('admin');
                
            // Marcar que ya se creó el primer admin
            SiteSetting::where('key', 'first_admin_created')->update(['value' => 'true']);
            SiteSetting::where('key', 'auto_admin_registration')->update(['value' => 'false']);
        } else {
            // Usuarios posteriores = Cliente
            $user->assignRole('cliente');
        }
    }

    /**
    * Redirigir según el rol del usuario
    */
    private function redirectBasedOnUserRole(User $user): RedirectResponse
    {
        if ($user->hasRole('admin') || $user->hasRole('empleado')) {
            return redirect()->route('dashboard');
        }
        
        return redirect()->route('inicio');
    }
}
