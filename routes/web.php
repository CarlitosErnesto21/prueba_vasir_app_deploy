<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\InformePDFController;
use App\Http\Controllers\RoleController;
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
    })->name('categorias');

    /////////////////////////////////////////////////////////////////
    // Respaldo de Base de Datos
    Route::get('/configuracion/backup', function () {
        return Inertia::render('Configuracion/Backup');
    })->name('backup');
    
    // Asignación de Roles - Solo para Administradores
    Route::get('/configuracion/roles', [RoleController::class, 'index'])->name('roles')->middleware('role:admin');
    
    // API routes for role management - Solo para Administradores
    Route::post('/roles/users/{user}/update-roles', [RoleController::class, 'updateUserRoles'])->middleware('role:admin');
    Route::post('/roles/users/{user}/assign-role', [RoleController::class, 'assignRole'])->middleware('role:admin');
    Route::post('/roles/users/{user}/remove-role', [RoleController::class, 'removeRole'])->middleware('role:admin');
    Route::get('/roles/users/{user}/permissions', [RoleController::class, 'getUserPermissions'])->middleware('role:admin');
    Route::post('/roles/users/{user}/assign-permission', [RoleController::class, 'assignPermission'])->middleware('role:admin');
    Route::post('/roles/users/{user}/remove-permission', [RoleController::class, 'removePermission'])->middleware('role:admin');
    Route::post('/roles/create-user', [RoleController::class, 'createInternalUser'])->middleware('role:admin');
    
    // Configuración del Sistema
    Route::get('/configuracion/settings', function () {
        return Inertia::render('Configuracion/Settings');
    })->name('settings');
    
    // Gestión de Usuarios
    Route::get('/configuracion/users', function () {
        return Inertia::render('Configuracion/Users');
    })->name('users');
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

Route::get('/tienda', function () {
    return Inertia::render('vistasClientes/Tienda');
})->name('tienda');

Route::get('/sobre-nosotros', function () {
    return Inertia::render('vistasClientes/SobreNosotros');
})->name('sobre-nosotros');

Route::get('/contactos', function () {
    return Inertia::render('vistasClientes/Contactos');
})->name('contactos');

require __DIR__.'/auth.php';