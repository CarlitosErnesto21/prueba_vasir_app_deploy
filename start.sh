#!/bin/bash
set -e

# Script de inicio optimizado para Railway
echo "=== Iniciando aplicación Laravel ==="

# Configurar el puerto correctamente
SERVER_PORT=${PORT:-8000}
echo "Puerto configurado: $SERVER_PORT"

# Ejecutar migraciones
echo "Ejecutando migraciones..."
php artisan migrate --force

# Crear directorios necesarios
echo "Creando directorios de storage..."
mkdir -p storage/framework/{sessions,views,cache,testing} storage/logs bootstrap/cache
chmod -R 775 storage bootstrap/cache

# Optimizaciones Laravel
echo "Optimizando configuraciones..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Iniciar servidor con puerto como número
echo "Iniciando servidor Laravel en 0.0.0.0:$SERVER_PORT"
exec php artisan serve --host=0.0.0.0 --port="$SERVER_PORT"
