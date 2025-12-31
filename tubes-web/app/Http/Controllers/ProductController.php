<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::active()->inStock()->with('category');
        
        // Search
        if ($request->has('search') && $request->search) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
        }
        
        // Filter by category
        if ($request->has('category') && $request->category) {
            $query->where('category_id', $request->category);
        }
        
        // Sort
        $sortBy = $request->get('sort', 'latest');
        switch ($sortBy) {
            case 'price_asc':
                $query->orderBy('price', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('price', 'desc');
                break;
            case 'name':
                $query->orderBy('name', 'asc');
                break;
            case 'popular':
                $query->withCount('transactions')->orderBy('transactions_count', 'desc');
                break;
            default:
                $query->latest();
        }
        
        $products = $query->paginate(12)->withQueryString();
        $categories = Category::active()->withCount('products')->get();
        
        return view('products.index', compact('products', 'categories'));
    }
    
    public function show($slug)
    {
        $product = Product::active()->where('slug', $slug)->with('category')->firstOrFail();
        
        // Related products
        $relatedProducts = Product::active()
            ->inStock()
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->limit(4)
            ->get();
        
        return view('products.show', compact('product', 'relatedProducts'));
    }
    
    public function search(Request $request)
    {
        $query = $request->get('q');
        
        $products = Product::active()
            ->inStock()
            ->where('name', 'like', '%' . $query . '%')
            ->limit(5)
            ->get(['id', 'name', 'slug', 'price', 'image']);
        
        return response()->json($products);
    }
}
