<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear roles
        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $clienteRole = Role::firstOrCreate(['name' => 'cliente', 'guard_name' => 'web']);
        $empleadoRole = Role::firstOrCreate(['name' => 'empleado', 'guard_name' => 'web']);

        // Crear permisos
        $verReservas = Permission::firstOrCreate(['name' => 'ver reservas', 'guard_name' => 'web']);
        $crearReservas = Permission::firstOrCreate(['name' => 'crear reservas', 'guard_name' => 'web']);
        $editarReservas = Permission::firstOrCreate(['name' => 'editar reservas', 'guard_name' => 'web']);
        $eliminarReservas = Permission::firstOrCreate(['name' => 'eliminar reservas', 'guard_name' => 'web']);

        // Asignar permisos a los roles
        $adminRole->syncPermissions([$verReservas, $crearReservas, $editarReservas, $eliminarReservas]);
        $empleadoRole->syncPermissions([$verReservas, $crearReservas, $editarReservas]);
        $clienteRole->syncPermissions([$verReservas, $crearReservas]);
    }
}
