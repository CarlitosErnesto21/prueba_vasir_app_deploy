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
echo 🔍 Iniciando diagnostico del sistema...
echo    Presiona ENTER para continuar o Ctrl+C para cancelar
pause >nul
echo.

REM Verificar permisos de administrador
echo 🔐 Verificando permisos de administrador...
net session >nul 2>&1
if %errorLevel% neq 0 (
    color 0C
    echo.
    echo ❌❌❌ ERROR: NECESITAS PERMISOS DE ADMINISTRADOR ❌❌❌
    echo.
    echo Para solucionarlo:
    echo 1. Cierra esta ventana
    echo 2. Clic derecho en el archivo configurar_backup_automatico.bat
    echo 3. Selecciona "Ejecutar como administrador"
    echo 4. Cuando Windows pregunte, haz clic en "Si"
    echo.
    echo ⚠️  Presiona ENTER para cerrar...
    echo.
    pause >nul
    exit /b 1
)

echo ✅ Permisos de administrador: OK
echo.

REM Obtener la ruta actual del proyecto
set PROJECT_PATH=%~dp0
set PROJECT_PATH=%PROJECT_PATH:~0,-1%

echo 📂 Detectando proyecto en: %PROJECT_PATH%
echo    Presiona ENTER para continuar...
pause >nul
echo.

REM Verificar que estamos en un proyecto Laravel válido
echo 🔍 Buscando archivo 'artisan' en el proyecto...
if not exist "%PROJECT_PATH%\artisan" (
    color 0C
    echo.
    echo ❌❌❌ ERROR: NO ES UN PROYECTO LARAVEL VALIDO ❌❌❌
    echo.
    echo No encontre el archivo 'artisan' en esta carpeta:
    echo %PROJECT_PATH%
    echo.
    echo ¿Que hacer?
    echo 1. Asegurate de ejecutar desde la carpeta del proyecto
    echo 2. Debe estar junto al archivo 'artisan'
    echo 3. Si no ves el archivo 'artisan', pregunta en el grupo
    echo.
    echo ⚠️  Presiona ENTER para cerrar...
    pause >nul
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
    echo ¿Que hacer?
    echo 1. Instalar PHP desde php.net
    echo 2. Agregar PHP a las variables de entorno PATH
    echo 3. Reiniciar la computadora después de instalar
    echo 4. Si tienes XAMPP, usar el PHP de ahí
    echo 5. Pedir ayuda en el grupo si no sabes como
    echo.
    echo ⚠️  Presiona ENTER para cerrar...
    pause >nul
    exit /b 1
)

echo ✅ PHP instalado y funcionando: OK
php --version | findstr "PHP"
echo.

REM Probar el comando de backup
echo 🧪 Probando si el sistema de backup funciona...
echo    (Esto puede tardar unos segundos...)
php artisan backup:auto >nul 2>&1
if %errorLevel% neq 0 (
    color 0C
    echo.
    echo ❌❌❌ ERROR: EL SISTEMA DE BACKUP NO FUNCIONA ❌❌❌
    echo.
    echo ¿Que hacer?
    echo 1. Ejecutar 'composer install' en la terminal
    echo 2. Configurar correctamente la base de datos (.env)
    echo 3. Ejecutar 'php artisan migrate' 
    echo 4. Verificar que no hay errores en el proyecto
    echo 5. Pedir ayuda en el grupo
    echo.
    echo ⚠️  Presiona ENTER para cerrar...
    pause >nul
    exit /b 1
)

echo ✅ Sistema de backup funcionando: OK
echo.
echo    ✨ ¡Excelente! El backup funciona correctamente
echo    Presiona ENTER para continuar con la configuracion automatica...
pause >nul
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
    echo ⚠️  Presiona ENTER para cerrar...
    pause >nul
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
    echo ⚠️  Presiona ENTER para cerrar...
    pause >nul
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
    echo ⚠️  Presiona ENTER para cerrar...
    pause >nul
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
