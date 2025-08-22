# Script de backup automático para VASIR
# Detecta automáticamente la ruta del proyecto

# Obtener la ruta del script actual
$ScriptPath = Split-Path -Parent $MyInvocation.MyCommand.Definition

# Cambiar al directorio del proyecto
Set-Location $ScriptPath

# Verificar que estamos en un proyecto Laravel válido
if (-not (Test-Path "artisan")) {
    $timestamp = Get-Date -Format "dd/MM/yyyy HH:mm:ss"
    Add-Content -Path "backup_error.log" -Value "ERROR [$timestamp]: No se encontró el archivo artisan en: $ScriptPath"
    exit 1
}

# Ejecutar backup en silencio
try {
    $output = & php artisan backup:auto 2>&1
    # Solo guardar en log si hay errores
    if ($LASTEXITCODE -ne 0) {
        $timestamp = Get-Date -Format "dd/MM/yyyy HH:mm:ss"
        Add-Content -Path "backup_error.log" -Value "ERROR [$timestamp]: Backup falló con código $LASTEXITCODE"
        Add-Content -Path "backup_error.log" -Value $output
        Add-Content -Path "backup_error.log" -Value ""
    }
} catch {
    $timestamp = Get-Date -Format "dd/MM/yyyy HH:mm:ss"
    Add-Content -Path "backup_error.log" -Value "EXCEPCION [$timestamp]: $($_.Exception.Message)"
    Add-Content -Path "backup_error.log" -Value ""
}
