#!/bin/sh

echo "Iniciando deploy..."

# Instalar dependencias PHP
composer install --no-dev --optimize-autoloader --ignore-platform-reqs

# Generar clave si no existe
php artisan key:generate --force

# Ejecutar migraciones
php artisan migrate --force

# Optimizar instalación de Node.js con más memoria y configuraciones para Railway
export NODE_OPTIONS="--max-old-space-size=1024 --max-semi-space-size=256"
export NPM_CONFIG_PROGRESS=false
export NPM_CONFIG_AUDIT=false
export NPM_CONFIG_FUND=false

# Limpiar cache npm por si acaso
npm cache clean --force 2>/dev/null || true

# Instalar dependencias con configuraciones optimizadas
npm ci --no-audit --no-fund --maxsockets 1 --network-timeout=60000

# Compilar assets
npm run build

# Limpiar node_modules para ahorrar espacio
rm -rf node_modules

echo "Build completado!"
