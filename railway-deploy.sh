#!/bin/sh

echo "Iniciando deploy..."

# Instalar dependencias PHP solamente
composer install --no-dev --optimize-autoloader --ignore-platform-reqs

# Generar clave si no existe
php artisan key:generate --force

# Ejecutar migraciones
php artisan migrate --force

# Optimizar Laravel para producción
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "Build completado - Assets pre-compilados incluidos!"
