@echo off
setlocal enabledelayedexpansion

REM Script simplificado sin caracteres especiales para preparar proyecto
REM Version compatible con diferentes codificaciones

color 0B
title Preparar Proyecto VASIR - Version Simplificada
echo.
echo ===============================================
echo    PREPARAR PROYECTO PARA BACKUPS
echo ===============================================
echo.
echo Este script va a instalar dependencias
echo.
echo Presiona ENTER para comenzar...
pause >nul
cls

echo.
echo Verificando proyecto...
echo Ruta actual: %~dp0
echo.

REM Verificacion simple del archivo artisan
dir artisan >nul 2>&1
if %errorLevel% neq 0 (
    color 0C
    echo ERROR: No se encuentra el archivo artisan
    echo.
    echo Archivos en esta carpeta:
    dir *.* | findstr /v "bytes free"
    echo.
    echo SOLUCION:
    echo 1. Ejecutar desde la carpeta del proyecto
    echo 2. Verificar que artisan existe
    echo.
    echo Presiona ENTER para cerrar...
    pause >nul
    goto :final
)

echo OK - Archivo artisan encontrado
echo.
echo Presiona ENTER para continuar...
pause >nul

echo.
echo Verificando Composer...

composer --version >nul 2>&1
if %errorLevel% neq 0 (
    color 0C
    echo ERROR: Composer no esta instalado
    echo.
    echo Instalar desde: https://getcomposer.org
    echo.
    echo Presiona ENTER para cerrar...
    pause >nul
    goto :final
)

echo OK - Composer encontrado
composer --version | findstr "Composer"
echo.
echo Presiona ENTER para instalar dependencias...
pause >nul

echo.
echo Instalando dependencias...
echo (Esto puede tardar varios minutos)
echo.

composer install --no-interaction
if %errorLevel% neq 0 (
    color 0C
    echo.
    echo ERROR en composer install
    echo.
    echo Presiona ENTER para cerrar...
    pause >nul
    goto :final
)

echo.
echo OK - Dependencias instaladas
echo.
echo Presiona ENTER para continuar...
pause >nul

echo.
echo Verificando archivo .env...

if not exist ".env" (
    if exist ".env.example" (
        echo Creando .env desde .env.example...
        copy ".env.example" ".env" >nul
        echo OK - Archivo .env creado
    ) else (
        echo ERROR: No existe .env ni .env.example
        echo Necesitas ayuda manual
        echo.
        echo Presiona ENTER para cerrar...
        pause >nul
        goto :final
    )
) else (
    echo OK - Archivo .env existe
)

echo.
echo Presiona ENTER para generar clave...
pause >nul

echo.
echo Generando clave de aplicacion...

php artisan key:generate --force
if %errorLevel% neq 0 (
    echo ERROR generando clave
    echo.
    echo Presiona ENTER para cerrar...
    pause >nul
    goto :final
)

echo OK - Clave generada
echo.
echo Presiona ENTER para migrar base de datos...
pause >nul

echo.
echo Ejecutando migraciones...

php artisan migrate --force
if %errorLevel% neq 0 (
    color 0E
    echo.
    echo ERROR en migraciones
    echo Posibles causas:
    echo 1. Base de datos no configurada en .env
    echo 2. Base de datos no existe
    echo.
    echo Configura la base de datos y vuelve a ejecutar
    echo.
    echo Presiona ENTER para cerrar...
    pause >nul
    goto :final
)

echo OK - Migraciones completadas
echo.
echo Presiona ENTER para probar backup...
pause >nul

echo.
echo Probando sistema de backup...

php artisan backup:auto >nul 2>&1
if %errorLevel% neq 0 (
    color 0C
    echo.
    echo ERROR: Backup aun no funciona
    echo.
    echo Mostrando error exacto:
    php artisan backup:auto
    echo.
    echo Presiona ENTER para cerrar...
    pause >nul
    goto :final
)

color 0A
echo.
echo ===============================================
echo    PROYECTO PREPARADO CORRECTAMENTE
echo ===============================================
echo.
echo El sistema de backup ya funciona
echo.
echo SIGUIENTE PASO:
echo 1. Ejecutar: configurar_backup_automatico.bat
echo 2. Como administrador
echo.

:final
echo.
echo Presiona ENTER para cerrar...
pause >nul
