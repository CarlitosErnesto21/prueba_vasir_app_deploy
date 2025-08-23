<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\SiteSetting;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class AutoBackupCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backup:auto';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Ejecuta respaldos automáticos según la configuración';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Verificando configuración de respaldos automáticos...');
        
        $autoBackupEnabled = SiteSetting::get('auto_backup_enabled', 'false') === 'true';
        
        if (!$autoBackupEnabled) {
            $this->info('Los respaldos automáticos están deshabilitados.');
            return 0;
        }
        
        $frequency = SiteSetting::get('backup_frequency', 'daily');
        $lastBackupTime = SiteSetting::get('last_auto_backup_time', null);
        
        $shouldRunBackup = false;
        $now = Carbon::now();
        
        if (!$lastBackupTime) {
            $shouldRunBackup = true;
            $this->info('No hay registro de respaldo anterior. Ejecutando primer respaldo...');
        } else {
            $lastBackup = Carbon::parse($lastBackupTime);
            
            switch ($frequency) {
                case 'every_2_minutes':
                    $minutesDiff = abs($now->diffInMinutes($lastBackup));
                    $shouldRunBackup = $minutesDiff >= 2;
                    $this->info("Última ejecución: {$lastBackup->diffForHumans()}");
                    break;
                case 'hourly':
                    $shouldRunBackup = abs($now->diffInHours($lastBackup)) >= 1;
                    break;
                case 'daily':
                    $shouldRunBackup = abs($now->diffInDays($lastBackup)) >= 1;
                    break;
                case 'weekly':
                    $shouldRunBackup = abs($now->diffInDays($lastBackup)) >= 7;
                    break;
                case 'monthly':
                    $shouldRunBackup = abs($now->diffInDays($lastBackup)) >= 30;
                    break;
            }
        }
        
        if ($shouldRunBackup) {
            try {
                $this->info("Ejecutando backup automático con frecuencia: {$frequency}");
                Log::info('Ejecutando backup automático - Frecuencia: ' . $frequency);
                
                // Ejecutar el backup
                $exitCode = Artisan::call('backup:run', [
                    '--only-db' => true,
                    '--disable-notifications' => true
                ]);
                
                if ($exitCode === 0) {
                    // Actualizar el timestamp del último backup
                    SiteSetting::set('last_auto_backup_time', $now->toDateTimeString());
                    
                    $this->info('✅ Backup automático completado exitosamente');
                    Log::info('Backup automático completado exitosamente');
                } else {
                    $this->error('❌ Error al ejecutar el backup');
                    Log::error('Error al ejecutar backup automático - Exit code: ' . $exitCode);
                }
                
            } catch (\Exception $e) {
                $this->error('❌ Error en backup automático: ' . $e->getMessage());
                Log::error('Error en backup automático: ' . $e->getMessage());
            }
        } else {
            $this->info('⏰ No es necesario ejecutar backup en este momento.');
        }
        
        return 0;
    }
}
