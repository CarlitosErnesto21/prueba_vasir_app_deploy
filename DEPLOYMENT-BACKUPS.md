# 🚀 GUÍA DE DEPLOYMENT - CONFIGURACIÓN DE BACKUPS

Esta guía te ayudará a configurar los backups cuando deploys tu aplicación a producción.

## 📋 CONFIGURACIONES DISPONIBLES

### 1. 🏠 DESARROLLO (Actual)
```env
BACKUP_DISKS=backup
```
- Guarda en: `storage/app/private/VASIR/`
- ✅ Ya configurado y funcionando

### 2. ☁️ PRODUCCIÓN - Solo AWS S3
```env
BACKUP_DISKS=backup-s3
AWS_ACCESS_KEY_ID=tu_access_key
AWS_SECRET_ACCESS_KEY=tu_secret_key
AWS_DEFAULT_REGION=us-east-1
AWS_BACKUP_BUCKET=vasir-backups-prod
```

### 3. 🔒 PRODUCCIÓN - Solo Local Seguro
```env
BACKUP_DISKS=backup-local-prod
BACKUP_LOCAL_PATH=/var/backups/vasir
```

### 4. 💎 PRODUCCIÓN - HÍBRIDO (Recomendado)
```env
BACKUP_DISKS=backup-local-prod,backup-s3
BACKUP_LOCAL_PATH=/var/backups/vasir
AWS_ACCESS_KEY_ID=tu_access_key
AWS_SECRET_ACCESS_KEY=tu_secret_key
AWS_DEFAULT_REGION=us-east-1
AWS_BACKUP_BUCKET=vasir-backups-prod
```

## 🎯 PASOS PARA DEPLOYMENT

### Para AWS S3:
1. Crea un bucket S3: `vasir-backups-prod`
2. Crea credenciales IAM con permisos S3
3. Actualiza las variables en `.env` de producción
4. ¡Listo! Los backups van directo a S3

### Para Local + S3 (Híbrido):
1. En el servidor: `sudo mkdir -p /var/backups/vasir`
2. Configura permisos: `sudo chown www-data:www-data /var/backups/vasir`
3. Configura S3 (pasos de arriba)
4. Actualiza `.env` con configuración híbrida
5. ¡Listo! Backups locales + nube automáticamente

## 💰 COSTOS ESTIMADOS

### AWS S3:
- Backup 1GB: ~$0.30/mes
- Backup 5GB: ~$1.50/mes
- Backup 10GB: ~$3.00/mes

### DigitalOcean Spaces:
- $5/mes incluye 250GB

## 🛠️ COMANDOS ÚTILES

```bash
# Verificar configuración
php artisan backup:list

# Generar backup manual
php artisan backup:run

# Limpiar backups antiguos
php artisan backup:clean

# Ver estado de backups
php artisan backup:monitor
```

## ⚠️ NOTAS IMPORTANTES

1. **Nunca** subas credenciales AWS al repositorio
2. Las variables `AWS_*` solo van en el `.env` de producción
3. Para desarrollo, deja todo como está actualmente
4. Siempre prueba los backups después del deployment

## 🆘 TROUBLESHOOTING

### Error: "Disk not found"
- Verifica que las variables `AWS_*` estén correctas
- Asegúrate de que el bucket S3 existe

### Error: "Permission denied"
- Para backups locales: `sudo chown -R www-data:www-data /var/backups/vasir`
- Para S3: Verifica permisos IAM

### Error: "Temporary directory"
- Verifica que el directorio temporal tenga permisos de escritura
