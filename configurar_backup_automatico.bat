@echo off
setlocal enabledelayedexpansion

REM Script para configurar backups automáticos en cualquier entorno de desarrollo
REM IMPORTANTE: Ejecutar este archivo como administrador

color 0A
echo.
echo ====================================================
echo     CONFIGURADOR DE BACKUPS AUTOMATICOS VASIR
echo ====================================================
echo.
echo     Hola! Este programa va a configurar los backups
echo     automaticos para que tus datos esten seguros.
echo.
echo     IMPORTANTE: Debes ejecutar como ADMINISTRADOR
echo ====================================================
echo.

REM Verificar permisos de administrador
net session >nul 2>&1
if %errorLevel% neq 0 (
    color 0C
    echo.
    echo ❌❌❌ ERROR: NECESITAS PERMISOS DE ADMINISTRADOR ❌❌❌
    echo.
    echo Para solucionarlo:
    echo 1. Cierra esta ventana
    echo 2. Clic derecho en el archivo .bat
    echo 3. Selecciona "Ejecutar como administrador"
    echo 4. Cuando Windows pregunte, haz clic en "Si"
    echo.
    echo ⚠️  NO CIERRES ESTA VENTANA SIN HACER LO ANTERIOR
    echo.
    pause
    exit /b 1
)

echo ✅ Permisos de administrador: OK
echo.

REM Obtener la ruta actual del proyecto
set PROJECT_PATH=%~dp0
set PROJECT_PATH=%PROJECT_PATH:~0,-1%

echo 📂 Detectando proyecto en: %PROJECT_PATH%
echo.

REM Verificar que estamos en un proyecto Laravel válido
if not exist "%PROJECT_PATH%\artisan" (
    color 0C
    echo.
    echo ❌❌❌ ERROR: NO ES UN PROYECTO LARAVEL VALIDO ❌❌❌
    echo.
    echo No encontre el archivo 'artisan' en esta carpeta.
    echo.
    echo Asegurate de ejecutar este archivo desde la carpeta
    echo donde esta el proyecto (donde esta el archivo 'artisan').
    echo.
    pause
    exit /b 1
)

echo ✅ Proyecto Laravel encontrado: OK
echo.

REM Verificar que PHP está disponible
echo 🧪 Verificando que PHP este instalado...
php --version >nul 2>&1
if %errorLevel% neq 0 (
    color 0C
    echo.
    echo ❌❌❌ ERROR: PHP NO ESTA INSTALADO O CONFIGURADO ❌❌❌
    echo.
    echo PHP es necesario para que funcionen los backups.
    echo.
    echo Soluciones:
    echo 1. Instalar PHP en tu computadora
    echo 2. Agregar PHP a las variables de entorno
    echo 3. Pedir ayuda en el grupo si no sabes como
    echo.
    pause
    exit /b 1
)

echo ✅ PHP instalado y funcionando: OK
echo.

REM Probar el comando de backup
echo 🧪 Probando si el sistema de backup funciona...
php artisan backup:auto >nul 2>&1
if %errorLevel% neq 0 (
    color 0C
    echo.
    echo ❌❌❌ ERROR: EL SISTEMA DE BACKUP NO FUNCIONA ❌❌❌
    echo.
    echo Posibles causas:
    echo 1. No hiciste 'composer install'
    echo 2. Falta configurar la base de datos
    echo 3. Hay algun error en el proyecto
    echo.
    echo Solucion: Ejecuta 'composer install' y vuelve a intentar
    echo.
    pause
    exit /b 1
)

echo ✅ Sistema de backup funcionando: OK
echo.

REM Crear el script VBS dinámicamente
echo 📄 Creando archivos del sistema...
echo Set objShell = CreateObject("WScript.Shell") > "%TEMP%\vasir_backup_silent.vbs"
echo objShell.Run "powershell.exe -WindowStyle Hidden -ExecutionPolicy Bypass -File ""%PROJECT_PATH%\ejecutar_backup.ps1""", 0, True >> "%TEMP%\vasir_backup_silent.vbs"

REM Copiar el script a C:\ para que sea accesible por el programador de tareas
copy "%TEMP%\vasir_backup_silent.vbs" "C:\vasir_backup_silent.vbs" >nul
if %errorLevel% neq 0 (
    color 0C
    echo.
    echo ❌❌❌ ERROR: NO SE PUDO CREAR ARCHIVO DEL SISTEMA ❌❌❌
    echo.
    echo Esto puede pasar si:
    echo 1. No tienes permisos de administrador
    echo 2. Hay un antivirus bloqueando
    echo.
    echo Solucion: Ejecutar como administrador
    echo.
    pause
    exit /b 1
)

echo ✅ Archivos del sistema creados: OK
echo.

REM Eliminar tarea existente si existe
echo 🗑️ Eliminando configuracion anterior (si existe)...
schtasks /delete /tn "VASIR_Backup_Scheduler" /f >nul 2>&1

REM Crear nueva tarea programada
echo ⚙️ Configurando backup automatico...
schtasks /create /tn "VASIR_Backup_Scheduler" /tr "wscript.exe //B \"C:\vasir_backup_silent.vbs\"" /sc minute /mo 2 /f >nul

if %ERRORLEVEL% EQU 0 (
    color 0A
    echo.
    echo ✅✅✅ CONFIGURACION COMPLETADA EXITOSAMENTE ✅✅✅
    echo.
    echo 🎉 ¡Felicidades! Los backups automaticos ya funcionan
    echo.
    echo ℹ️  Que hace el sistema:
    echo    • Crea backups cada 2 minutos automaticamente
    echo    • Funciona aunque cierres Visual Studio Code
    echo    • No veras ventanas ni notificaciones
    echo    • Los backups se guardan en storage/app/private/VASIR/
    echo.
    echo 💡 Puedes cambiar la frecuencia desde la pagina web:
    echo    /configuracion/backup
    echo.
) else (
    color 0C
    echo.
    echo ❌❌❌ ERROR AL CONFIGURAR BACKUP AUTOMATICO ❌❌❌
    echo.
    echo Codigo de error: %ERRORLEVEL%
    echo.
    echo Solucion: Ejecutar como administrador
    echo.
    pause
    exit /b 1
)

echo 🧪 Verificando que todo este funcionando...

REM Verificar que la tarea se creó
schtasks /query /tn "VASIR_Backup_Scheduler" >nul 2>&1
if %errorLevel% neq 0 (
    color 0C
    echo.
    echo ❌❌❌ ERROR: LA TAREA NO SE CREO CORRECTAMENTE ❌❌❌
    echo.
    pause
    exit /b 1
)

echo ✅ Verificacion final: OK
echo.

REM Mostrar información final
color 0A
echo ====================================================
echo    🎉🎉 TODO FUNCIONA PERFECTAMENTE 🎉🎉
echo ====================================================
echo.
echo 📊 Informacion del sistema:
echo.
schtasks /query /tn "VASIR_Backup_Scheduler"
echo.
echo 📁 Los backups se guardaran automaticamente en:
echo    %PROJECT_PATH%\storage\app\private\VASIR\
echo.
echo 💡 Comandos utiles para el futuro:
echo    • Ver estado: verificar_estado_backup.bat
echo    • Backup manual: php artisan backup:auto  
echo    • Desactivar: schtasks /delete /tn "VASIR_Backup_Scheduler" /f
echo.
echo ⚠️  IMPORTANTE: Esto se hace UNA SOLA VEZ
echo    No necesitas repetir esto cada vez que actualices el proyecto
echo.
echo ====================================================
echo Presiona ENTER para cerrar esta ventana...
pause >nul
