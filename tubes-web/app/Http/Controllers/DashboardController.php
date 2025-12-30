<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $categories = Category::active()->withCount('products')->get();
        $products = Product::active()->inStock()->with('category')->latest()->get();
        
        return view('dashboard', compact('categories', 'products'));
    }
    
    // Method baru untuk filter by category
    public function filterByCategory(Category $category)
    {
        $categories = Category::active()->withCount('products')->get();
        $products = Product::active()
            ->inStock()
            ->with('category')
            ->where('category_id', $category->id)
            ->latest()
            ->get();
        
        return view('dashboard', compact('categories', 'products', 'category'));
    }
}