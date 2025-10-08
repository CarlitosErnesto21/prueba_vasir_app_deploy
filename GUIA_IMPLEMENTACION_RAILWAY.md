# Guía de Implementación Laravel en Railway

## Introducción
Este documento detalla los pasos y configuraciones necesarias para desplegar exitosamente una aplicación Laravel con Inertia.js en Railway.

## Archivos y Configuraciones Requeridas

### 1. Dockerfile
**Archivo:** `Dockerfile`
**Propósito:** Configuración del contenedor para el entorno de producción

```dockerfile
# Usar imagen base de PHP con Apache
FROM php:8.2-apache

# Instalar dependencias del sistema
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    nodejs \
    npm \
    sqlite3 \
    libsqlite3-dev

# Limpiar caché
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Instalar extensiones PHP
RUN docker-php-ext-install pdo_mysql pdo_sqlite mbstring exif pcntl bcmath gd

# Obtener Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Establecer directorio de trabajo
WORKDIR /app

# Copiar archivos de la aplicación
COPY . /app

# Instalar dependencias de Composer
RUN composer install --no-dev --optimize-autoloader

# Instalar dependencias de Node.js y compilar assets
RUN npm install
RUN npm run build

# Configurar permisos
RUN chown -R www-data:www-data /app/storage /app/bootstrap/cache
RUN chmod -R 775 /app/storage /app/bootstrap/cache

# Hacer ejecutable el entrypoint
RUN chmod +x /app/entrypoint.sh

# Exponer puerto
EXPOSE 8000

# Usar entrypoint personalizado
ENTRYPOINT ["/app/entrypoint.sh"]
```

### 2. Script de Entrada (Entrypoint)
**Archivo:** `entrypoint.sh`
**Propósito:** Automatizar la inicialización de la aplicación

```bash
#!/bin/bash

# Salir si algún comando falla
set -e

echo "🚀 Iniciando aplicación Laravel en Railway..."

# Configurar el puerto desde la variable de entorno de Railway
if [ -n "$PORT" ]; then
    export PORT="$PORT"
    echo "✅ Puerto configurado: $PORT"
else
    export PORT=8000
    echo "⚠️  Puerto por defecto: 8000"
fi

# Generar clave de aplicación si no existe
if [ -z "$APP_KEY" ]; then
    echo "🔑 Generando APP_KEY..."
    php artisan key:generate --force
fi

# Ejecutar migraciones
echo "📊 Ejecutando migraciones de base de datos..."
php artisan migrate --force

# Ejecutar seeders
echo "🌱 Ejecutando seeders..."
php artisan db:seed --force

# Limpiar y optimizar cachés
echo "🧹 Optimizando aplicación..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "✅ Aplicación lista. Iniciando servidor en puerto $PORT..."

# Iniciar servidor PHP integrado (NO usar artisan serve en Railway)
exec php -S 0.0.0.0:$PORT -t public
```

### 3. Configuración de CORS
**Archivo:** `config/cors.php`
**Modificación crítica:** Patrones de origen correctos

```php
<?php

return [
    'paths' => ['api/*', 'sanctum/csrf-cookie'],
    'allowed_methods' => ['*'],
    'allowed_origins' => [
        'http://localhost:5173',
        'http://localhost:8000',
        'https://web-production-32cf.up.railway.app' // Tu dominio específico
    ],
    'allowed_origins_patterns' => [
        '/^https:\/\/.*\.railway\.app$/',  // CRÍTICO: Usar regex válido
        '/^https:\/\/.*\.render\.com$/',
        '/^https:\/\/.*\.vercel\.app$/'
    ],
    'allowed_headers' => ['*'],
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => true,
];
```

### 4. Configuración de Middleware
**Archivo:** `bootstrap/app.php`
**Propósito:** Configurar Sanctum para autenticación API

```php
return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->web(append: [
            HandleInertiaRequests::class,
            AddLinkHeadersForPreloadedAssets::class
        ]);

        // Middleware de Sanctum para API
        $middleware->api(prepend: [
            EnsureFrontendRequestsAreStateful::class,
        ]);

        $middleware->alias([
            'rutas.admin' => RutasAdmin::class,
            'role' => RoleMiddleware::class,
            'permission' => PermissionMiddleware::class,
            'role_or_permission' => RoleOrPermissionMiddleware::class
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })
    ->create();
```

### 5. Variables de Entorno para Railway
**Archivo:** `railway.env` (ejemplo)

```env
APP_NAME="AgenciaVasir"
APP_ENV=production
APP_KEY=base64:TuClaveGeneradaAutomaticamente
APP_DEBUG=false
APP_TIMEZONE=UTC
APP_URL=https://tu-dominio.up.railway.app

DB_CONNECTION=sqlite
DB_DATABASE=/app/database/database.sqlite

BROADCAST_CONNECTION=log
CACHE_STORE=database
FILESYSTEM_DISK=local
QUEUE_CONNECTION=database
SESSION_DRIVER=database

# Configuración de correo (opcional)
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=tu-email@gmail.com
MAIL_PASSWORD=tu-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=tu-email@gmail.com
MAIL_FROM_NAME="${APP_NAME}"

# Admin por defecto (configurar en Railway)
ADMIN_NAME="Tu Nombre"
ADMIN_EMAIL="admin@tudominio.com"
ADMIN_PASSWORD="password-seguro"
```

### 6. Configuración de Base de Datos
**Modificación:** Usar SQLite para simplificar el despliegue

```php
// config/database.php - Ya configurado por defecto en Laravel
'sqlite' => [
    'driver' => 'sqlite',
    'url' => env('DB_URL'),
    'database' => env('DB_DATABASE', database_path('database.sqlite')),
    'prefix' => '',
    'foreign_key_constraints' => env('DB_FOREIGN_KEYS', true),
    'busy_timeout' => null,
    'journal_mode' => null,
    'synchronous' => null,
],
```

## Configuración en Railway

### 1. Pasos Iniciales
1. Conectar repositorio de GitHub a Railway
2. Railway detectará automáticamente el Dockerfile
3. Configurar variables de entorno en el dashboard

### 2. Variables de Entorno Críticas
```
APP_ENV=production
APP_DEBUG=false
APP_KEY=base64:... (se genera automáticamente)
DB_CONNECTION=sqlite
ADMIN_NAME=Tu Nombre
ADMIN_EMAIL=admin@ejemplo.com
ADMIN_PASSWORD=contraseña-segura
```

### 3. Configuración de Dominio
- Railway proporciona automáticamente un dominio como: `web-production-xxxxx.up.railway.app`
- Configurar este dominio en `allowed_origins` del archivo CORS

## Puntos Críticos para el Éxito

### ❌ Errores Comunes a Evitar
1. **NO usar `artisan serve`** en producción con Railway
2. **NO usar patrones CORS incorrectos** (usar regex válido)
3. **NO olvidar** hacer ejecutable el `entrypoint.sh`

### ✅ Mejores Prácticas
1. **Usar PHP built-in server** con `php -S 0.0.0.0:$PORT`
2. **Pre-compilar assets** con `npm run build` en Dockerfile
3. **Cachear configuraciones** en el entrypoint
4. **Usar SQLite** para simplicidad (sin configuración de BD externa)

## Orden de Despliegue
1. Crear y configurar `Dockerfile`
2. Crear `entrypoint.sh` y hacerlo ejecutable
3. Configurar CORS correctamente
4. Configurar variables de entorno en Railway
5. Hacer push al repositorio
6. Railway desplegará automáticamente

## Verificación Post-Despliegue
1. ✅ Aplicación accesible en el dominio proporcionado
2. ✅ Login funcional
3. ✅ APIs funcionando (sin errores 500)
4. ✅ Dashboard cargando correctamente
5. ✅ Base de datos inicializada con seeders

---

*Este documento garantiza un despliegue exitoso en Railway siguiendo estas configuraciones exactas.*
