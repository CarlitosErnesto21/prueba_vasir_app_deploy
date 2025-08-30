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
        $this->command->info('🚀 Iniciando creación de roles y permisos...');
        $this->command->info('📋 Creando roles del sistema...');
        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $clienteRole = Role::firstOrCreate(['name' => 'cliente', 'guard_name' => 'web']);
        $empleadoRole = Role::firstOrCreate(['name' => 'empleado', 'guard_name' => 'web']);
        $this->command->info('✅ Roles creados: admin, cliente, empleado');

        $this->command->info('🔑 Creando permisos del sistema...');
        $this->createPermissions();
        $this->command->info('✅ Permisos creados exitosamente');

        $this->command->info('🔗 Asignando permisos a roles...');
        $this->assignPermissionsToRoles($adminRole, $empleadoRole, $clienteRole);
        $this->command->info('✅ Permisos asignados correctamente');

        $this->command->info('🎉 ¡Sistema de roles y permisos configurado exitosamente!');
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
        $this->command->info('  👑 Admin: Acceso completo asignado');

        $empleadoPermissions = Permission::whereIn('name', [
            'ver reservas', 'crear reservas', 'editar reservas', 'cancelar reservas',
            'ver tours', 'crear tours', 'editar tours',
            'ver usuarios', 'crear usuarios', 'editar usuarios',
            'ver informes', 'generar informes', 'descargar informes',
            'ver perfil', 'editar perfil'
        ])->get();
        $empleadoRole->syncPermissions($empleadoPermissions);
        $this->command->info('  👔 Empleado: Permisos operativos asignados');

        $clientePermissions = Permission::whereIn('name', [
            'ver tours',
            'ver reservas', 'crear reservas', 'cancelar reservas',
            'ver perfil', 'editar perfil'
        ])->get();
        $clienteRole->syncPermissions($clientePermissions);
        $this->command->info('  🧑‍🤝‍🧑 Cliente: Permisos básicos asignados');
    }
}