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
        if ($request->has('category') && !empty($request->category)) {
            $query->whereHas('categories', function ($q) use ($request) {
                $q->where('categories.id', $request->category);
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
