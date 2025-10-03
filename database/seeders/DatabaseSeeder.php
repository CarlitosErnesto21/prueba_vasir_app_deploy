<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
  
    public function run()
    {
        $this->command->info('🌱 Iniciando seeders de VASIR...');

        $this->call([
            ControlSeeder::class,          //Roles y permisos
            SiteSettingsSeeder::class,     //Configuraciones del sitio
            AdminUserSeeder::class,       //Usuario administrador
        ]);

        $this->command->info('🎊 ¡Base de datos inicializada correctamente!');
    }
}
