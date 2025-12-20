<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $categories = Category::active()->get();
        $products = Product::active()->inStock()->with('category')->latest()->get();
        
        return view('dashboard', compact('categories', 'products'));
    }
}