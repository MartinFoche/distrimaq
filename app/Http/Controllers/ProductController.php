<?php

namespace App\Http\Controllers;
use Inertia\inertia;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\ProductImage;
class ProductController extends Controller
{
    public function index_cargar(){
        return Inertia::render('Cargar_productos');
    }

    public function index_editar(){
        return Inertia::render('Editar_productos');
    }

    public function store_cargar(Request $request){
        if ($request->has('images') && is_array($request->images)) {
            // Filtrar para eliminar valores null o cadenas vacías
            $request->request->set('images', array_filter($request->images, fn($url) => !empty($url)));
        } else {
            $request->request->set('images', []);
        }
        $validated = $request->validate([
            'description' => 'required|string|max:255',
            'category' => 'nullable|exists:categories,id',
            'sku' => 'required|string|max:20|unique:products,sku',
            'price' => 'required|numeric',
            'images' => 'sometimes|array',
            'images.*' => 'url',
        ]);
        $product = Product::create([
            'description' => $validated['description'],
            'sku' => $validated['sku'],
            'price' => $validated['price'],
        ]);
        $images = $validated['images'] ?? [];
        if (!empty($images)) {
            foreach ($images as $url) {
                $product->images()->create(['url' => $url]);
            }
        }
        if (!empty($validated['category'])) {
            $product->categories()->attach($validated['category']);
        }
    }

    public function search_products(Request $request){
        $query = $request->input('query');

        $product = Product::with('images')
            ->where('sku', 'like', "%{$query}%")
            ->orWhere('description', 'like', "%{$query}%")
            ->limit(10)
            ->get();

        if ($product) {
            return response()->json($product);
        }

    }
    public function destroy($id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json(['message' => 'Producto no encontrado'], 404);
        }

        $product->delete();
    }

    public function update(Request $request, $id)
    {
        // Validar datos
        $validated = $request->validate([
            'description' => 'required|string|max:255',
            'price' => 'required|numeric',
            'images' => 'sometimes|array',
            'images.*' => 'url',
        ]);

        $product = Product::findOrFail($id);

        // Actualizar datos principales
        $product->update([
            'description' => $validated['description'],
            'price' => $validated['price'],
        ]);

        // Si hay imágenes nuevas, actualizarlas
        if (isset($validated['images'])) {
            // Borrar todas las imágenes anteriores
            $product->images()->delete();

            // Cargar las nuevas imágenes
            foreach ($validated['images'] as $url) {
                $product->images()->create(['url' => $url]);
            }
        }

        return response()->json(['message' => 'Producto actualizado con éxito']);
    }
}
