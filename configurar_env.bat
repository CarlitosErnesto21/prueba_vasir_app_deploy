@echo off
setlocal enabledelayedexpansion

REM Script para configurar rapidamente el archivo .env
REM Para resolver problemas comunes de backup

color 0B
title Configurar .env para Backups
echo.
echo ========================================
echo    CONFIGURAR .ENV PARA BACKUPS
echo ========================================
echo.
echo Este script configura el archivo .env
echo para que los backups funcionen correctamente.
echo.
echo Presiona ENTER para continuar...
pause >nul
cls

echo.
echo 📄 Verificando archivo .env...
echo.

if not exist ".env" (
    color 0C
    echo ❌ NO EXISTE ARCHIVO .env
    echo.
    if exist ".env.example" (
        echo 🔄 Creando .env desde .env.example...
        copy ".env.example" ".env" >nul
        echo ✅ Archivo .env creado
    ) else (
        echo ❌ Tampoco existe .env.example
        echo.
        echo NECESITAS CREAR EL ARCHIVO .env MANUALMENTE
        echo Pide ayuda en el grupo
        goto :final
    )
) else (
    echo ✅ Archivo .env existe
)

echo.
echo 🔧 Configurando backup password...
echo.

REM Verificar si ya tiene password configurado
findstr "BACKUP_ARCHIVE_PASSWORD=" .env | findstr -v "BACKUP_ARCHIVE_PASSWORD=$" | findstr -v "BACKUP_ARCHIVE_PASSWORD= " >nul
if %errorLevel% equ 0 (
    echo ✅ Password de backup ya está configurado
) else (
    echo 🔄 Configurando password de backup...
    
    REM Buscar la línea y reemplazarla
    powershell -Command "(Get-Content .env) -replace 'BACKUP_ARCHIVE_PASSWORD=.*', 'BACKUP_ARCHIVE_PASSWORD=vasir2024backup' | Set-Content .env"
    
    if %errorLevel% equ 0 (
        echo ✅ Password de backup configurado
    ) else (
        echo ⚠️ Error configurando password, pero continuamos...
    )
)

echo.
echo 🔧 Verificando configuración de base de datos...
echo.

findstr "DB_DATABASE=" .env | findstr -v "DB_DATABASE=$" | findstr -v "DB_DATABASE= " >nul
if %errorLevel% neq 0 (
    echo ⚠️ Base de datos no está configurada
    echo.
    echo CONFIGURANDO BASE DE DATOS BASICA:
    powershell -Command "(Get-Content .env) -replace 'DB_DATABASE=.*', 'DB_DATABASE=vasir_app' | Set-Content .env"
    echo ✅ Configurado DB_DATABASE=vasir_app
) else (
    echo ✅ Base de datos configurada
)

echo.
echo 🔧 Generando clave de aplicación...
echo.

php artisan key:generate --force >nul 2>&1
if %errorLevel% equ 0 (
    echo ✅ Clave de aplicación generada
) else (
    echo ⚠️ Error generando clave (pero continuamos...)
)

echo.
echo 🧪 Probando configuración...
echo.

php artisan config:cache >nul 2>&1
php artisan backup:auto >nul 2>&1
if %errorLevel% equ 0 (
    color 0A
    echo.
    echo ✅✅✅ CONFIGURACION COMPLETADA ✅✅✅
    echo.
    echo 🎉 El backup debería funcionar ahora
    echo.
    echo SIGUIENTE PASO:
    echo 1. Ejecutar: configurar_backup_automatico.bat
    echo 2. Como administrador
    echo.
) else (
    color 0E
    echo.
    echo ⚠️ AÚN HAY PROBLEMAS CON EL BACKUP
    echo.
    echo POSIBLES CAUSAS:
    echo 1. Base de datos no existe
    echo 2. Faltan dependencias (ejecutar preparar_proyecto.bat)
    echo 3. Problemas de permisos
    echo.
    echo SIGUIENTE PASO:
    echo 1. Ejecutar: preparar_proyecto.bat
    echo 2. Después volver a ejecutar este script
    echo.
)

:final
echo.
echo Presiona ENTER para cerrar...
pause >nul
