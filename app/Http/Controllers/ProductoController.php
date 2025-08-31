<?php

namespace App\Http\Controllers;

use App\Models\Imagen;
use App\Models\Producto;
use App\Models\CategoriaProducto;
use Illuminate\Http\Request;
use Exception;

class ProductoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // ✅ CORREGIDO: categoriaProducto (no categoria), sin inventario (no existe esa relación)
        return Producto::with(['imagenes', 'categoriaProducto'])->get();
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Devolver categorías para el formulario
        $categorias = CategoriaProducto::orderBy('nombre')->get();
        return response()->json(['categorias' => $categorias]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'required|string',
            'precio' => 'required|numeric|min:0|max:9999.99',
            // ✅ CORREGIDO: stock_actual y stock_minimo (no inventario_id)
            'stock_actual' => 'required|integer|min:0',
            'stock_minimo' => 'required|integer|min:0',
            'categoria_id' => 'required|exists:categorias_productos,id',
            'imagenes' => 'nullable|array',
            'imagenes.*' => 'image|max:2048',
        ]);

        try {
            // ✅ CORREGIDO: campos según migración
            $producto = Producto::create($request->only([
                'nombre',
                'descripcion',
                'precio',
                'stock_actual',
                'stock_minimo',
                'categoria_id'
            ]));

            if ($request->hasFile('imagenes')) {
                foreach ($request->file('imagenes') as $imagen) {
                    $nombreArchivo = time() . '_' . $imagen->getClientOriginalName();
                    $destino = public_path('images/productos');

                    if (!file_exists($destino)) {
                        mkdir($destino, 0755, true);
                    }

                    $imagen->move($destino, $nombreArchivo);

                    $producto->imagenes()->create([
                        'nombre' => $nombreArchivo
                    ]);
                }
            }

            // ✅ CORREGIDO: cargar categoriaProducto
            return response()->json($producto->load(['imagenes', 'categoriaProducto']), 201);

        } catch (Exception $e) {
            return response()->json([
                'message' => 'Error al crear producto: ' . $e->getMessage(),
                'error' => true
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'required|string',
            'precio' => 'required|numeric|min:0|max:9999.99',
            // ✅ CORREGIDO: stock fields
            'stock_actual' => 'required|integer|min:0',
            'stock_minimo' => 'required|integer|min:0',
            'categoria_id' => 'required|exists:categorias_productos,id',
            'imagenes' => 'nullable|array',
            'imagenes.*' => 'image|max:2048',
        ]);

        try {
            $producto = Producto::findOrFail($id);
            
            // ✅ CORREGIDO: campos según migración
            $producto->update($request->only([
                'nombre',
                'descripcion',
                'precio',
                'stock_actual',
                'stock_minimo',
                'categoria_id'
            ]));

            // Agregar nuevas imágenes sin eliminar las existentes
            if ($request->hasFile('imagenes')) {
                foreach ($request->file('imagenes') as $imagen) {
                    $nombreArchivo = time() . '_' . $imagen->getClientOriginalName();
                    $destino = public_path('images/productos');

                    if (!file_exists($destino)) {
                        mkdir($destino, 0755, true);
                    }

                    $imagen->move($destino, $nombreArchivo);

                    $producto->imagenes()->create([
                        'nombre' => $nombreArchivo,
                    ]);
                }
            }

            // Eliminar imágenes seleccionadas
            if ($request->has('removed_images')) {
                foreach ($request->input('removed_images') as $imageName) {
                    $imagen = $producto->imagenes()->where('nombre', $imageName)->first();
                    if ($imagen) {
                        $rutaImagen = public_path('images/productos/' . $imagen->nombre);
                        if (file_exists($rutaImagen)) {
                            unlink($rutaImagen); // Elimina el archivo físico
                        }
                        $imagen->delete(); // Elimina el registro de la base de datos
                    }
                }
            }

            // ✅ CORREGIDO: cargar categoriaProducto
            return response()->json($producto->load(['imagenes', 'categoriaProducto']), 200);

        } catch (Exception $e) {
            return response()->json([
                'message' => 'Error al actualizar producto: ' . $e->getMessage(),
                'error' => true
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Producto $producto)
    {
        // ✅ CORREGIDO: categoriaProducto e imagenes
        $producto->load(['categoriaProducto', 'imagenes']);
        return response()->json($producto);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Producto $producto)
    {
        // Cargar producto con sus relaciones y categorías disponibles
        $producto->load(['categoriaProducto', 'imagenes']);
        $categorias = CategoriaProducto::orderBy('nombre')->get();
        
        return response()->json([
            'producto' => $producto,
            'categorias' => $categorias
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $producto = Producto::find($id);

            if (!$producto) {
                return response()->json(['error' => 'Producto no encontrado'], 404);
            }

            // Verificar si tiene movimientos de inventario
            if ($producto->inventarios()->exists()) {
                return response()->json([
                    'error' => 'No se puede eliminar el producto porque tiene movimientos de inventario registrados'
                ], 422);
            }

            // Eliminar imágenes asociadas
            foreach ($producto->imagenes as $imagen) {
                $rutaImagen = public_path('images/productos/' . $imagen->nombre);
                if (file_exists($rutaImagen)) {
                    unlink($rutaImagen);
                }
                $imagen->delete();
            }

            $producto->delete();

            return response()->json(['message' => 'Producto eliminado correctamente'], 200);

        } catch (Exception $e) {
            return response()->json([
                'message' => 'Error al eliminar producto: ' . $e->getMessage(),
                'error' => true
            ], 500);
        }
    }

    /**
     * ✅ NUEVO: Obtener productos con stock bajo
     */
    public function stockBajo()
    {
        $productos = Producto::with('categoriaProducto')
            ->whereColumn('stock_actual', '<=', 'stock_minimo')
            ->where('stock_actual', '>', 0)
            ->orderBy('stock_actual', 'asc')
            ->get();
            
        return response()->json($productos);
    }

    /**
     * ✅ NUEVO: Obtener productos agotados
     */
    public function agotados()
    {
        $productos = Producto::with('categoriaProducto')
            ->where('stock_actual', '<=', 0)
            ->orderBy('nombre')
            ->get();
            
        return response()->json($productos);
    }
}