<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Catalogo');
})->name('Catalogo');

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

Route::get('catalogos/Productos', function () {
    return Inertia::render('catalogos/Productos');
    })->middleware(['auth', 'verified'])->name('productos');
});

Route::get('catalogos/Hoteles', function () {
    return Inertia::render('catalogos/Hoteles');
    })->middleware(['auth', 'verified'])->name('hoteles');
    
Route::get('catalogos/ReservaTours', function () {
    return Inertia::render('catalogos/ReservaTours');
    })->middleware(['auth', 'verified'])->name('reservatours');

Route::get('catalogos/Tours', function () {
    return Inertia::render('catalogos/Tours');
})->middleware(['auth', 'verified'])->name('tours');

Route::get('catalogos/Aerolineas', function () {
    return Inertia::render('catalogos/Aerolineas');
})->middleware(['auth', 'verified'])->name('aerolineas');


// Rutas para vistas de clientes
Route::get('/inicio', function () {
    return Inertia::render('Inicio');
})->name('inicio');

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
