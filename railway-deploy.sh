#!/bin/sh

echo "Iniciando deploy..."

# Instalar dependencias PHP
composer install --no-dev --optimize-autoloader

# Generar clave si no existe
php artisan key:generate --force

# Compilar assets con límite de memoria
export NODE_OPTIONS="--max-old-space-size=512"
npm ci --production=false
npm run build

# Limpiar node_modules para ahorrar espacio
rm -rf node_modules

echo "Build completado!"
