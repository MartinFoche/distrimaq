<?php

namespace App\Http\Controllers;
use Inertia\inertia;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index() {
        return Inertia::render('Home');
    }
}
