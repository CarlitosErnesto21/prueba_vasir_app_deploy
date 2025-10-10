<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Tour;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class RepairTourImages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tours:repair-images';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Repara las imágenes de tours asociando nombres de archivos reales con registros de base de datos';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔧 Iniciando reparación de imágenes de tours...');

        // Obtener todos los archivos de imágenes del storage
        $files = Storage::disk('public')->files('tours');
        $imageFiles = array_filter($files, function($file) {
            return in_array(strtolower(pathinfo($file, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'webp', 'gif']);
        });

        $this->info('📁 Encontrados ' . count($imageFiles) . ' archivos de imagen en storage/tours/');

        // Obtener todas las imágenes con nombres vacíos
        $emptyNameImages = DB::table('imagenes')
            ->where('imageable_type', 'App\\Models\\Tour')
            ->where('nombre', '')
            ->get();

        $this->info('🗃️  Encontrados ' . count($emptyNameImages) . ' registros de imágenes con nombres vacíos');

        if (count($emptyNameImages) === 0) {
            $this->info('✅ No se encontraron imágenes con nombres vacíos para reparar');
            return;
        }

        if (count($imageFiles) === 0) {
            $this->error('❌ No se encontraron archivos de imagen en el storage');
            return;
        }

        // Crear una estrategia de asignación basada en orden cronológico
        $this->info('🔄 Asignando nombres de archivos a registros de base de datos...');

        // Ordenar archivos por fecha de creación
        $sortedFiles = collect($imageFiles)->sort(function($a, $b) {
            $timeA = Storage::disk('public')->lastModified($a);
            $timeB = Storage::disk('public')->lastModified($b);
            return $timeA <=> $timeB;
        })->values();

        // Ordenar registros por fecha de creación
        $sortedImages = collect($emptyNameImages)->sortBy('created_at')->values();

        $repaired = 0;
        $errors = 0;

        // Asignar archivos a registros en orden cronológico
        foreach ($sortedImages as $index => $imageRecord) {
            if ($index < count($sortedFiles)) {
                $fileName = basename($sortedFiles[$index]);
                
                try {
                    DB::table('imagenes')
                        ->where('id', $imageRecord->id)
                        ->update(['nombre' => $fileName]);
                    
                    $this->line("✅ Imagen ID {$imageRecord->id} → {$fileName}");
                    $repaired++;
                } catch (\Exception $e) {
                    $this->error("❌ Error asignando {$fileName} a imagen ID {$imageRecord->id}: {$e->getMessage()}");
                    $errors++;
                }
            } else {
                $this->warn("⚠️  No hay suficientes archivos para el registro ID {$imageRecord->id}");
            }
        }

        $this->info('');
        $this->info("📊 Resumen de reparación:");
        $this->info("   ✅ Imágenes reparadas: {$repaired}");
        if ($errors > 0) {
            $this->error("   ❌ Errores: {$errors}");
        }

        // Verificar el resultado
        $this->info('');
        $this->info('🔍 Verificando resultado...');
        
        $remainingEmpty = DB::table('imagenes')
            ->where('imageable_type', 'App\\Models\\Tour')
            ->where('nombre', '')
            ->count();

        if ($remainingEmpty === 0) {
            $this->info('🎉 ¡Reparación completada exitosamente! Todas las imágenes tienen nombres válidos.');
        } else {
            $this->warn("⚠️  Aún quedan {$remainingEmpty} imágenes con nombres vacíos");
        }

        return 0;
    }
}