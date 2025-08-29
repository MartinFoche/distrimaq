<?php

namespace App\Http\Controllers;
use Inertia\inertia;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index() {
    $products = Product::with('images', 'categories')
            ->limit(10)
            ->get();
    return Inertia::render('Home', [
        'products' => $products,
    ]);
}
}
