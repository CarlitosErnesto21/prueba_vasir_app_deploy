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

# Crear archivo .env basado en .env.example
RUN cp .env.example .env

# Crear directorios necesarios y permisos
RUN mkdir -p storage/framework/{sessions,views,cache,testing} storage/logs bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# Generar clave de aplicación
RUN php artisan key:generate --force

# Copiar script de inicio
COPY start.sh /start.sh
RUN chmod +x /start.sh

# Exponer puerto
EXPOSE 8000

# Usar script de inicio
CMD ["/start.sh"]
