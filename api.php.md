´´´´
<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AerolineaController;
use App\Http\Controllers\BackupController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\EmpleadoController;
use App\Http\Controllers\HotelController;
use App\Http\Controllers\MetodoPagoController;
use App\Http\Controllers\ReservaController;
use App\Http\Controllers\TourController;
use App\Http\Controllers\VentaController;
use App\Http\Controllers\CategoriaHotelController;
use App\Http\Controllers\CategoriaProductoController;
use App\Http\Controllers\PaisController;
use App\Http\Controllers\ProvinciaController;
use App\Http\Controllers\InventarioController;
use App\Http\Controllers\TransporteController;
use App\Http\Controllers\TipoDocumentoController;
use App\Http\Controllers\PaqueteController;
use App\Http\Controllers\Auth\RegisteredUserController;

// RUTAS PÚBLICAS (GET, visibles para todos)
Route::get('productos', [ProductoController::class, 'index']);
Route::get('productos/{id}', [ProductoController::class, 'show']);
Route::get('tours', [TourController::class, 'index']);
Route::get('tours/{id}', [TourController::class, 'show']);
Route::get('categorias-productos', [CategoriaProductoController::class, 'index']);
Route::get('categorias-productos/{id}', [CategoriaProductoController::class, 'show']);
Route::get('hoteles', [HotelController::class, 'index']);
Route::get('hoteles/{id}', [HotelController::class, 'show']);
Route::get('paquetes', [PaqueteController::class, 'index']);
Route::get('paquetes/{id}', [PaqueteController::class, 'show']);
Route::get('aerolineas', [AerolineaController::class, 'index']);
Route::get('aerolineas/{id}', [AerolineaController::class, 'show']);
Route::get('provincias', [ProvinciaController::class, 'index']);
Route::get('provincias/{id}', [ProvinciaController::class, 'show']);
Route::get('paises', [PaisController::class, 'index']);
Route::get('paises/{id}', [PaisController::class, 'show']);
Route::get('transportes', [TransporteController::class, 'index']);
Route::get('transportes/{id}', [TransporteController::class, 'show']);
Route::get('tipo-documentos', [TipoDocumentoController::class, 'index']);
Route::get('tipo-documentos/{id}', [TipoDocumentoController::class, 'show']);
Route::get('categorias-hoteles', [CategoriaHotelController::class, 'index']);
Route::get('categorias-hoteles/{id}', [CategoriaHotelController::class, 'show']);
Route::get('empleados', [EmpleadoController::class, 'index']);
Route::get('empleados/{id}', [EmpleadoController::class, 'show']);
Route::get('metodos-pago', [MetodoPagoController::class, 'index']);
Route::get('metodos-pago/{id}', [MetodoPagoController::class, 'show']);
Route::get('ventas', [VentaController::class, 'index']);
Route::get('ventas/{id}', [VentaController::class, 'show']);
Route::get('inventario', [InventarioController::class, 'index']);
Route::get('inventario/{id}', [InventarioController::class, 'show']);

// RUTAS PROTEGIDAS (solo usuarios autenticados y con rol admin/empleado)
Route::middleware(['auth:sanctum', 'role:admin|empleado'])->group(function () {
    // Productos
    Route::post('productos', [ProductoController::class, 'store']);
    Route::put('productos/{id}', [ProductoController::class, 'update']);
    Route::delete('productos/{id}', [ProductoController::class, 'destroy']);
    // Tours
    Route::post('tours', [TourController::class, 'store']);
    Route::put('tours/{id}', [TourController::class, 'update']);
    Route::delete('tours/{id}', [TourController::class, 'destroy']);
    // Ventas
    Route::post('ventas', [VentaController::class, 'store']);
    Route::put('ventas/{id}', [VentaController::class, 'update']);
    Route::delete('ventas/{id}', [VentaController::class, 'destroy']);
    // Inventario
    Route::post('inventario/agregar-stock', [InventarioController::class, 'agregarStock']);
    Route::post('inventario/ajustar-stock', [InventarioController::class, 'ajustarStock']);
    Route::delete('inventario/{id}', [InventarioController::class, 'destroy']);
    // Empleados
    Route::post('empleados', [EmpleadoController::class, 'store']);
    Route::put('empleados/{id}', [EmpleadoController::class, 'update']);
    Route::delete('empleados/{id}', [EmpleadoController::class, 'destroy']);
    // Hoteles
    Route::post('hoteles', [HotelController::class, 'store']);
    Route::put('hoteles/{id}', [HotelController::class, 'update']);
    Route::delete('hoteles/{id}', [HotelController::class, 'destroy']);

    Route::get('reservas', [ReservaController::class, 'index']);
    // ...otros métodos protegidos para admin/empleado
});

// RUTAS PROTEGIDAS PARA CLIENTES AUTENTICADOS
Route::middleware(['auth:sanctum', 'role:cliente'])->group(function () {
    // Reservas
    Route::post('reservas', [ReservaController::class, 'store']);
    Route::get('clientes/{id}', [ClienteController::class, 'show']);
    Route::put('clientes/{id}', [ClienteController::class, 'update']);
    // ...otros métodos protegidos para clientes
});

// Endpoints de validación de registro
Route::post('auth/check-name', [RegisteredUserController::class, 'checkName']);
Route::post('auth/check-email', [RegisteredUserController::class, 'checkEmail']);

´´´´