<?php

namespace App\Http\Controllers;
use Inertia\inertia;
use App\Models\Product;
use App\Models\Category;
use App\Models\ProductImage;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with('images', 'categories');

        // Filtro por categoría
        if ($request->has('category') && !empty($request->category)) {
            $query->whereHas('categories', function ($q) use ($request) {
                $q->where('categories.id', $request->category);
            });
        }

        // Filtro por búsqueda
        if ($request->has('search') && !empty($request->search)) {
            $query->where(function ($q) use ($request) {
                $q->where('description', 'like', '%' . $request->search . '%')
                ->orWhere('sku', 'like', '%' . $request->search . '%');
            });
        }
        $products = $query->orderBy('description')->paginate(25);

        $categories = Category::orderBy('name')->get();

        return Inertia::render('Home', [
            'products'   => $products,
            'categories' => $categories,
            'filters'    => $request->only('category'),
        ]);
    }
}
