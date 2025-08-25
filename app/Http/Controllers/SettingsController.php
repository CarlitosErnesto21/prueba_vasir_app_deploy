<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SiteSetting;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Inertia\Inertia;

class SettingsController extends Controller
{
    public function index()
    {
        $mission = SiteSetting::get('company_mission');
        $vision = SiteSetting::get('company_vision');
        $description = SiteSetting::get('company_description');
        
        // Log para debug
        Log::info('Settings loaded:', [
            'mission' => $mission,
            'vision' => $vision,
            'description' => $description
        ]);
        
        return Inertia::render('Configuracion/Settings', [
            'siteSettings' => [
                'mission' => $mission,
                'vision' => $vision,
                'description' => $description,
            ],
            'databaseInfo' => $this->getDatabaseInfo()
        ]);
    }

    /**
     * Obtener información dinámica de la base de datos
     */
    public function getDatabaseInfo()
    {
        try {
            // Obtener el último backup
            $lastBackup = $this->getLastBackupInfo();
            
            // Obtener el tamaño de la base de datos
            $dbSize = $this->getDatabaseSize();
            
            // Determinar el estado de la base de datos
            $status = $this->getDatabaseStatus();
            
            return [
                'last_backup_date' => $lastBackup['date'],
                'last_backup_formatted' => $lastBackup['formatted'],
                'database_size' => $dbSize,
                'status' => $status,
                'status_text' => $this->getStatusText($status)
            ];
            
        } catch (\Exception $e) {
            return [
                'last_backup_date' => null,
                'last_backup_formatted' => 'No disponible',
                'database_size' => 'No disponible',
                'status' => 'unknown',
                'status_text' => 'Desconocido'
            ];
        }
    }

    /**
     * Obtener información del último backup
     */
    private function getLastBackupInfo()
    {
        try {
            $backupDisk = Storage::disk(config('backup.backup.destination.disks.0', 'local'));
            $backupPath = config('backup.backup.name', 'VASIR');
            
            if (!$backupDisk->exists($backupPath)) {
                return [
                    'date' => null,
                    'formatted' => 'Sin respaldos'
                ];
            }
            
            $files = $backupDisk->files($backupPath);
            $latestFile = null;
            $latestTime = 0;
            
            foreach ($files as $file) {
                if (str_ends_with($file, '.zip')) {
                    $lastModified = $backupDisk->lastModified($file);
                    if ($lastModified > $latestTime) {
                        $latestTime = $lastModified;
                        $latestFile = $file;
                    }
                }
            }
            
            if ($latestFile) {
                $date = Carbon::createFromTimestamp($latestTime)
                            ->setTimezone('America/El_Salvador');
                return [
                    'date' => $date,
                    'formatted' => $date->format('d \d\e F, Y - H:i')
                ];
            }
            
            return [
                'date' => null,
                'formatted' => 'Sin respaldos'
            ];
            
        } catch (\Exception $e) {
            return [
                'date' => null,
                'formatted' => 'Error al verificar'
            ];
        }
    }

    /**
     * Obtener el tamaño de la base de datos
     */
    private function getDatabaseSize()
    {
        try {
            $databaseName = config('database.connections.mysql.database');
            
            $result = DB::select("
                SELECT 
                    ROUND(SUM(data_length + index_length) / 1024 / 1024, 2) AS size_mb
                FROM information_schema.tables 
                WHERE table_schema = ?
            ", [$databaseName]);
            
            $sizeMB = $result[0]->size_mb ?? 0;
            
            if ($sizeMB >= 1024) {
                return round($sizeMB / 1024, 2) . ' GB';
            } else {
                return $sizeMB . ' MB';
            }
            
        } catch (\Exception $e) {
            return 'No disponible';
        }
    }

    /**
     * Determinar el estado de la base de datos
     */
    private function getDatabaseStatus()
    {
        try {
            // Intentar hacer una consulta simple para verificar conectividad
            DB::select('SELECT 1');
            
            // Verificar si hay conexiones activas o problemas
            $processlist = DB::select('SHOW PROCESSLIST');
            $activeConnections = count($processlist);
            
            // Verificar el estado de las tablas principales
            $tableCount = DB::select("
                SELECT COUNT(*) as count 
                FROM information_schema.tables 
                WHERE table_schema = ?
            ", [config('database.connections.mysql.database')]);
            
            if ($tableCount[0]->count > 0 && $activeConnections < 100) {
                return 'operational';
            } elseif ($activeConnections >= 100) {
                return 'high_load';
            } else {
                return 'warning';
            }
            
        } catch (\Exception $e) {
            return 'error';
        }
    }

    /**
     * Obtener texto descriptivo del estado
     */
    private function getStatusText($status)
    {
        return match($status) {
            'operational' => 'Operacional',
            'high_load' => 'Carga Alta',
            'warning' => 'Advertencia',
            'error' => 'Error',
            default => 'Desconocido'
        };
    }

    public function update(Request $request)
    {
        $request->validate([
            'mission' => 'required|string|max:1000',
            'vision' => 'required|string|max:1000',
            'description' => 'required|string|max:1000',
        ]);

        // Log para debug
        Log::info('Updating settings:', [
            'mission' => $request->mission,
            'vision' => $request->vision,
            'description' => $request->description
        ]);

        try {
            // Actualizar o crear la misión
            $missionSetting = SiteSetting::updateOrCreate(
                ['key' => 'company_mission'],
                [
                    'value' => $request->mission,
                    'type' => 'textarea',
                    'label' => 'Misión de la Empresa',
                    'description' => 'Misión corporativa que aparece en la página Sobre Nosotros',
                    'updated_by' => Auth::id()
                ]
            );

            // Actualizar o crear la visión
            $visionSetting = SiteSetting::updateOrCreate(
                ['key' => 'company_vision'],
                [
                    'value' => $request->vision,
                    'type' => 'textarea',
                    'label' => 'Visión de la Empresa',
                    'description' => 'Visión corporativa que aparece en la página Sobre Nosotros',
                    'updated_by' => Auth::id()
                ]
            );

            // Actualizar o crear la descripción
            $descriptionSetting = SiteSetting::updateOrCreate(
                ['key' => 'company_description'],
                [
                    'value' => $request->description,
                    'type' => 'textarea',
                    'label' => 'Descripción de la Empresa',
                    'description' => 'Descripción principal que aparece en el encabezado de la página Sobre Nosotros',
                    'updated_by' => Auth::id()
                ]
            );

            // Log para confirmar la actualización
            Log::info('Settings updated successfully:', [
                'mission_id' => $missionSetting->id,
                'vision_id' => $visionSetting->id,
                'description_id' => $descriptionSetting->id,
                'mission_value' => $missionSetting->value,
                'vision_value' => $visionSetting->value,
                'description_value' => $descriptionSetting->value
            ]);

            // Limpiar cache si está habilitado
            if (config('cache.default') !== 'array') {
                cache()->forget('site_settings_company_mission');
                cache()->forget('site_settings_company_vision');
                cache()->forget('site_settings_company_description');
            }

            return back()->with('success', 'Configuración de la empresa actualizada correctamente');
            
        } catch (\Exception $e) {
            Log::error('Error updating settings:', [
                'error' => $e->getMessage(),
                'mission' => $request->mission,
                'vision' => $request->vision,
                'description' => $request->description
            ]);
            
            return back()->with('error', 'Error al actualizar la configuración: ' . $e->getMessage());
        }
    }
}
