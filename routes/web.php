<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductImportController;
use App\Http\Controllers\CarritoController;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/home', [HomeController::class, 'index']);
Route::get('/carrito', [CarritoController::class, 'index']);

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/cargar_productos', [ProductController::class, 'index_cargar']);
    Route::post('/cargar_productos', [ProductController::class, 'store_cargar']);
    Route::get('/cargar_productos/search', [ProductController::class, 'search_products']);
    Route::delete('/cargar_productos/delete/{id}', [ProductController::class, 'destroy']);
    Route::put('/cargar_productos/update/{id}', [ProductController::class, 'update']);
    Route::post('/import-products', [ProductImportController::class, 'importUpdate']);

});

require __DIR__.'/auth.php';
