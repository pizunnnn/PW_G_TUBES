<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Services\RawgService;

class DashboardController extends Controller
{
    public function index(RawgService $rawgService)
    {
        $categories = Category::active()
        ->withCount('products')
        ->where('is_active', true)
        ->get();
        $products = Product::active()
        ->with('category')
        ->where('is_active', true)
        ->inStock()
        ->latest()
        ->get();

        $popularGames = $rawgService->getPopularGames();
        
        return view('dashboard', compact(
            'categories',
            'products',
        'popularGames'
    ));
    }
    
    // Method baru untuk filter by category
    public function filterByCategory(Category $category)
    {
        $categories = Category::active()
        ->withCount('products')
        ->where('is_active', true)
        ->get();
        $products = Product::active()
            ->inStock()
            ->with('category')
            ->where('category_id', $category->id)
            ->latest()
            ->get();

        $popularGames = $rawgService->getPopularGames();
        
        return view('dashboard', compact(
            'categories',
            'products',
            'category',
            'popularGames'
        ));
    }
}