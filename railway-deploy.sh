#!/bin/sh

echo "Iniciando deploy..."

# Instalar dependencias PHP ignorando requisitos de zip
composer install --no-dev --optimize-autoloader --ignore-platform-req=ext-zip

# Generar clave si no existe
php artisan key:generate --force

# Ejecutar migraciones
php artisan migrate --force

# Crear directorios necesarios
mkdir -p storage/framework/{sessions,views,cache,testing} storage/logs bootstrap/cache

# Optimizar Laravel para producción
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "Build completado - Assets pre-compilados incluidos!"
