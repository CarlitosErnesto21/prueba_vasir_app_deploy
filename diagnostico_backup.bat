@echo off
setlocal enabledelayedexpansion

REM Script de diagnóstico para problemas con backups
REM Este script ayuda a identificar qué está fallando

color 0B
title Diagnostico de Backup VASIR
echo.
echo ========================================
echo    DIAGNOSTICO DE BACKUP VASIR
echo ========================================
echo.
echo Este script te ayudara a encontrar
echo que esta causando problemas.
echo.
echo Presiona ENTER para empezar...
pause >nul
cls

echo.
echo 🔍 PASO 1: Verificando permisos...
echo.

net session >nul 2>&1
if %errorLevel% neq 0 (
    color 0C
    echo ❌ NO TIENES PERMISOS DE ADMINISTRADOR
    echo.
    echo SOLUCION:
    echo 1. Clic derecho en este archivo
    echo 2. "Ejecutar como administrador"
    echo 3. Clic "Si" cuando Windows pregunte
    echo.
    goto :final
) else (
    echo ✅ Permisos de administrador: OK
)

echo.
echo 🔍 PASO 2: Verificando ubicación del proyecto...
echo.

set PROJECT_PATH=%~dp0
set PROJECT_PATH=%PROJECT_PATH:~0,-1%
echo Ruta detectada: %PROJECT_PATH%

if not exist "%PROJECT_PATH%\artisan" (
    color 0C
    echo ❌ NO ES UN PROYECTO LARAVEL VALIDO
    echo.
    echo PROBLEMA: No encontré el archivo 'artisan'
    echo Ruta actual: %PROJECT_PATH%
    echo.
    echo ❌ Confirmo: NO hay archivo 'artisan' en esta ubicación
    echo.
    echo 🔍 Vamos a buscar dónde está el proyecto...
    echo.
    dir /s artisan 2>nul | findstr "artisan"
    echo.
    echo SOLUCION PASO A PASO:
    echo 1. Busca la carpeta que contiene el archivo 'artisan'
    echo 2. Copia ESTOS ARCHIVOS a esa carpeta:
    echo    - diagnostico_backup.bat
    echo    - configurar_backup_automatico.bat
    echo 3. Ejecuta desde AHI, no desde aquí
    echo.
    echo O mejor aún:
    echo 1. Abre la carpeta del proyecto en VS Code
    echo 2. Abre terminal en VS Code (Ctrl + `)
    echo 3. Ejecuta: ..\diagnostico_backup.bat
    echo.
    goto :final
) else (
    echo ✅ Proyecto Laravel: OK
)

echo.
echo 🔍 PASO 3: Verificando PHP...
echo.

php --version >nul 2>&1
if %errorLevel% neq 0 (
    color 0C
    echo ❌ PHP NO ESTA INSTALADO O CONFIGURADO
    echo.
    echo SOLUCION:
    echo 1. Instalar PHP desde php.net
    echo 2. O usar XAMPP/WAMP/LARAGON
    echo 3. Agregar PHP al PATH del sistema
    echo.
    goto :final
) else (
    echo ✅ PHP instalado: OK
    php --version | findstr "PHP"
)

echo.
echo 🔍 PASO 4: Verificando dependencias...
echo.

if not exist "%PROJECT_PATH%\vendor" (
    color 0E
    echo ⚠️ FALTA INSTALAR DEPENDENCIAS
    echo.
    echo SOLUCION:
    echo 1. Ejecutar: preparar_proyecto.bat (recomendado)
    echo 2. O manualmente: composer install
    echo 3. Esperar a que termine
    echo.
    goto :final
) else (
    echo ✅ Dependencias instaladas: OK
)

echo.
echo 🔍 PASO 5: Probando comando de backup...
echo.

php artisan backup:auto >nul 2>&1
if %errorLevel% neq 0 (
    color 0C
    echo ❌ EL COMANDO DE BACKUP FALLA
    echo.
    echo PROBLEMA: Hay un error en el sistema
    echo.
    echo 🚀 SOLUCION AUTOMATICA:
    echo 1. Ejecutar: preparar_proyecto.bat
    echo 2. Ese script arreglara todo automaticamente
    echo 3. Despues volver a ejecutar configurar_backup_automatico.bat
    echo.
    echo 📋 SOLUCION MANUAL:
    echo 1. Ejecutar: composer install
    echo 2. Configurar base de datos en .env
    echo 3. Ejecutar: php artisan migrate
    echo 4. Pedir ayuda con el error que aparece abajo
    echo.
    echo Vamos a ver el error exacto:
    echo.
    php artisan backup:auto
    echo.
    echo SOLUCION:
    echo 1. Configurar base de datos en .env
    echo 2. Ejecutar: php artisan migrate
    echo 3. Pedir ayuda con el error que aparece arriba
    echo.
    goto :final
) else (
    echo ✅ Comando de backup: OK
)

echo.
echo 🔍 PASO 6: Verificando tarea programada...
echo.

schtasks /query /tn "VASIR_Backup_Scheduler" >nul 2>&1
if %errorLevel% neq 0 (
    color 0E
    echo ⚠️ LA TAREA PROGRAMADA NO EXISTE
    echo.
    echo SOLUCION:
    echo 1. Ejecutar: configurar_backup_automatico.bat
    echo 2. Como administrador
    echo.
) else (
    echo ✅ Tarea programada: OK
    echo.
    echo Información de la tarea:
    schtasks /query /tn "VASIR_Backup_Scheduler" /fo table
)

echo.
color 0A
echo ========================================
echo    ✅ DIAGNOSTICO COMPLETADO
echo ========================================
echo.
echo Si todo sale OK arriba, los backups
echo deberian funcionar correctamente.
echo.
echo Si hay errores rojos (❌), esas son
echo las cosas que necesitas arreglar.
echo.

:final
echo.
echo Presiona ENTER para cerrar...
pause >nul
