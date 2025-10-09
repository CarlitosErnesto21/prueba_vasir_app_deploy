# 🚀 Guía de Deployment: Render + PlanetScale

## Paso 1: Configurar PlanetScale

1. **Crear cuenta**: Ve a https://planetscale.com y regístrate
2. **Crear base de datos**:
   - Nombre: `vasir-agency`
   - Región: `us-east` (gratis)
3. **Obtener credenciales**:
   - Ve a tu base de datos
   - Clic en "Connect"
   - Selecciona "Laravel"
   - Copia las credenciales

## Paso 2: Configurar Render

1. **Crear cuenta**: Ve a https://render.com y regístrate con GitHub
2. **Nuevo servicio**:
   - Clic en "New +"
   - Selecciona "Web Service"
   - Conecta tu repositorio: `prueba_vasir_app_deploy`

3. **Configuración del servicio**:
   ```
   Name: vasir-agency-app
   Environment: Native (no Docker por simplicidad)
   Branch: main
   Build Command: composer install --no-dev --optimize-autoloader
   Start Command: php artisan serve --host=0.0.0.0 --port=$PORT
   ```

## Paso 3: Variables de Entorno en Render

Agrega estas variables en Render Dashboard:

### Variables básicas:
```
APP_NAME=Vasir Agency
APP_ENV=production
APP_DEBUG=false
APP_KEY=[genera automáticamente]
APP_URL=https://vasir-agency-app.onrender.com
LOG_CHANNEL=stderr
```

### Variables de PlanetScale (reemplazar con tus datos):
```
DB_CONNECTION=mysql
DB_HOST=[tu-host-planetscale].us-east-1.psdb.cloud
DB_PORT=3306
DB_DATABASE=vasir-agency
DB_USERNAME=[tu-usuario-planetscale]
DB_PASSWORD=[tu-password-planetscale]
MYSQL_ATTR_SSL_CA=/etc/ssl/certs/ca-certificates.crt
```

### Variables adicionales:
```
SESSION_DRIVER=database
CACHE_DRIVER=database
MAIL_MAILER=log
```

## Paso 4: Deploy

1. **Push a GitHub** (ya hecho)
2. **Deploy automático** se ejecutará
3. **Migraciones** se ejecutarán automáticamente

## Costos:
- **PlanetScale**: Gratis (hasta 1GB)
- **Render**: Gratis (con limitaciones) o $7/mes

## Notas importantes:
- PlanetScale no soporta foreign keys
- Las migraciones están configuradas para esto
- SSL está habilitado automáticamente
