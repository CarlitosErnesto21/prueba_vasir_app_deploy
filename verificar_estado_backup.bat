@echo off
setlocal enabledelayedexpansion

REM Script para verificar el estado de los backups automáticos
color 0B
echo ================================================
echo     VERIFICADOR DE ESTADO - BACKUPS VASIR
echo ================================================
echo.

REM Verificar si la tarea programada existe
echo 🔍 Verificando tarea programada...
schtasks /query /tn "VASIR_Backup_Scheduler" >nul 2>&1
if %errorLevel% neq 0 (
    color 0C
    echo ❌ La tarea programada NO existe
    echo.
    echo 💡 Para configurar los backups automáticos:
    echo    Ejecutar como administrador: configurar_backup_automatico.bat
    echo.
    goto :fin
)

echo ✅ Tarea programada encontrada
echo.

REM Mostrar información de la tarea
echo 📊 Información de la tarea:
schtasks /query /tn "VASIR_Backup_Scheduler"
echo.

REM Verificar archivos del sistema
echo 🔍 Verificando archivos del sistema...
if exist "C:\vasir_backup_silent.vbs" (
    echo ✅ Script VBS: C:\vasir_backup_silent.vbs
) else (
    echo ❌ Script VBS no encontrado
)

if exist "ejecutar_backup.ps1" (
    echo ✅ Script PowerShell: ejecutar_backup.ps1
) else (
    echo ❌ Script PowerShell no encontrado
)

echo.

REM Verificar comando de backup
echo 🧪 Probando comando de backup...
php artisan backup:auto >nul 2>&1
if %errorLevel% equ 0 (
    echo ✅ Comando de backup funcional
) else (
    echo ❌ Error en comando de backup
)

echo.

REM Mostrar últimos backups
echo 📁 Últimos backups creados:
if exist "storage\app\private\VASIR" (
    dir "storage\app\private\VASIR\*.zip" /O-D /B 2>nul | findstr . >nul
    if %errorLevel% equ 0 (
        for /f "tokens=*" %%f in ('dir "storage\app\private\VASIR\*.zip" /O-D /B 2^>nul') do (
            set /a count+=1
            if !count! leq 5 (
                echo   • %%f
            )
        )
    ) else (
        echo   ❌ No se encontraron backups
    )
) else (
    echo   ❌ Carpeta de backups no encontrada
)

echo.

REM Verificar logs de errores
if exist "backup_error.log" (
    echo ⚠️ Archivo de errores encontrado:
    echo.
    type "backup_error.log"
    echo.
) else (
    echo ✅ No hay errores registrados
)

:fin
echo ================================================
echo Verificación completada. Presiona cualquier tecla para continuar...
pause >nul
