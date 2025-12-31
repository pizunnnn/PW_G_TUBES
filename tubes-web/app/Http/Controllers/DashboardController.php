<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\Slider;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $categories = Category::active()->withCount('products')->get();
        $products = Product::active()->inStock()->with('category')->latest()->paginate(12);

        // Get recent paid transactions for running text
        $recentTransactions = Transaction::paid()
            ->with(['user', 'product'])
            ->latest('paid_at')
            ->limit(15)
            ->get();

        // Get active sliders
        $sliders = Slider::active()->ordered()->with('product')->get();

        return view('products.index', compact('categories', 'products', 'recentTransactions', 'sliders'));
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
            ->paginate(12);

        // Get recent paid transactions for running text
        $recentTransactions = Transaction::paid()
            ->with(['user', 'product'])
            ->latest('paid_at')
            ->limit(15)
            ->get();

        // Get active sliders
        $sliders = Slider::active()->ordered()->with('product')->get();

        return view('products.index', compact('categories', 'products', 'category', 'recentTransactions', 'sliders'));
    }
}