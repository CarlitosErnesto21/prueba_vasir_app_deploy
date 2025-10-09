<?php
/**
 * Script para migrar imágenes existentes de /public/images/ a /storage/app/public/
 * EJECUTAR SOLO UNA VEZ
 */

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\Storage;

// Configurar Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$folders = [
    'tours' => 'tours',
    'productos' => 'productos', 
    'hoteles' => 'hoteles',
    'aerolinea' => 'aerolinea'
];

foreach ($folders as $oldFolder => $newFolder) {
    $oldPath = public_path("images/{$oldFolder}");
    
    if (!is_dir($oldPath)) {
        echo "❌ Carpeta {$oldPath} no existe\n";
        continue;
    }
    
    $files = glob($oldPath . '/*');
    echo "📁 Migrando {$oldFolder}: " . count($files) . " archivos\n";
    
    foreach ($files as $file) {
        if (is_file($file)) {
            $filename = basename($file);
            $content = file_get_contents($file);
            
            // Guardar en storage
            Storage::disk('public')->put("{$newFolder}/{$filename}", $content);
            echo "  ✅ {$filename}\n";
        }
    }
}

echo "🎉 Migración completa!\n";