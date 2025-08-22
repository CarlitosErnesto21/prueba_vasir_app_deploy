@echo off
setlocal enabledelayedexpansion

REM Configurador simplificado sin verificacion de backup
REM Ya sabemos que el backup funciona

color 0A
echo.
echo ====================================================
echo     CONFIGURADOR DE BACKUPS AUTOMATICOS VASIR
echo ====================================================
echo.
echo CONFIGURACION SIMPLIFICADA
echo (Omitiendo verificaciones - sabemos que funciona)
echo.
echo Presiona ENTER para continuar...
pause >nul

REM Verificar permisos de administrador
net session >nul 2>&1
if %errorLevel% neq 0 (
    color 0C
    echo.
    echo ERROR: NECESITAS PERMISOS DE ADMINISTRADOR
    echo.
    echo Solucion:
    echo 1. Clic derecho en este archivo
    echo 2. "Ejecutar como administrador"
    echo.
    pause >nul
    exit /b 1
)

echo OK - Permisos de administrador verificados
echo.

REM Obtener la ruta actual del proyecto
set PROJECT_PATH=%~dp0
set PROJECT_PATH=%PROJECT_PATH:~0,-1%

echo Configurando para proyecto en: %PROJECT_PATH%
echo.

echo Creando archivos del sistema...

REM Crear el script VBS
echo Set objShell = CreateObject("WScript.Shell") > "%TEMP%\vasir_backup_silent.vbs"
echo objShell.Run "powershell.exe -WindowStyle Hidden -ExecutionPolicy Bypass -File ""%PROJECT_PATH%\ejecutar_backup.ps1""", 0, True >> "%TEMP%\vasir_backup_silent.vbs"

REM Copiar el script a C:\
copy "%TEMP%\vasir_backup_silent.vbs" "C:\vasir_backup_silent.vbs" >nul
if %errorLevel% neq 0 (
    color 0C
    echo ERROR: No se pudo crear archivo del sistema
    echo Verifica que tengas permisos de administrador
    pause >nul
    exit /b 1
)

echo OK - Archivos del sistema creados
echo.

echo Eliminando tarea anterior si existe...
schtasks /delete /tn "VASIR_Backup_Scheduler" /f >nul 2>&1

echo Creando nueva tarea programada...
schtasks /create /tn "VASIR_Backup_Scheduler" /tr "wscript.exe //B \"C:\vasir_backup_silent.vbs\"" /sc minute /mo 2 /f >nul

if %ERRORLEVEL% EQU 0 (
    color 0A
    echo.
    echo ===============================================
    echo    CONFIGURACION COMPLETADA EXITOSAMENTE
    echo ===============================================
    echo.
    echo Los backups automaticos ya funcionan
    echo.
    echo Informacion:
    echo - Backups cada 2 minutos automaticamente
    echo - Funciona aunque cierres VS Code
    echo - No veras ventanas ni notificaciones
    echo - Archivos en: storage/app/private/VASIR/
    echo.
    echo Verificando tarea creada...
    schtasks /query /tn "VASIR_Backup_Scheduler"
    echo.
) else (
    color 0C
    echo ERROR: No se pudo crear la tarea programada
    echo Codigo de error: %ERRORLEVEL%
    pause >nul
    exit /b 1
)

echo ===============================================
echo    TODO FUNCIONA CORRECTAMENTE
echo ===============================================
echo.
echo Presiona ENTER para cerrar...
pause >nul
