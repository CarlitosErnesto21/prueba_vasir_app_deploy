# 🔄 SISTEMA DE BACKUPS AUTOMÁTICOS - VASIR

## 📋 CONFIGURACIÓN PARA DESARROLLADORES

### ✅ **REQUISITOS PREVIOS:**
- Windows 10/11
- PHP instalado y en PATH
- Composer instalado
- Proyecto Laravel funcionando

### 🚀 **INSTALACIÓN (SOLO LA PRIMERA VEZ):**

1. **Clonar el repositorio y instalar dependencias:**
   ```bash
   git clone https://github.com/CarlitosErnesto21/AgenciaVasir.git
   cd vasir_app
   composer install
   npm install
   ```

2. **Configurar el entorno:**
   ```bash
   cp .env.example .env
   php artisan key:generate
   php artisan migrate
   ```

3. **⚠️ IMPORTANTE - CONFIGURAR BACKUPS AUTOMÁTICOS:**
   
   **PASO A:** Abrir **PowerShell como Administrador**
   - Clic derecho en el botón de Windows
   - Seleccionar "Windows PowerShell (Administrador)"
   
   **PASO B:** Navegar al proyecto y ejecutar:
   ```powershell
   cd "ruta\donde\clonaste\vasir_app"
   .\configurar_backup_automatico.bat
   ```
   
   **PASO C:** Verificar que funciona:
   ```powershell
   schtasks /query /tn "VASIR_Backup_Scheduler"
   php artisan backup:auto
   ```

### ⚙️ **QUÉ HACE EL CONFIGURADOR:**
- ✅ Detecta automáticamente la ruta de tu proyecto
- ✅ Crea los scripts necesarios con TUS rutas específicas
- ✅ Configura la tarea programada de Windows
- ✅ Ejecuta backups cada 2 minutos automáticamente
- ✅ Funciona completamente en segundo plano (sin ventanas)

### 🗂️ **DÓNDE SE GUARDAN LOS BACKUPS:**
Los backups se almacenan en: `storage/app/private/VASIR/`

### 🛠️ **COMANDOS ÚTILES:**

**Ver últimos backups:**
```bash
php artisan backup:list
```

**Ejecutar backup manual:**
```bash
php artisan backup:auto
```

**Ver configuración:**
```bash
php artisan tinker
# Dentro de tinker:
App\Models\SiteSetting::get('auto_backup_enabled')
App\Models\SiteSetting::get('backup_frequency')
```

**Desactivar backups automáticos:**
```bash
schtasks /delete /tn "VASIR_Backup_Scheduler" /f
```

### 🚨 **IMPORTANTE:**
- Los backups funcionan **independientemente** de si VS Code está abierto
- No es necesario ejecutar `php artisan serve`
- Solo se crean logs cuando hay errores (ver `backup_error.log`)
- Cada desarrollador debe ejecutar `configurar_backup_automatico.bat` una vez

### 🔧 **SOLUCIÓN DE PROBLEMAS:**

**Si no funciona:**
1. Verificar que PHP está en PATH: `php --version`
2. Ejecutar el configurador como administrador
3. Revisar el archivo `backup_error.log`
4. Verificar la tarea: `schtasks /query /tn "VASIR_Backup_Scheduler"`

**Cambiar frecuencia de backup:**
Editar la línea en `configurar_backup_automatico.bat`:
```
/sc minute /mo 2    # Cada 2 minutos
/sc hourly          # Cada hora  
/sc daily           # Diario
```
