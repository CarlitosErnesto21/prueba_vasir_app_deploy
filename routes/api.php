<?php

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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Rutas de API
Route::apiResource('productos', ProductoController::class);
Route::apiResource('aerolineas', AerolineaController::class);
Route::apiResource('empleados', EmpleadoController::class);
Route::apiResource('hoteles', HotelController::class);
Route::apiResource('metodos-pago', MetodoPagoController::class);
Route::apiResource('reservas', ReservaController::class);
Route::apiResource('tours', TourController::class);
Route::apiResource('ventas', VentaController::class);
Route::apiResource('categorias-hoteles', CategoriaHotelController::class);
Route::apiResource('categorias-productos', CategoriaProductoController::class);
Route::apiResource('paises', PaisController::class);
Route::apiResource('provincias', ProvinciaController::class);
Route::apiResource('transportes', TransporteController::class);
Route::apiResource('clientes', ClienteController::class);
Route::apiResource('tipo-documentos', TipoDocumentoController::class);

Route::prefix('backups')->group(function () {
    Route::get('/', [BackupController::class, 'index']);
    Route::post('/generate', [BackupController::class, 'generate']);
    Route::get('/{id}/download', [BackupController::class, 'download']);
    Route::delete('/{id}', [BackupController::class, 'delete']);
    Route::post('/cleanup', [BackupController::class, 'cleanup']);
});

Route::prefix('clientes')->group(function () {
    Route::get('/', [ClienteController::class, 'getClientes']);
    Route::post('/', [ClienteController::class, 'store']);
    Route::get('/{cliente}', [ClienteController::class, 'show']);
    Route::put('/{cliente}', [ClienteController::class, 'update']);
    Route::delete('/{cliente}', [ClienteController::class, 'destroy']);
    Route::patch('/{cliente}/toggle-status', [ClienteController::class, 'toggleStatus']);
    Route::get('/tipos-documento-options', [ClienteController::class, 'getTiposDocumento']);
});

// Rutas de Categorías de Productos
Route::prefix('categorias-productos')->name('categorias-productos.')->group(function () {
    Route::get('/', [CategoriaProductoController::class, 'index'])->name('index');
    Route::post('/', [CategoriaProductoController::class, 'store'])->name('store');
    Route::get('/{categoriaProducto}', [CategoriaProductoController::class, 'show'])->name('show');
    Route::put('/{categoriaProducto}', [CategoriaProductoController::class, 'update'])->name('update');
    Route::delete('/{categoriaProducto}', [CategoriaProductoController::class, 'destroy'])->name('destroy');
});

// Rutas de Productos
Route::prefix('productos')->name('productos.')->group(function () {
    Route::get('/', [ProductoController::class, 'index'])->name('index');
    Route::get('/create', [ProductoController::class, 'create'])->name('create');
    Route::post('/', [ProductoController::class, 'store'])->name('store');
    Route::get('/stock-bajo', [ProductoController::class, 'stockBajo'])->name('stock-bajo');
    Route::get('/agotados', [ProductoController::class, 'agotados'])->name('agotados');
    Route::get('/{producto}', [ProductoController::class, 'show'])->name('show');
    Route::get('/{producto}/edit', [ProductoController::class, 'edit'])->name('edit');
    Route::put('/{producto}', [ProductoController::class, 'update'])->name('update');
    Route::delete('/{producto}', [ProductoController::class, 'destroy'])->name('destroy');
});

// Rutas de Ventas
Route::prefix('ventas')->name('ventas.')->group(function () {
    Route::get('/', [VentaController::class, 'index'])->name('index');
    Route::get('/create', [VentaController::class, 'create'])->name('create');
    Route::post('/', [VentaController::class, 'store'])->name('store');
    Route::get('/estado/{estado}', [VentaController::class, 'porEstado'])->name('por-estado');
    Route::get('/{venta}', [VentaController::class, 'show'])->name('show');
    Route::get('/{venta}/edit', [VentaController::class, 'edit'])->name('edit');
    Route::put('/{venta}', [VentaController::class, 'update'])->name('update');
    Route::post('/{venta}/procesar', [VentaController::class, 'procesar'])->name('procesar');
    Route::post('/{venta}/cancelar', [VentaController::class, 'cancelar'])->name('cancelar');
    Route::delete('/{venta}', [VentaController::class, 'destroy'])->name('destroy');
});

// Rutas de Inventario
Route::prefix('inventario')->name('inventario.')->group(function () {
    Route::get('/', [InventarioController::class, 'index'])->name('index');
    Route::post('/agregar-stock', [InventarioController::class, 'agregarStock'])->name('agregar-stock');
    Route::post('/ajustar-stock', [InventarioController::class, 'ajustarStock'])->name('ajustar-stock');
    Route::get('/resumen', [InventarioController::class, 'resumen'])->name('resumen');
    Route::get('/stock-bajo', [InventarioController::class, 'stockBajo'])->name('stock-bajo');
    Route::get('/agotados', [InventarioController::class, 'agotados'])->name('agotados');
    Route::get('/producto/{producto}/historial', [InventarioController::class, 'historialProducto'])->name('producto.historial');
    Route::get('/{inventario}', [InventarioController::class, 'show'])->name('show');
});