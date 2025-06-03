<?php

namespace App\Http\Controllers;

use App\Models\Imagen;
use App\Models\Producto;
use Illuminate\Http\Request;

class ProductoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Producto::with(['imagenes', 'inventario', 'categoria'])->get();
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nombre'        => 'required|string|max:255',
            'descripcion'   => 'required|string',
            'precio'        => 'required|numeric|min:0|max:9999.99',
            'inventario_id' => 'required|exists:inventarios,id',
            'categoria_id'  => 'required|exists:categorias_productos,id',

            // acepta jpeg, png, gif, webp, bmp, svg por MIME
            'imagenes'      => 'nullable|array',
            'imagenes.*'    => 'image|max:2048',
        ]);
        // Crea el producto con los datos validados

        $producto = Producto::create($request->only([
            'nombre',
            'descripcion',
            'precio',
            'inventario_id',
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

                Imagen::create([
                    'producto_id' => $producto->id,
                    'nombre' => $nombreArchivo // ✅ solo el nombre real del archivo
                ]);
            }
        }

        return response()->json($producto->load('imagenes'), 201);
    }



    /**
     * Display the specified resource.
     */
    public function show(Producto $producto)
    {
        $producto->load('categoria');
        return response()->json($producto);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Producto $producto)
    {
        //
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
            'inventario_id' => 'required|exists:inventarios,id',
            'categoria_id' => 'required|exists:categorias_productos,id',
            'imagenes'      => 'nullable|array',
            'imagenes.*'    => 'image|max:2048',
        ]);

        $producto = Producto::findOrFail($id);
        $producto->update($validated);

        // Agregar nuevas imágenes sin eliminar las existentes
        if ($request->hasFile('imagenes')) {
            foreach ($request->file('imagenes') as $imagen) {
                $nombreArchivo = time() . '_' . $imagen->getClientOriginalName();
                $destino = public_path('images/productos');

                if (!file_exists($destino)) {
                    mkdir($destino, 0755, true);
                }

                $imagen->move($destino, $nombreArchivo);

                Imagen::create([
                    'producto_id' => $producto->id,
                    'nombre' => $nombreArchivo, // Guarda solo el nombre del archivo
                ]);
            }
        }

        if ($request->has('removed_images')) {
            foreach ($request->input('removed_images') as $imageName) {
                $imagen = Imagen::where('nombre', $imageName)->where('producto_id', $producto->id)->first();
                if ($imagen) {
                    $rutaImagen = public_path('images/productos/' . $imagen->nombre);
                    if (file_exists($rutaImagen)) {
                        unlink($rutaImagen); // Elimina el archivo físico
                    }
                    $imagen->delete(); // Elimina el registro de la base de datos
                }
            }
        }

        return response()->json($producto->load('imagenes'), 200);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $producto = Producto::find($id);

        if (!$producto) {
            return response()->json(['error' => 'Producto no encontrado'], 404);
        }

        $producto->delete();

        return response()->json(['message' => 'Producto eliminado correctamente'], 200);
    }
}
