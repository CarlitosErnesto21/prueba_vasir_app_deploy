<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        // Verificar si ya existe un usuario con rol de administrador
        $adminRole = Role::where('name', 'Administrador')->first();
        
        // Si el rol no existe o ya hay un usuario con ese rol, salir
        if ($adminRole && $adminRole->users()->exists()) {
            $this->command->info('⚠️  Ya existe un administrador en el sistema, saltando...');
            return;
        }

        // Obtener variables de entorno
        $adminEmail = env('ADMIN_EMAIL');
        $adminName = env('ADMIN_NAME');
        $adminPassword = env('ADMIN_PASSWORD');
        
        // Validar que las variables estén configuradas
        if (!$adminEmail || !$adminName || !$adminPassword) {
            $this->command->error('❌ Variables de admin no configuradas en .env');
            $this->command->error('Configura: ADMIN_NAME, ADMIN_EMAIL, ADMIN_PASSWORD');
            return;
        }
        
        // Crear admin inicial
        $admin = User::create([
            'name' => $adminName,
            'email' => $adminEmail,
            'password' => Hash::make($adminPassword),
            'email_verified_at' => now(),
        ]);

        $admin->assignRole('Administrador');

        $this->command->info('✅ Admin inicial creado exitosamente!');
        $this->command->info('📧 Podrá cambiar sus credenciales desde el perfil web');
    }
}