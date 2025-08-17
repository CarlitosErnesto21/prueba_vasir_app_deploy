<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\InformePDFController;
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
    })->middleware(['auth', 'verified'])->name('categorias');

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