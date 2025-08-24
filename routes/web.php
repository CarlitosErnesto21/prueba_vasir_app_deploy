<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\InformePDFController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\TipoDocumentoController;
use App\Http\Controllers\BackupController;
use App\Http\Controllers\SettingsController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Middleware\RutasAdmin;

Route::get('/', function () {
    return Inertia::render('Inicio');
})->name('inicio');

Route::middleware(['auth', 'verified', RutasAdmin::class])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    Route::get('catalogos/Productos', function () {
        return Inertia::render('catalogos/Productos');
    })->name('productos');

    Route::get('catalogos/Hoteles', function () {
        return Inertia::render('catalogos/Hoteles');
    })->name('hoteles');
    
    Route::get('catalogos/ReservaTours', function () {
        return Inertia::render('catalogos/ReservaTours');
    })->name('reservatours');

    Route::get('catalogos/Tours', function () {
        return Inertia::render('catalogos/Tours');
    })->name('tours');

    Route::get('catalogos/Aerolineas', function () {
        return Inertia::render('catalogos/Aerolineas');
    })->name('aerolineas');

    Route::get('catalogos/ControlCategorias', function () {
        return Inertia::render('catalogos/ControlCategorias');
    })->name('catPTH');

    Route::get('catalogos/ControlPaisesProvincias', function () {
        return Inertia::render('catalogos/ControlPaisesProvincias');
    })->name('controlPaisesProvincias');

    /////////////////////////////////////////////////////////////////
    // Respaldo de Base de Datos (Solo Manual)
    Route::get('/configuracion/backup', [BackupController::class, 'showBackupPage'])->name('backup');
    
    // API routes for backup management - Solo para Administradores
    Route::middleware(['auth'])->group(function () {
        Route::get('/api/backups', [BackupController::class, 'index']);
        Route::post('/api/backups/generate', [BackupController::class, 'generate']);
        Route::get('/api/backups/{id}/download', [BackupController::class, 'download']);
        Route::delete('/api/backups/{id}', [BackupController::class, 'delete']);
        Route::post('/api/backups/cleanup', [BackupController::class, 'cleanup']);
    });
    
    // Asignación de Roles - Solo para Administradores
    Route::get('/configuracion/roles', [RoleController::class, 'index'])->name('roles')->middleware('role:admin');
    
    // API routes for role management - Solo para Administradores
    Route::post('/roles/users/{user}/update-roles', [RoleController::class, 'updateUserRoles'])->middleware('role:admin');
    Route::post('/roles/users/{user}/assign-role', [RoleController::class, 'assignRole'])->middleware('role:admin');
    Route::post('/roles/users/{user}/remove-role', [RoleController::class, 'removeRole'])->middleware('role:admin');
    Route::get('/roles/users/{user}/permissions', [RoleController::class, 'getUserPermissions'])->middleware('role:admin');
    Route::post('/roles/users/{user}/assign-permission', [RoleController::class, 'assignPermission'])->middleware('role:admin');
    Route::post('/roles/users/{user}/remove-permission', [RoleController::class, 'removePermission'])->middleware('role:admin');
    Route::put('/roles/users/{user}/sync-permissions', [RoleController::class, 'syncPermissions'])->middleware('role:admin');
    Route::post('/roles/create-user', [RoleController::class, 'createInternalUser'])->middleware('role:admin');
    Route::get('/roles/users/{user}/employee', [RoleController::class, 'getEmployeeData'])->middleware('role:admin');
    Route::put('/roles/users/{user}/employee', [RoleController::class, 'updateEmployeeData'])->middleware('role:admin');
    
    // Configuración del Sistema - Solo para Administradores
    Route::get('/configuracion/settings', [SettingsController::class, 'index'])->name('settings')->middleware('role:admin');
    Route::post('/configuracion/settings', [SettingsController::class, 'update'])->name('settings.update')->middleware('role:admin');
    
    // Gestión de Clientes
    Route::get('/configuracion/clientes', [ClienteController::class, 'index'])->name('clientes');
    
    // API routes for client management
    Route::get('/api/clientes', [ClienteController::class, 'getClientes']);
    Route::post('/api/clientes', [ClienteController::class, 'store']);
    Route::put('/api/clientes/{cliente}', [ClienteController::class, 'update']);
    Route::patch('/api/clientes/{cliente}/toggle-status', [ClienteController::class, 'toggleStatus']);
    Route::delete('/api/clientes/{cliente}', [ClienteController::class, 'destroy']);
    Route::get('/api/tipos-documento-options', [ClienteController::class, 'getTiposDocumento']);

    // Gestión de Tipos de Documento
    Route::get('/configuracion/tipos-documento', [TipoDocumentoController::class, 'index'])->name('tipos-documento');
    
    // API routes for document types management
    Route::get('/api/tipos-documento', [TipoDocumentoController::class, 'index']);
    Route::post('/api/tipos-documento', [TipoDocumentoController::class, 'store']);
    Route::put('/api/tipos-documento/{tipoDocumento}', [TipoDocumentoController::class, 'update']);
    Route::delete('/api/tipos-documento/{tipoDocumento}', [TipoDocumentoController::class, 'destroy']);
    /////////////////////////////////////////////////////////////////

    //Ruta para los informes de la aplicacion
    Route::get('/informes', function () {
        return Inertia::render('informes/Informes');
    })->middleware(['auth', 'verified'])->name('informes');

    Route::get('/descargar-informe', [InformePDFController::class, 'descargarInforme']);
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Rutas para vistas de clientes //***NO MODIFICAR ESTAS RUTAS***//
Route::get('/paquetes', function () {
    return Inertia::render('vistasClientes/Paquetes');
})->name('paquetes');

Route::get('/reservaciones', function () {
    return Inertia::render('vistasClientes/Reservaciones');
})->name('reservaciones');

Route::get('/tours-nacionales', [App\Http\Controllers\TourController::class, 'toursNacionales'])->name('tours-nacionales');

Route::get('/tours-internacionales', [App\Http\Controllers\TourController::class, 'toursInternacionales'])->name('tours-internacionales');

// Rutas para vista detallada de tours
Route::get('/tours-nacionales/{id}', [App\Http\Controllers\TourController::class, 'mostrarTourNacional'])->name('tour-nacional.show');
Route::get('/tours-internacionales/{id}', [App\Http\Controllers\TourController::class, 'mostrarTourInternacional'])->name('tour-internacional.show');

// API para obtener información detallada de un tour
Route::get('/api/tours-clientes/{id}', [App\Http\Controllers\TourController::class, 'show'])->name('tours-clientes.show');

Route::get('/tienda', function () {
    return Inertia::render('vistasClientes/Tienda');
})->name('tienda');

Route::get('/sobre-nosotros', [App\Http\Controllers\SobreNosotrosController::class, 'index'])->name('sobre-nosotros');

Route::get('/contactos', function () {
    return Inertia::render('vistasClientes/Contactos');
})->name('contactos');

require __DIR__.'/auth.php';