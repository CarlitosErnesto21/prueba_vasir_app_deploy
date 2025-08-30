<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\InformePDFController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\BackupController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\TourController;
use App\Http\Controllers\SobreNosotrosController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Middleware\RutasAdmin;

Route::get('/', function () {
    return Inertia::render('Inicio');
})->name('inicio');

Route::middleware(['auth', 'verified', RutasAdmin::class])->group(function () {
    Route::get('dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    Route::get('transportes', function () {
        return Inertia::render('catalogos/Transportes');
    })->name('transportes');

    Route::get('productos', function () {
        return Inertia::render('catalogos/Productos');
    })->name('productos');

    Route::get('hoteles', function () {
        return Inertia::render('catalogos/Hoteles');
    })->name('hoteles');

    Route::get('gestion-reserva-tours', function () {
        return Inertia::render('catalogos/ReservaTours');
    })->name('reservatours');

    Route::get('tours', function () {
        return Inertia::render('catalogos/Tours');
    })->name('tours');

    Route::get('aerolineas', function () {
        return Inertia::render('catalogos/Aerolineas');
    })->name('aerolineas');

    Route::get('categorias', function () {
        return Inertia::render('catalogos/ControlCategorias');
    })->name('catPTH');

    Route::get('control-paises-provincias', function () {
        return Inertia::render('catalogos/ControlPaisesProvincias');
    })->name('controlPaisesProvincias');

    Route::get('tipo-documentos', function () {
        return Inertia::render('Configuracion/TiposDocumento');
    })->name('tipodocumentos');

    Route::get('generar-informes', function () {
        return Inertia::render('Informes/Informes');
    })->name('informes');

    Route::get('/descargar-informe', [InformePDFController::class, 'descargarInforme']);

    Route::get('/configuracion/backup', [BackupController::class, 'showBackupPage'])->name('backups')->middleware('password.confirm');

    Route::get('/configuracion/roles', [RoleController::class, 'index'])->name('roles')->middleware('role:admin');
    
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
    //Route::get('/configuracion/tipos-documento', [TipoDocumentoController::class, 'index'])->name('tipos-documento');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Rutas para vistas de clientes //***NO MODIFICAR ESTAS RUTAS***//
Route::get('paquetes', function () {
    return Inertia::render('vistasClientes/Paquetes');
})->name('paquetes');

Route::get('reservaciones', function () {
    return Inertia::render('vistasClientes/Reservaciones');
})->name('reservaciones');

Route::get('tours-nacionales', [TourController::class, 'toursNacionales'])->name('tours-nacionales');

Route::get('tours-internacionales', [TourController::class, 'toursInternacionales'])->name('tours-internacionales');

// Rutas para vista detallada de tours
Route::get('/tours-nacionales/{id}', [TourController::class, 'mostrarTourNacional'])->name('tour-nacional.show');
Route::get('/tours-internacionales/{id}', [TourController::class, 'mostrarTourInternacional'])->name('tour-internacional.show');

Route::get('tienda', function () {
    return Inertia::render('vistasClientes/Tienda');
})->name('tienda');

Route::get('sobre-nosotros', [SobreNosotrosController::class, 'index'])->name('sobre-nosotros');

Route::get('contactos', function () {
    return Inertia::render('vistasClientes/Contactos');
})->name('contactos');

require __DIR__.'/auth.php';