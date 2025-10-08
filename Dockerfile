# Usar imagen PHP con extensiones necesarias
FROM php:8.2-cli

# Instalar extensiones PHP necesarias
RUN apt-get update && apt-get install -y \
    zip \
    unzip \
    libzip-dev \
    && docker-php-ext-install zip \
    && rm -rf /var/lib/apt/lists/*

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Establecer directorio de trabajo
WORKDIR /app

# Copiar archivos de composer
COPY composer.json composer.lock ./

# Instalar dependencias PHP sin extensiones requeridas
RUN composer install --no-dev --optimize-autoloader --no-scripts --ignore-platform-req=ext-zip

# Copiar el resto de la aplicación
COPY . .

# Crear directorios necesarios y permisos
RUN mkdir -p storage/framework/{sessions,views,cache,testing} storage/logs bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# Generar clave de aplicación si no existe
RUN php artisan key:generate --force

# Cachear configuraciones
RUN php artisan config:cache \
    && php artisan route:cache \
    && php artisan view:cache

# Exponer puerto
EXPOSE 8000

# Comando para iniciar la aplicación
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
