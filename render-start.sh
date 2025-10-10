#!/bin/bash

# Script de inicio para Render
set -e

echo "🚀 Iniciando aplicación Laravel en Render..."

# Esperar a que la base de datos esté disponible
echo "⏳ Esperando conexión a la base de datos..."
until php artisan db:show --quiet 2>/dev/null; do
    echo "⏳ Base de datos no disponible, esperando..."
    sleep 2
done

echo "✅ Base de datos conectada!"

# Ejecutar migraciones
echo "🔄 Ejecutando migraciones..."
php artisan migrate --force

# Ejecutar seeders (datos iniciales)
echo "🌱 Ejecutando seeders..."
php artisan db:seed --force

# Configurar sistema de almacenamiento
echo "📁 Configurando almacenamiento..."
php setup-storage.php

# Asegurar que el symlink funcione
echo "🔗 Creando enlace simbólico de storage..."
php artisan storage:link || echo "⚠️ Error creando symlink con artisan, usando script personalizado"

# Limpiar y cachear configuraciones
echo "🧹 Optimizando aplicación..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Crear enlace simbólico para storage si no existe
if [ ! -L public/storage ]; then
    php artisan storage:link
fi

echo "✅ Aplicación lista!"

# Iniciar Apache
exec apache2-foreground
