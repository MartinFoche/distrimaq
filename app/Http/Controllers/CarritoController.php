<?php

namespace App\Http\Controllers;
use Inertia\inertia;
use Illuminate\Http\Request;


class CarritoController extends Controller
{
    
    public function index()
    {
        return Inertia::render('Carrito');
    }
}
