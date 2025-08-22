# 🌐 BACKUPS EN PRODUCCIÓN - VASIR

## 📋 CONFIGURACIÓN PARA HOSTING/PRODUCCIÓN

### 🎯 **OPCIÓN 1: HOSTING CON CRON JOBS (RECOMENDADO)**

Si tu hosting permite configurar cron jobs:

1. **Agregar en cPanel/Panel de Control:**
   ```bash
   # Ejecutar cada 2 minutos
   */2 * * * * /usr/local/bin/php /path/to/your/project/artisan schedule:run >> /dev/null 2>&1
   ```

2. **O si prefieres menos frecuente (cada hora):**
   ```bash
   # Ejecutar cada hora
   0 * * * * /usr/local/bin/php /path/to/your/project/artisan schedule:run >> /dev/null 2>&1
   ```

### 🎯 **OPCIÓN 2: HOSTING SIN CRON JOBS**

Si tu hosting NO permite cron jobs, usa un servicio externo:

**A) UptimeRobot (GRATIS):**
1. Crear cuenta en uptimerobot.com
2. Agregar un monitor HTTP
3. URL: `https://tudominio.com/backup-trigger`
4. Intervalo: cada 5 minutos

**B) Crear endpoint público:**
```php
// En routes/web.php
Route::get('/backup-trigger', function () {
    // Verificar token de seguridad
    if (request('token') !== env('BACKUP_TRIGGER_TOKEN')) {
        abort(403);
    }
    
    Artisan::call('backup:auto');
    return response()->json(['status' => 'backup triggered']);
});
```

### 🎯 **OPCIÓN 3: BACKUPS MANUALES DESDE ADMIN**

Agregar botón en el panel administrativo:

```php
// En tu controlador de admin
public function triggerBackup()
{
    try {
        Artisan::call('backup:auto');
        return back()->with('success', 'Backup ejecutado exitosamente');
    } catch (\Exception $e) {
        return back()->with('error', 'Error al ejecutar backup: ' . $e->getMessage());
    }
}
```

### ⚙️ **CONFIGURACIÓN PREVIA AL DEPLOY:**

1. **Verificar configuración de backup:**
   ```bash
   php artisan config:show backup
   ```

2. **Asegurar que la carpeta de backups sea escribible:**
   ```bash
   chmod 755 storage/app/private
   ```

3. **Configurar variables de entorno:**
   ```env
   # En .env de producción
   BACKUP_TRIGGER_TOKEN=tu_token_super_secreto_aqui
   ```

### 🗂️ **ALMACENAMIENTO EN PRODUCCIÓN:**

**Para hosting compartido:**
- Los backups se guardan en `storage/app/private/VASIR/`
- Asegurar que esta carpeta NO sea accesible vía web

**Para hosting avanzado:**
- Configurar almacenamiento en S3, Google Drive, etc.
- Editar `config/backup.php` y agregar discos adicionales

### 🚨 **CONSIDERACIONES IMPORTANTES:**

1. **Frecuencia:** En producción, considera backups menos frecuentes (cada hora o diarios)
2. **Recursos:** Backups consumen CPU y espacio en disco
3. **Notificaciones:** Configurar alertas por email si fallan
4. **Retención:** Configurar limpieza automática de backups antiguos

### 🔧 **CAMBIAR FRECUENCIA EN PRODUCCIÓN:**

Editar `app/Console/Kernel.php`:

```php
// Cada hora
$schedule->command('backup:auto')->hourly();

// Diario a las 2 AM
$schedule->command('backup:auto')->dailyAt('02:00');

// Solo días laborables
$schedule->command('backup:auto')->dailyAt('02:00')->weekdays();
```

### 📧 **CONFIGURAR NOTIFICACIONES:**

```php
// En config/backup.php, sección 'notifications'
'mail' => [
    'to' => 'admin@tudominio.com',
    'from' => [
        'address' => 'noreply@tudominio.com',
        'name' => 'VASIR Backups',
    ],
],
```

### 🧪 **PROBAR EN PRODUCCIÓN:**

```bash
# SSH al servidor
php artisan backup:auto
php artisan backup:list
```
