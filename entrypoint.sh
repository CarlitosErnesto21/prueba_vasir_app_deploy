#!/bin/bash
set -e

echo "=== Railway Laravel Startup ==="

# Debug: mostrar variables de entorno relacionadas con el puerto
echo "Debug - Variables de puerto:"
echo "PORT original: '$PORT'"
echo "Tipo de PORT: $(echo $PORT | od -c)"

# Función mejorada para extraer solo números
extract_port() {
    # Usar regex para extraer solo dígitos
    echo "$1" | sed 's/[^0-9]//g'
}

# Configurar puerto de forma más agresiva
if [ -n "$PORT" ]; then
    # Extraer solo los números del puerto
    SERVER_PORT=$(extract_port "$PORT")

    # Verificar que tenemos al menos un dígito
    if [ -n "$SERVER_PORT" ] && [ "$SERVER_PORT" -gt 0 ] 2>/dev/null; then
        echo "Puerto extraído correctamente: $SERVER_PORT"
    else
        SERVER_PORT=8000
        echo "No se pudo extraer puerto válido, usando 8000"
    fi
else
    SERVER_PORT=8000
    echo "PORT no definido, usando puerto 8000"
fi

echo "Puerto final configurado: $SERVER_PORT"

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

# Intentar múltiples estrategias para iniciar el servidor
echo "Iniciando servidor Laravel..."
echo "Host: 0.0.0.0"
echo "Puerto: $SERVER_PORT"

# Estrategia 1: Usar PHP built-in server directamente (más compatible)
echo "Usando PHP built-in server..."
cd /app

# Exportar el puerto como variable de entorno numérica limpia
export SERVER_PORT_CLEAN=$(echo "$SERVER_PORT" | tr -d '"' | tr -d "'")

echo "Puerto limpio: $SERVER_PORT_CLEAN"
exec php -S "0.0.0.0:$SERVER_PORT_CLEAN" -t public public/index.php
