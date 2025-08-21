<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BackupController extends Controller
{
    /**
     * Obtener la lista de backups disponibles
     */
    public function index()
    {
        try {
            $backupDisk = Storage::disk(config('backup.backup.destination.disks.0', 'local'));
            $backupPath = config('backup.backup.name', 'VASIR');
            
            // Verificar si el directorio existe
            if (!$backupDisk->exists($backupPath)) {
                return response()->json([
                    'success' => true,
                    'backups' => []
                ]);
            }
            
            $files = $backupDisk->files($backupPath);
            $backups = [];
            
            foreach ($files as $file) {
                if (str_ends_with($file, '.zip')) {
                    $backups[] = [
                        'id' => base64_encode($file),
                        'name' => basename($file),
                        'path' => $file,
                        'date' => Carbon::createFromTimestamp($backupDisk->lastModified($file))
                                    ->setTimezone('America/El_Salvador')
                                    ->format('d/m/Y H:i'),
                        'size' => $this->formatBytes($backupDisk->size($file))
                    ];
                }
            }
            
            // Ordenar por fecha más reciente
            usort($backups, function($a, $b) {
                return strcmp($b['date'], $a['date']);
            });
            
            return response()->json([
                'success' => true,
                'backups' => $backups
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error al obtener lista de backups: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener la lista de backups'
            ], 500);
        }
    }

    /**
     * Generar un nuevo backup
     */
    public function generate()
    {
        try {
            // Contar backups antes
            $backupDisk = Storage::disk(config('backup.backup.destination.disks.0', 'local'));
            $backupPath = config('backup.backup.name', 'VASIR');
            $filesBefore = $backupDisk->exists($backupPath) ? count($backupDisk->files($backupPath)) : 0;
            
            Log::info('Iniciando backup - Archivos antes: ' . $filesBefore);
            
            // Primero intentar el método original
            $exitCode = Artisan::call('backup:run', [
                '--only-db' => true,
                '--disable-notifications' => true
            ]);

            $output = Artisan::output();
            Log::info('Comando ejecutado - Exit code: ' . $exitCode);
            
            // Dar tiempo al sistema de archivos
            sleep(2);
            
            // Verificar que el backup se creó realmente
            $filesAfter = $backupDisk->exists($backupPath) ? count($backupDisk->files($backupPath)) : 0;
            Log::info('Archivos después del primer intento: ' . $filesAfter);
            
            if ($filesAfter > $filesBefore) {
                Log::info('Backup generado exitosamente con método original');
                return response()->json([
                    'success' => true,
                    'message' => 'Backup generado exitosamente',
                    'backups_count' => $filesAfter
                ]);
            }
            
            // Si el método original falló, usar método alternativo
            Log::info('Método original falló, intentando método alternativo...');
            
            $success = $this->createManualBackup($backupDisk, $backupPath);
            
            if ($success) {
                $filesAfterManual = $backupDisk->exists($backupPath) ? count($backupDisk->files($backupPath)) : 0;
                Log::info('Backup manual exitoso - Archivos: ' . $filesAfterManual);
                
                return response()->json([
                    'success' => true,
                    'message' => 'Backup generado exitosamente (método alternativo)',
                    'backups_count' => $filesAfterManual
                ]);
            } else {
                throw new \Exception('Ambos métodos de backup fallaron');
            }
            
        } catch (\Exception $e) {
            Log::error('Error al generar backup: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al generar el backup: ' . $e->getMessage()
            ], 500);
        }
    }
    
    private function createManualBackup($backupDisk, $backupPath)
    {
        try {
            // Crear dump manual de la base de datos
            $databaseName = config('database.connections.mysql.database');
            $timestamp = Carbon::now('America/El_Salvador')->format('Y-m-d-H-i-s');
            $filename = $timestamp . '.zip';
            
            // Crear directorio temporal
            $tempDir = storage_path('app/backup-temp-manual');
            if (!file_exists($tempDir)) {
                mkdir($tempDir, 0755, true);
            }
            
            $sqlFile = $tempDir . '/database.sql';
            
            // Obtener todas las tablas
            $tables = DB::select('SHOW TABLES');
            $tableKey = 'Tables_in_' . $databaseName;
            
            $sqlContent = "-- Database: $databaseName\n";
            $sqlContent .= "-- Generated on: " . Carbon::now('America/El_Salvador')->format('Y-m-d H:i:s T') . "\n\n";
            
            foreach ($tables as $table) {
                $tableName = $table->$tableKey;
                
                // Estructura de la tabla
                $createTable = DB::select("SHOW CREATE TABLE `$tableName`")[0];
                $sqlContent .= "\n-- Table: $tableName\n";
                $sqlContent .= "DROP TABLE IF EXISTS `$tableName`;\n";
                $sqlContent .= $createTable->{'Create Table'} . ";\n\n";
                
                // Datos de la tabla
                $rows = DB::table($tableName)->get();
                if ($rows->count() > 0) {
                    $sqlContent .= "-- Data for table: $tableName\n";
                    foreach ($rows as $row) {
                        $values = [];
                        foreach ($row as $value) {
                            $values[] = is_null($value) ? 'NULL' : "'" . addslashes($value) . "'";
                        }
                        $sqlContent .= "INSERT INTO `$tableName` VALUES (" . implode(', ', $values) . ");\n";
                    }
                    $sqlContent .= "\n";
                }
            }
            
            // Guardar archivo SQL
            file_put_contents($sqlFile, $sqlContent);
            
            // Crear ZIP
            $zipFile = $tempDir . '/' . $filename;
            $zip = new \ZipArchive();
            
            if ($zip->open($zipFile, \ZipArchive::CREATE) === TRUE) {
                $zip->addFile($sqlFile, 'database.sql');
                $zip->close();
                
                // Mover a la ubicación final
                if (!$backupDisk->exists($backupPath)) {
                    $backupDisk->makeDirectory($backupPath);
                }
                
                $finalPath = $backupPath . '/' . $filename;
                $backupDisk->put($finalPath, file_get_contents($zipFile));
                
                // Limpiar archivos temporales
                unlink($sqlFile);
                unlink($zipFile);
                rmdir($tempDir);
                
                Log::info('Backup manual creado exitosamente: ' . $filename);
                return true;
            }
            
            return false;
            
        } catch (\Exception $e) {
            Log::error('Error en backup manual: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Descargar un backup específico
     */
    public function download($id)
    {
        try {
            $filePath = base64_decode($id);
            $backupDisk = Storage::disk(config('backup.backup.destination.disks.0', 'local'));
            
            if (!$backupDisk->exists($filePath)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Archivo de backup no encontrado'
                ], 404);
            }
            
            $fileName = basename($filePath);
            $fileContent = $backupDisk->get($filePath);
            
            return Response::make($fileContent, 200, [
                'Content-Type' => 'application/zip',
                'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
                'Content-Length' => strlen($fileContent)
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error al descargar backup: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al descargar el backup'
            ], 500);
        }
    }

    /**
     * Eliminar un backup específico
     */
    public function delete($id)
    {
        try {
            $filePath = base64_decode($id);
            $backupDisk = Storage::disk(config('backup.backup.destination.disks.0', 'local'));
            
            if (!$backupDisk->exists($filePath)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Archivo de backup no encontrado'
                ], 404);
            }
            
            $backupDisk->delete($filePath);
            
            Log::info('Backup eliminado: ' . $filePath);
            
            return response()->json([
                'success' => true,
                'message' => 'Backup eliminado exitosamente'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error al eliminar backup: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el backup'
            ], 500);
        }
    }

    /**
     * Limpiar backups antiguos - mantener solo los últimos 3
     */
    public function cleanup()
    {
        try {
            $backupDisk = Storage::disk(config('backup.backup.destination.disks.0', 'local'));
            $backupPath = config('backup.backup.name', 'VASIR');
            
            if (!$backupDisk->exists($backupPath)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se encontró directorio de backups'
                ], 404);
            }

            // Obtener todos los archivos de backup
            $files = $backupDisk->files($backupPath);
            
            // Filtrar solo archivos .zip
            $zipFiles = array_filter($files, function($file) {
                return pathinfo($file, PATHINFO_EXTENSION) === 'zip';
            });

            // Ordenar por fecha de modificación (más reciente primero)
            usort($zipFiles, function($a, $b) use ($backupDisk) {
                return $backupDisk->lastModified($b) - $backupDisk->lastModified($a);
            });

            $filesDeleted = 0;
            $keepLatest = 3; // Mantener los últimos 3 backups

            // Eliminar archivos antiguos (mantener solo los últimos N)
            if (count($zipFiles) > $keepLatest) {
                $filesToDelete = array_slice($zipFiles, $keepLatest);
                
                foreach ($filesToDelete as $file) {
                    $backupDisk->delete($file);
                    $filesDeleted++;
                    Log::info('Backup eliminado por limpieza: ' . $file);
                }
            }

            // También intentar la limpieza original de spatie
            try {
                Artisan::call('backup:clean', [
                    '--disable-notifications' => true
                ]);
            } catch (\Exception $e) {
                Log::warning('Limpieza original falló: ' . $e->getMessage());
            }
            
            $message = $filesDeleted > 0 
                ? "¡Limpieza exitosa!"
                : "¡Limpieza completada!";
            
            Log::info('Limpieza completada: ' . $filesDeleted . ' archivos eliminados');
            
            return response()->json([
                'success' => true,
                'message' => $message,
                'files_deleted' => $filesDeleted,
                'files_remaining' => count($zipFiles) - $filesDeleted
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error al limpiar backups: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al limpiar los backups: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Formatear bytes a formato legible
     */
    private function formatBytes($bytes, $precision = 2)
    {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, $precision) . ' ' . $units[$i];
    }
}
