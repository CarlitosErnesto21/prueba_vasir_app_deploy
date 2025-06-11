<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run()
    {
        // Crear roles
        $adminRole = Role::create(['name' => 'admin']);
        $clienteRole = Role::create(['name' => 'cliente']);
        $empleadoRole = Role::create(['name' => 'empleado']);

        // Crear permisos
        $verReservas = Permission::create(['name' => 'ver reservas']);
        $crearReservas = Permission::create(['name' => 'crear reservas']);
        $editarReservas = Permission::create(['name' => 'editar reservas']);
        $eliminarReservas = Permission::create(['name' => 'eliminar reservas']);

        // Asignar permisos a los roles
        $adminRole->givePermissionTo(Permission::all());
        $empleadoRole->givePermissionTo([$verReservas, $crearReservas, $editarReservas]);
        $clienteRole->givePermissionTo([$verReservas, $crearReservas]);
    }
}
