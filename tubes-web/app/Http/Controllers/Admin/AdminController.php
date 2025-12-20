<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function dashboard()
    {
        $totalUsers = User::where('role', 'user')->count();
        $totalCategories = Category::count();
        $totalProducts = Product::count();
        $totalTransactions = Transaction::count();
        $totalRevenue = Transaction::paid()->sum('total_price');
        
        // Recent transactions
        $recentTransactions = Transaction::with(['user', 'product'])
            ->latest()
            ->take(10)
            ->get();
        
        return view('admin.dashboard', compact(
            'totalUsers',
            'totalCategories',
            'totalProducts',
            'totalTransactions',
            'totalRevenue',
            'recentTransactions'
        ));
    }
}