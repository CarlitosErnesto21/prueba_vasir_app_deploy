# 🔄 CONFIGURACIÓN DE BACKUPS AUTOMÁTICOS

> **⚠️ IMPORTANTE:** Todos los desarrolladores deben configurar los backups automáticos después de clonar el proyecto.

## 🚀 INSTALACIÓN RÁPIDA

### 1. **Configurar Proyecto Laravel:**
```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate
```

### 2. **⚡ Configurar Backups (OBLIGATORIO):**

**En Windows:**
1. Abrir **PowerShell como Administrador**
2. Navegar al proyecto: `cd "ruta\del\proyecto"`
3. Ejecutar: `.\configurar_backup_automatico.bat`

### 3. **✅ Verificar Funcionamiento:**
```bash
# Verificar estado general
.\verificar_estado_backup.bat

# Verificar tarea programada
schtasks /query /tn "VASIR_Backup_Scheduler"

# Ejecutar backup manual
php artisan backup:auto
```

## 📋 QUÉ HACE LA CONFIGURACIÓN

- ✅ Crea backups automáticos cada 2 minutos
- ✅ Funciona en segundo plano (sin ventanas)
- ✅ Guarda backups en `storage/app/private/VASIR/`
- ✅ Solo incluye la base de datos (archivos excluidos por rendimiento)
- ✅ Independiente de VS Code (funciona aunque esté cerrado)

## 🛠️ COMANDOS ÚTILES

```bash
# Ver últimos backups
php artisan backup:list

# Backup manual
php artisan backup:auto

# Desactivar backups automáticos
schtasks /delete /tn "VASIR_Backup_Scheduler" /f

# Ver estado completo
.\verificar_estado_backup.bat
```

## 🚨 SOLUCIÓN DE PROBLEMAS

| Problema | Solución |
|----------|----------|
| "Error: acceso denegado" | Ejecutar como Administrador |
| "PHP no reconocido" | Instalar PHP y agregarlo al PATH |
| "No se encuentra artisan" | Ejecutar desde la carpeta raíz del proyecto |
| "No hay backups" | Verificar `storage/app/private/VASIR/` |

---
📚 **Documentación completa:** Ver `README_BACKUPS.md`
