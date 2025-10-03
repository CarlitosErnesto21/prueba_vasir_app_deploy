<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class ControlSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminRole = Role::firstOrCreate(['name' => 'Administrador', 'guard_name' => 'web']);
        $empleadoRole = Role::firstOrCreate(['name' => 'Empleado', 'guard_name' => 'web']);
        $clienteRole = Role::firstOrCreate(['name' => 'Cliente', 'guard_name' => 'web']);

        $this->createPermissions();
        $this->assignPermissionsToRoles($adminRole, $empleadoRole, $clienteRole);
    }

        private function createPermissions(): void
    {
        $permissions = [
            // Reservas
            'ver reservas',
            'crear reservas',
            'editar reservas',
            'eliminar reservas',
            'cancelar reservas',
            
            // Tours
            'ver tours',
            'crear tours',
            'editar tours',
            'eliminar tours',
            
            // Clientes/Usuarios
            'ver usuarios',
            'crear usuarios',
            'editar usuarios',
            'eliminar usuarios',
            'gestionar usuarios',
            'asignar roles',
            
            // Informes
            'ver informes',
            'generar informes',
            'descargar informes',
            
            // Administración
            'ver dashboard admin',
            'gestionar backups',
            'gestionar configuraciones',
            'gestionar tipos documento',
            
            // Perfil
            'ver perfil',
            'editar perfil',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web'
            ]);
        }
    }

    private function assignPermissionsToRoles($adminRole, $empleadoRole, $clienteRole): void {
        $adminRole->syncPermissions(Permission::all());

        $empleadoPermissions = Permission::whereIn('name', [
            'ver reservas', 'crear reservas', 'editar reservas', 'cancelar reservas',
            'ver tours', 'crear tours', 'editar tours',
            'ver usuarios', 'crear usuarios', 'editar usuarios',
            'ver informes', 'generar informes', 'descargar informes',
            'ver perfil', 'editar perfil'
        ])->get();
        $empleadoRole->syncPermissions($empleadoPermissions);

        $clientePermissions = Permission::whereIn('name', [
            'ver tours',
            'ver reservas', 'crear reservas', 'cancelar reservas',
            'ver perfil', 'editar perfil'
        ])->get();
        $clienteRole->syncPermissions($clientePermissions);
    }
}