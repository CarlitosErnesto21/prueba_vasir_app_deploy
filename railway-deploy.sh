#!/bin/sh

# Generar clave de aplicación si no existe
if [ -z "$APP_KEY" ]; then
    echo "Generando APP_KEY..."
    php artisan key:generate --force
fi

# Ejecutar migraciones
echo "Ejecutando migraciones..."
php artisan migrate --force

# Ejecutar seeders (opcional)
# php artisan db:seed --force

# Limpiar y optimizar
echo "Optimizando aplicación..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Compilar assets
echo "Compilando assets..."
npm ci
npm run build

echo "Deploy completado!"
