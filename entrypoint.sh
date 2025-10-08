#!/bin/bash
set -e

echo "=== Railway Laravel Startup ==="

# Función para verificar si es un número
is_number() {
    case $1 in
        ''|*[!0-9]*) return 1 ;;
        *) return 0 ;;
    esac
}

# Configurar puerto de forma segura
if [ -n "$PORT" ] && is_number "$PORT"; then
    SERVER_PORT=$PORT
else
    SERVER_PORT=8000
    echo "Warning: PORT no es un número válido, usando puerto por defecto 8000"
fi

echo "Configurando servidor en puerto: $SERVER_PORT"

# Preparar Laravel
echo "Preparando directorios..."
mkdir -p storage/framework/{sessions,views,cache,testing}
mkdir -p storage/logs
mkdir -p bootstrap/cache
chmod -R 775 storage bootstrap/cache

# Migraciones
echo "Ejecutando migraciones..."
php artisan migrate --force

# Limpiar caches existentes
echo "Limpiando caches..."
php artisan config:clear || true
php artisan route:clear || true
php artisan view:clear || true

# Regenerar caches
echo "Generando caches..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Iniciar servidor
echo "Iniciando servidor Laravel..."
echo "Host: 0.0.0.0"
echo "Puerto: $SERVER_PORT"

exec php artisan serve --host=0.0.0.0 --port=$SERVER_PORT
