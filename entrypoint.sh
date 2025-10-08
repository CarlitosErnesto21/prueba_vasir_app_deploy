#!/bin/bash
set -e

echo "=== Railway Laravel Startup - Versión Simplificada ==="

# Configuración simple del puerto
PORT_NUM=${PORT:-8000}
echo "Puerto Railway: $PORT_NUM"

# Solo crear directorios básicos
mkdir -p storage/framework/{sessions,views,cache,testing} storage/logs bootstrap/cache
chmod -R 775 storage bootstrap/cache

# Migraciones simples
echo "Ejecutando migraciones..."
php artisan migrate --force

# Ejecutar seeders para crear roles y admin inicial
echo "Ejecutando seeders..."
php artisan db:seed --force

# NO USAR ARTISAN CACHE - puede causar conflictos
echo "Limpiando solo caches problemáticos..."
php artisan config:clear 2>/dev/null || true
php artisan route:clear 2>/dev/null || true

# USAR SOLO PHP BUILT-IN SERVER - NUNCA ARTISAN SERVE
echo "=== INICIANDO PHP BUILT-IN SERVER ==="
echo "Servidor: 0.0.0.0:$PORT_NUM"
echo "Document Root: /app/public"

cd /app
echo "Directorio actual: $(pwd)"
echo "Archivos en public: $(ls -la public/ | head -5)"

# Verificar que la tabla existe
echo "Verificando tablas de la base de datos..."
php artisan tinker --execute="echo 'Tablas: ' . implode(', ', \Illuminate\Support\Facades\Schema::getTableListing());"

# Comando final - Solo PHP server, sin Laravel artisan serve
exec php -S "0.0.0.0:$PORT_NUM" -t public
