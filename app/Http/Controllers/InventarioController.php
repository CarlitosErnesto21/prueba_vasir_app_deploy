<?php

namespace App\Http\Controllers;

use App\Models\Inventario;
use App\Models\Producto;
use App\Services\InventarioService;
use Illuminate\Http\Request;
use Exception;

class InventarioController extends Controller
{
    protected $inventarioService;

    public function __construct(InventarioService $inventarioService)
    {
        $this->inventarioService = $inventarioService;
    }

    /**
     * Mostrar historial de movimientos de inventario
     */
    public function index(Request $request)
    {
        $query = Inventario::with(['producto', 'user', 'referenciable']);

        // Filtros opcionales
        if ($request->has('producto_id')) {
            $query->where('producto_id', $request->producto_id);
        }

        if ($request->has('tipo_movimiento')) {
            $query->where('tipo_movimiento', $request->tipo_movimiento);
        }

        if ($request->has('fecha_desde')) {
            $query->whereDate('fecha_movimiento', '>=', $request->fecha_desde);
        }

        $movimientos = $query->orderBy('fecha_movimiento', 'desc')->paginate(20);

        return response()->json($movimientos);
    }

    /**
     * Agregar stock manualmente
     */
    public function agregarStock(Request $request)
    {
        $validated = $request->validate([
            'producto_id' => 'required|exists:productos,id',
            'cantidad' => 'required|integer|min:1',
            'motivo' => 'nullable|string|max:50',
            'observacion' => 'nullable|string|max:255',
        ]);

        try {
            $movimiento = $this->inventarioService->agregarStock(
                $validated['producto_id'],
                $validated['cantidad'],
                $validated['motivo'] ?? 'entrada_manual',
                $validated['observacion']
            );

            return response()->json([
                'message' => 'Stock agregado exitosamente',
                'movimiento' => $movimiento->load('producto'),
            ]);

        } catch (Exception $e) {
            return response()->json([
                'message' => 'Error al agregar stock: ' . $e->getMessage(),
                'error' => true
            ], 400);
        }
    }

    /**
     * Ajustar stock a un valor específico
     */
    public function ajustarStock(Request $request)
    {
        $validated = $request->validate([
            'producto_id' => 'required|exists:productos,id',
            'nuevo_stock' => 'required|integer|min:0',
            'observacion' => 'nullable|string|max:255',
        ]);

        try {
            $this->inventarioService->ajustarStock(
                $validated['producto_id'],
                $validated['nuevo_stock'],
                $validated['observacion'] ?? 'Ajuste de inventario'
            );

            $producto = Producto::find($validated['producto_id']);

            return response()->json([
                'message' => 'Stock ajustado exitosamente',
                'producto' => $producto,
            ]);

        } catch (Exception $e) {
            return response()->json([
                'message' => 'Error al ajustar stock: ' . $e->getMessage(),
                'error' => true
            ], 400);
        }
    }

    /**
     * Obtener resumen de inventario
     */
    public function resumen()
    {
        $resumen = [
            'total_productos' => Producto::count(),
            'productos_disponibles' => Producto::where('stock_actual', '>', 0)->count(),
            'productos_agotados' => Producto::where('stock_actual', '<=', 0)->count(),
            'productos_stock_bajo' => Producto::whereColumn('stock_actual', '<=', 'stock_minimo')->where('stock_actual', '>', 0)->count(),
            'movimientos_hoy' => Inventario::whereDate('fecha_movimiento', today())->count(),
        ];
        return response()->json($resumen);
    }

    /**
     * Obtener productos con stock bajo
     */
    public function stockBajo()
    {
        $productos = Producto::with('categoria')->whereColumn('stock_actual', '<=', 'stock_minimo')->where('stock_actual', '>', 0)->get();
        return response()->json($productos);
    }

    /**
     * Obtener productos agotados
     */
    public function agotados()
    {
        $productos = Producto::with('categoria')->where('stock_actual', '<=', 0)->get();
        return response()->json($productos);
    }

    /**
     * Historial de movimientos de un producto específico
     */
    public function historialProducto(Producto $producto)
    {
        $movimientos = $producto->inventarios()->with(['user', 'referenciable'])->orderBy('fecha_movimiento', 'desc')->paginate(15);
        return response()->json([
            'producto' => $producto,
            'movimientos' => $movimientos,
        ]);
    }

    /**
     *  MÉTODOS PARA COMPATIBILIDAD
     */
    public function show(Inventario $inventario)
    {
        $inventario->load(['producto', 'user', 'referenciable']);
        return response()->json($inventario);
    }
}