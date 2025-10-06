<?php

use App\Http\Controllers\AerolineaController;
use App\Http\Controllers\Auth\ApiAuthController;
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
use App\Http\Controllers\TransporteController;
use App\Http\Controllers\TipoDocumentoController;
use App\Http\Controllers\PaqueteController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\InventarioController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// ═══════════════════════════════════════════════════════════
// 🌐 RUTAS PÚBLICAS (sin autenticación)
// ═══════════════════════════════════════════════════════════

// Rutas de autenticación
Route::post('/login', [ApiAuthController::class, 'login']);

//Rutas de validación en tiempo real para el registro
Route::post('/auth/check-name', [RegisteredUserController::class, 'checkName']);
Route::post('/auth/check-email', [RegisteredUserController::class, 'checkEmail']);

// Rutas para la tienda
Route::get('/productos', [ProductoController::class, 'index']);
Route::get('/categorias-productos', [CategoriaProductoController::class, 'index']);
Route::get('/tours', [TourController::class, 'index']);
Route::get('/hoteles', [HotelController::class, 'index']);
Route::get('/paquetes', [PaqueteController::class, 'index']);

// ═══════════════════════════════════════════════════════════
// 🔐 RUTAS PROTEGIDAS (requieren autenticación)
// ═══════════════════════════════════════════════════════════
Route::middleware('auth:sanctum')->group(function () {

    // ───────────────────────────────────────────────────────
    // 👤 RUTAS BÁSICAS DE USUARIO
    // ───────────────────────────────────────────────────────
    Route::post('/logout', [ApiAuthController::class, 'logout']);
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // ───────────────────────────────────────────────────────
    // 🛡️ RUTAS ADMINISTRATIVAS (requieren rol admin/empleado)
    // ───────────────────────────────────────────────────────
    Route::middleware('rutas.admin')->group(function () {

        // Gestión de reservas
        Route::prefix('reservas')->group(function () {
            Route::get('/', [ReservaController::class, 'index']);
            Route::get('/resumen', [ReservaController::class, 'resumen']);
            Route::put('/{id}/confirmar', [ReservaController::class, 'confirmar']);
            Route::put('/{id}/rechazar', [ReservaController::class, 'rechazar']);
            Route::put('/{id}/reprogramar', [ReservaController::class, 'reprogramar']);
            Route::get('/{id}/historial', [ReservaController::class, 'historial']);
        });

        // Recursos CRUD principales
        Route::apiResource('productos', ProductoController::class)->except(['index']);
        Route::apiResource('categorias-productos', CategoriaProductoController::class)->except(['index']);
        Route::apiResource('hoteles', HotelController::class)->except(['index']);
        Route::apiResource('tours', TourController::class)->except(['index']);
        Route::apiResource('paquetes', PaqueteController::class)->except(['index']);
        Route::apiResource('clientes', ClienteController::class);
        Route::apiResource('ventas', VentaController::class);
        Route::apiResource('aerolineas', AerolineaController::class);
        Route::apiResource('empleados', EmpleadoController::class);
        Route::apiResource('metodos-pago', MetodoPagoController::class);
        Route::apiResource('categorias-hoteles', CategoriaHotelController::class);
        Route::apiResource('paises', PaisController::class)->parameter('paises', 'pais');
        Route::apiResource('provincias', ProvinciaController::class)->parameter('provincias', 'provincia');
        Route::apiResource('transportes', TransporteController::class);
        Route::apiResource('tipo-documentos', TipoDocumentoController::class);

        //Gestión específica de clientes
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
        Route::prefix('admin/categorias-productos')->name('admin.categorias-productos.')->group(function () {
            Route::get('/', [CategoriaProductoController::class, 'index'])->name('index');
            Route::post('/', [CategoriaProductoController::class, 'store'])->name('store');
            Route::get('/{categoriaProducto}', [CategoriaProductoController::class, 'show'])->name('show');
            Route::put('/{categoriaProducto}', [CategoriaProductoController::class, 'update'])->name('update');
            Route::delete('/{categoriaProducto}', [CategoriaProductoController::class, 'destroy'])->name('destroy');
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

        // 🔧 Rutas adicionales específicas para productos (sin conflicto)
        Route::prefix('admin/productos')->group(function () {
            Route::get('/stock-bajo', [ProductoController::class, 'stockBajo']);
            Route::get('/agotados', [ProductoController::class, 'agotados']);
        });

        // // 🔧 Rutas adicionales específicas para ventas (sin conflicto)
        // Route::prefix('ventas')->group(function () {
        //     Route::get('/estado/{estado}', [VentaController::class, 'porEstado']);
        //     Route::post('/{venta}/procesar', [VentaController::class, 'procesar']);
        //     Route::post('/{venta}/cancelar', [VentaController::class, 'cancelar']);
        // });
    });
});
