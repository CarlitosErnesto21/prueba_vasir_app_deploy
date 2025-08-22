<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SiteSetting;

class SiteSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            [
                'key' => 'company_mission',
                'value' => '', // Campo vacío para que el admin lo configure
                'type' => 'textarea',
                'label' => 'Misión de la Empresa',
                'description' => 'Misión corporativa que aparece en la página Sobre Nosotros'
            ],
            [
                'key' => 'company_vision',
                'value' => '', // Campo vacío para que el admin lo configure
                'type' => 'textarea',
                'label' => 'Visión de la Empresa',
                'description' => 'Visión corporativa que aparece en la página Sobre Nosotros'
            ],
            [
                'key' => 'company_description',
                'value' => '', // Campo vacío para que el admin lo configure
                'type' => 'textarea',
                'label' => 'Descripción de la Empresa',
                'description' => 'Descripción principal que aparece en el encabezado de la página Sobre Nosotros'
            ],
            [
                'key' => 'auto_backup_enabled',
                'value' => 'false',
                'type' => 'boolean',
                'label' => 'Habilitar Respaldos Automáticos',
                'description' => 'Activar o desactivar los respaldos automáticos de la base de datos'
            ],
            [
                'key' => 'backup_frequency',
                'value' => 'daily',
                'type' => 'select',
                'label' => 'Frecuencia de Respaldo',
                'description' => 'Con qué frecuencia se realizarán los respaldos automáticos'
            ],
            [
                'key' => 'backup_retention_days',
                'value' => '7',
                'type' => 'number',
                'label' => 'Días de Retención',
                'description' => 'Cuántos días mantener los respaldos antes de eliminarlos'
            ]
        ];

        foreach ($settings as $setting) {
            SiteSetting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}
