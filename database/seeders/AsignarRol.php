<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;

class AsignarRol extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener el primer usuario
        $user = User::orderBy('id')->first();
        if ($user) {
            // Obtener o crear el rol admin
            $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
            // Eliminar todos los roles actuales y asignar solo el rol admin
            $user->syncRoles([$adminRole]);
        }
    }
}