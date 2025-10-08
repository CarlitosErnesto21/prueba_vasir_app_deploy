#!/bin/bash

# Script de inicio para Railway

echo "Iniciando aplicación Laravel..."

# Ejecutar migraciones si es necesario
if [ "$RUN_MIGRATIONS" = "true" ]; then
    echo "Ejecutando migraciones..."
    php artisan migrate --force
fi

# Crear directorios si no existen
mkdir -p storage/framework/{sessions,views,cache,testing} storage/logs bootstrap/cache

# Establecer permisos
chmod -R 775 storage bootstrap/cache

# Cachear configuraciones (con variables de entorno de Railway)
echo "Cacheando configuraciones..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Iniciar servidor
echo "Iniciando servidor en puerto ${PORT:-8000}..."
exec php artisan serve --host=0.0.0.0 --port=${PORT:-8000}