@echo off
setlocal enabledelayedexpansion

REM Script simple para detectar por qué preparar_proyecto.bat se cierra
REM Ejecutar cuando preparar_proyecto.bat se cierre automáticamente

color 0E
title Diagnóstico Rápido - Por qué se cierra el script
echo.
echo ====================================================
echo    ¿POR QUE SE CIERRA PREPARAR_PROYECTO.BAT?
echo ====================================================
echo.
echo Este script te dirá exactamente qué está fallando
echo.
echo Presiona ENTER para empezar...
pause >nul
cls

echo.
echo 🔍 VERIFICACION 1: ¿Tienes permisos de administrador?
echo.

net session >nul 2>&1
if %errorLevel% neq 0 (
    color 0C
    echo ❌ NO TIENES PERMISOS DE ADMINISTRADOR
    echo.
    echo ➡️ ESTA ES LA CAUSA MAS PROBABLE
    echo.
    echo SOLUCION:
    echo 1. Clic derecho en preparar_proyecto.bat
    echo 2. "Ejecutar como administrador"
    echo 3. Clic "Si" cuando Windows pregunte
    echo.
    goto :mostrar_final
) else (
    echo ✅ Permisos de administrador: OK
)

echo.
echo 🔍 VERIFICACION 2: ¿Estás en la carpeta correcta?
echo.
echo Ruta actual: %~dp0

if not exist "artisan" (
    color 0C
    echo ❌ NO ESTAS EN LA CARPETA CORRECTA
    echo.
    echo ➡️ ESTA ES LA CAUSA DEL PROBLEMA
    echo.
    echo SOLUCION:
    echo 1. Buscar la carpeta que contiene 'artisan'
    echo 2. Copiar preparar_proyecto.bat a esa carpeta
    echo 3. Ejecutar desde ahí
    echo.
    goto :mostrar_final
) else (
    echo ✅ Archivo artisan encontrado: OK
)

echo.
echo 🔍 VERIFICACION 3: ¿Está instalado Composer?
echo.

composer --version >nul 2>&1
if %errorLevel% neq 0 (
    color 0C
    echo ❌ COMPOSER NO ESTA INSTALADO
    echo.
    echo ➡️ ESTA ES LA CAUSA DEL PROBLEMA
    echo.
    echo SOLUCION:
    echo 1. Instalar Composer desde getcomposer.org
    echo 2. Reiniciar la computadora
    echo 3. Volver a ejecutar preparar_proyecto.bat
    echo.
    goto :mostrar_final
) else (
    echo ✅ Composer instalado: OK
    composer --version | findstr "Composer"
)

echo.
echo 🔍 VERIFICACION 4: ¿Está instalado PHP?
echo.

php --version >nul 2>&1
if %errorLevel% neq 0 (
    color 0C
    echo ❌ PHP NO ESTA INSTALADO
    echo.
    echo ➡️ ESTA ES LA CAUSA DEL PROBLEMA
    echo.
    echo SOLUCION:
    echo 1. Instalar PHP o XAMPP/LARAGON
    echo 2. Agregar PHP al PATH del sistema
    echo 3. Reiniciar la computadora
    echo.
    goto :mostrar_final
) else (
    echo ✅ PHP instalado: OK
    php --version | findstr "PHP"
)

echo.
echo 🔍 VERIFICACION 5: ¿Existe archivo .env?
echo.

if not exist ".env" (
    color 0E
    echo ⚠️ NO EXISTE ARCHIVO .env
    echo.
    echo ➡️ ESTO PUEDE CAUSAR PROBLEMAS
    echo.
    if exist ".env.example" (
        echo SOLUCION:
        echo 1. El script debería crear .env automáticamente
        echo 2. Si no lo hace, ejecutar: copy .env.example .env
    ) else (
        echo PROBLEMA: Tampoco existe .env.example
        echo NECESITAS AYUDA DEL GRUPO
    )
) else (
    echo ✅ Archivo .env existe: OK
)

color 0A
echo.
echo ====================================================
echo    ✅ DIAGNOSTICO COMPLETADO
echo ====================================================
echo.
echo Si todo aparece con ✅ arriba, entonces el problema
echo es que el script necesita más tiempo o hay un error
echo interno que requiere ayuda manual.
echo.

:mostrar_final
echo.
echo 💡 RECOMENDACIONES:
echo.
echo 1. Si hay ❌ rojos arriba, arregla esos problemas primero
echo 2. Si todo está ✅, ejecuta preparar_proyecto.bat nuevamente
echo 3. Si se sigue cerrando, toma captura y pide ayuda
echo.
echo Presiona ENTER para cerrar...
pause >nul
