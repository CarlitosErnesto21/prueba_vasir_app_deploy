<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\InformePDFController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\BackupController;
use App\Http\Controllers\ReservaController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\TourController;
use App\Http\Controllers\SobreNosotrosController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Middleware\RutasAdmin;

//Ruta principal
Route::get('/', fn() => Inertia::render('Inicio'))->name('inicio');

// Rutas protegidas
Route::middleware(['auth', 'verified', RutasAdmin::class])->group(function () {

    Route::get('dashboard', fn() => Inertia::render('Dashboard'))->name('dashboard');
    Route::get('transportes', fn() => Inertia::render('Catalogos/Transportes'))->name('transportes');
    Route::get('productos', fn() => Inertia::render('Catalogos/Productos'))->name('productos');
    Route::get('hoteles', fn() => Inertia::render('Catalogos/Hoteles'))->name('hoteles');
    Route::get('gestion-reserva-tours', fn() => Inertia::render('Catalogos/ReservaTours'))->name('reservatours');
    Route::get('tours', fn() => Inertia::render('Catalogos/Tours'))->name('tours');
    Route::get('aerolineas', fn() => Inertia::render('Catalogos/Aerolineas'))->name('aerolineas');
    Route::get('categorias', fn() => Inertia::render('Catalogos/ControlCategorias'))->name('catPTH');
    Route::get('control-paises-provincias', fn() => Inertia::render('Catalogos/ControlPaisesProvincias'))->name('controlPaisesProvincias');
    Route::get('tipo-documentos', fn() => Inertia::render('Configuracion/TiposDocumento'))->name('tipodocumentos');
    Route::get('generar-informes', fn() => Inertia::render('Informes/Informes'))->name('informes');
    Route::get('clientes', fn() => Inertia::render('Configuracion/Clientes'))->name('clientes');

    //Rutas para informes
    Route::get('/descargar-informe', [InformePDFController::class, 'descargarInforme']);
    Route::get('/configuracion/backup', [BackupController::class, 'showBackupPage'])->name('backups')->middleware('password.confirm');
    Route::get('/configuracion/roles', [RoleController::class, 'index'])->name('roles')->middleware('role:admin');

    //Rutas para gestión de roles
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
});

//Rutas de perfil
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

//Rutas de clientes
Route::get('paquetes', fn() => Inertia::render('VistasClientes/Paquetes'))->name('paquetes');
Route::get('reservaciones', fn() => Inertia::render('VistasClientes/Reservaciones'))->name('reservaciones');
Route::get('tours-nacionales', fn() => Inertia::render('VistasClientes/tours/ToursNacionales'))->name('tours-nacionales');
Route::get('tours-internacionales', fn() => Inertia::render('VistasClientes/tours/ToursInternacionales'))->name('tours-internacionales');
Route::get('tienda', fn() => Inertia::render('VistasClientes/Tienda'))->name('tienda');
Route::get('sobre-nosotros', [SobreNosotrosController::class, 'index'])->name('sobre-nosotros');
Route::get('contactos', fn() => Inertia::render('VistasClientes/Contactos'))->name('contactos');

//Estas rutas muestran un tour ya sea nacional o internacional
Route::get('/tours-nacionales/{id}', [TourController::class, 'mostrarTourNacional'])->name('tour-nacional.show');
Route::get('/tours-internacionales/{id}', [TourController::class, 'mostrarTourInternacional'])->name('tour-internacional.show');

// Ruta para crear reservas de tours (accesible para usuarios autenticados)
Route::post('/reservas/tour', [ReservaController::class, 'crearReservaTour'])->middleware('auth')->name('reservas.tour');

// Ruta para obtener datos del cliente autenticado
Route::get('/api/cliente-datos', [ClienteController::class, 'obtenerDatosAutenticado'])->middleware('auth')->name('cliente.datos');

require __DIR__.'/auth.php';