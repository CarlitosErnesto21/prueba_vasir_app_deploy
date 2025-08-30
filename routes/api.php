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
use App\Http\Controllers\InventarioController;
use App\Http\Controllers\PaisController;
use App\Http\Controllers\ProvinciaController;
use App\Http\Controllers\TransporteController;
use App\Http\Controllers\TipoDocumentoController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


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
Route::apiResource('inventarios', InventarioController::class);
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