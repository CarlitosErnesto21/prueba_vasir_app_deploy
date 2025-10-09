# 🚀 Guía de Deployment: Render + JawsDB MySQL

## Paso 1: Configurar JawsDB MySQL

1. **Crear cuenta**: Ve a https://www.jawsdb.com y regístrate
2. **Crear base de datos**:
   - Plan: "Kitefin Shared" ($10/mes)
   - Región: US East (recomendada)
   - Nombre: `vasir_agency` (automático)
3. **Obtener credenciales**:
   - Ve a tu dashboard de JawsDB
   - Copia las credenciales de conexión
   - Se ven así: `mysql://username:password@hostname:port/database_name`

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

### Variables de JawsDB (reemplazar con tus datos):
```
DB_CONNECTION=mysql
DB_HOST=[tu-host-jawsdb].compute-1.amazonaws.com
DB_PORT=3306
DB_DATABASE=[tu-database-jawsdb]
DB_USERNAME=[tu-usuario-jawsdb]
DB_PASSWORD=[tu-password-jawsdb]
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
- **JawsDB**: $10/mes (plan Kitefin Shared)
- **Render**: Gratis (con limitaciones) o $7/mes
- **Total**: $10-17/mes

## Notas importantes:
- JawsDB es MySQL tradicional (soporta foreign keys)
- SSL habilitado automáticamente
- Backups automáticos incluidos
