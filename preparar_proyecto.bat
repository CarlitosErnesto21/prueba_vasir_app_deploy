@echo off
setlocal enabledelayedexpansion

REM Script para preparar el proyecto antes de configurar backups
REM Ejecutar cuando el diagnostico dice que el backup no funciona

color 0B
title Preparar Proyecto VASIR
echo.
echo ===============================================
echo    PREPARAR PROYECTO PARA BACKUPS
echo ===============================================
echo.
echo Este script va a instalar dependencias y
echo configurar el proyecto para que funcionen
echo los backups.
echo.
echo ⚠️  IMPORTANTE: Presiona ENTER en cada paso
echo    para poder ver si hay errores
echo.
echo Presiona ENTER para comenzar...
pause >nul
cls

echo.
echo 🔍 Verificando ubicación del proyecto...
echo Ruta actual: %~dp0
echo.

if not exist "artisan" (
    color 0C
    echo ❌ ERROR: NO ESTAS EN LA CARPETA CORRECTA
    echo.
    echo Este script debe ejecutarse desde la carpeta
    echo que contiene el archivo 'artisan'
    echo.
    echo SOLUCION:
    echo 1. Buscar la carpeta que tiene 'artisan'
    echo 2. Copiar este archivo ahí
    echo 3. Ejecutar desde esa ubicación
    echo.
    echo ⚠️  Presiona ENTER para cerrar...
    pause >nul
    goto :final
)

echo ✅ Archivo artisan encontrado - ubicación correcta
echo    Presiona ENTER para continuar...
pause >nul

echo.
echo 📦 PASO 1: Instalando dependencias...
echo    (Esto puede tardar varios minutos)
echo.

REM Verificar si composer existe
echo 🔍 Verificando Composer...
composer --version >nul 2>&1
if %errorLevel% neq 0 (
    color 0C
    echo ❌ COMPOSER NO ESTA INSTALADO
    echo.
    echo NECESITAS INSTALAR COMPOSER:
    echo 1. Ve a: https://getcomposer.org/download/
    echo 2. Descarga e instala Composer
    echo 3. Reinicia la computadora
    echo 4. Vuelve a ejecutar este script
    echo.
    echo ⚠️  Presiona ENTER para cerrar...
    pause >nul
    goto :final
)

echo ✅ Composer encontrado
composer --version | findstr "Composer"
echo.
echo    Presiona ENTER para continuar con la instalación...
pause >nul

REM Ejecutar composer install
echo 🔄 Ejecutando composer install...
echo    (Esto puede tardar varios minutos - ten paciencia)
echo.
composer install
if %errorLevel% neq 0 (
    color 0C
    echo.
    echo ❌ ERROR EN COMPOSER INSTALL
    echo.
    echo POSIBLES SOLUCIONES:
    echo 1. Verificar conexión a internet
    echo 2. Ejecutar: composer update
    echo 3. Pedir ayuda en el grupo
    echo.
    echo ⚠️  Presiona ENTER para cerrar...
    pause >nul
    goto :final
)

echo ✅ Dependencias instaladas correctamente
echo.
echo    ✨ ¡Excelente! Composer install completado
echo    Presiona ENTER para continuar...
pause >nul

echo 📄 PASO 2: Verificando archivo .env...
echo.

if not exist ".env" (
    if exist ".env.example" (
        echo 🔄 Creando archivo .env desde .env.example...
        copy ".env.example" ".env" >nul
        echo ✅ Archivo .env creado
    ) else (
        color 0E
        echo ⚠️ NO EXISTE .env NI .env.example
        echo.
        echo NECESITAS:
        echo 1. Crear archivo .env
        echo 2. Configurar base de datos
        echo 3. Pedir ayuda en el grupo
        echo.
        echo ⚠️  Presiona ENTER para cerrar...
        pause >nul
        goto :final
    )
) else (
    echo ✅ Archivo .env existe
)

echo.
echo    Presiona ENTER para continuar...
pause >nul

echo.
echo 🔑 PASO 3: Generando clave de aplicación...
echo.

php artisan key:generate --force
if %errorLevel% neq 0 (
    color 0C
    echo ❌ ERROR GENERANDO CLAVE
    echo.
    echo ⚠️  Presiona ENTER para cerrar...
    pause >nul
    goto :final
)

echo ✅ Clave generada correctamente
echo.
echo    Presiona ENTER para continuar con migraciones...
pause >nul

echo 🗃️ PASO 4: Ejecutando migraciones...
echo.

php artisan migrate --force
if %errorLevel% neq 0 (
    color 0E
    echo ⚠️ ERROR EN MIGRACIONES
    echo.
    echo POSIBLES CAUSAS:
    echo 1. Base de datos no configurada en .env
    echo 2. Base de datos no existe
    echo 3. Problemas de conexión
    echo.
    echo QUE HACER:
    echo 1. Verificar configuración en .env
    echo 2. Crear la base de datos
    echo 3. Volver a ejecutar este script
    echo.
    echo ⚠️  Presiona ENTER para cerrar...
    pause >nul
    goto :final
)

echo ✅ Migraciones ejecutadas correctamente
echo.
echo    ✨ ¡Base de datos configurada!
echo    Presiona ENTER para probar el backup...
pause >nul

echo 🧪 PASO 5: Probando backup nuevamente...
echo.

php artisan backup:auto >nul 2>&1
if %errorLevel% neq 0 (
    color 0C
    echo ❌ BACKUP AÚN NO FUNCIONA
    echo.
    echo NECESITAS AYUDA MANUAL:
    echo 1. Tomar captura de este error
    echo 2. Enviar al grupo
    echo 3. Mencionar que ya ejecutaste este script
    echo.
    echo Vamos a ver el error exacto:
    echo.
    php artisan backup:auto
    goto :final
)

color 0A
echo.
echo ✅✅✅ ¡PROYECTO PREPARADO CORRECTAMENTE! ✅✅✅
echo.
echo 🎉 El sistema de backup ya funciona
echo.
echo SIGUIENTE PASO:
echo 1. Ejecutar: configurar_backup_automatico.bat
echo 2. Como administrador
echo 3. Seguir las instrucciones
echo.

:final
echo.
echo Presiona ENTER para cerrar...
pause >nul
