<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SiteSetting;

class SiteSettingsSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('⚙️ Creando configuraciones del sitio...');
        
        $settings = [
            [
                'key' => 'mision',
                'value' => '',
                'type' => 'textarea',
                'label' => 'Misión',
                'description' => 'Misión corporativa que aparece en la página Sobre Nosotros'
            ],
            [
                'key' => 'vision',
                'value' => '',
                'type' => 'textarea',
                'label' => 'Visión',
                'description' => 'Visión corporativa que aparece en la página Sobre Nosotros'
            ],
            [
                'key' => 'descripcion_principal',
                'value' => '',
                'type' => 'textarea',
                'label' => 'Descripción Principal',
                'description' => 'Descripción principal que aparece en el encabezado de la página Sobre Nosotros'
            ],
            [
                'key' => 'respaldos_automaticos',
                'value' => 'false',
                'type' => 'boolean',
                'label' => 'Respaldos Automáticos',
                'description' => 'Activar o desactivar los respaldos automáticos de la base de datos'
            ],
            
            // ✅ CONFIGURACIONES PARA ADMIN AUTOMÁTICO
            [
                'key' => 'first_admin_created',
                'value' => 'false',
                'type' => 'boolean',
                'label' => 'Primer Administrador Creado',
                'description' => 'Indica si ya se ha creado el primer administrador del sistema'
            ],
            [
                'key' => 'auto_admin_registration',
                'value' => 'true',
                'type' => 'boolean',
                'label' => 'Registro Automático de Admin',
                'description' => 'Permite que el primer usuario registrado sea automáticamente administrador'
            ]
        ];

        foreach ($settings as $setting) {
            SiteSetting::firstOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
        
        $this->command->info('✅ Configuraciones del sitio creadas correctamente');
        $this->command->info('🔧 Admin automático configurado para el primer usuario');
    }
}