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
        // Statistics
        $totalUsers = User::where('role', 'user')->count();
        $totalGames = Category::count();
        $totalProducts = Product::count();
        $totalRevenue = Transaction::where('payment_status', 'paid')->sum('total_price');
        
        // Recent transactions
        $recentTransactions = Transaction::with(['user', 'product'])
            ->latest()
            ->take(10)
            ->get();
        
        // Monthly revenue data for chart
        $monthlyRevenue = Transaction::where('payment_status', 'paid')
            ->whereYear('created_at', date('Y'))
            ->selectRaw("CAST(strftime('%m', created_at) AS INTEGER) as month, SUM(total_price) as total")
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->pluck('total', 'month')
            ->toArray();
        
        // Fill missing months with 0
        $chartData = [];
        for ($i = 1; $i <= 12; $i++) {
            $chartData[] = $monthlyRevenue[$i] ?? 0;
        }
        
        // Transaction status breakdown
        $transactionStats = [
            'pending' => Transaction::pending()->count(),
            'paid' => Transaction::paid()->count(),
            'failed' => Transaction::failed()->count(),
        ];

        return view('admin.dashboard', compact(
            'totalUsers',
            'totalGames',
            'totalProducts',
            'totalRevenue',
            'recentTransactions',
            'chartData',
            'transactionStats'
        ));
    }

    public function users()
    {
        // Get all users with their transaction data
        $users = User::withCount('transactions')
            ->with(['transactions' => function($query) {
                $query->where('payment_status', 'paid');
            }])
            ->latest()
            ->get();
        
        // Calculate total spent for each user
        $users->each(function($user) {
            $user->total_spent = $user->transactions
                ->where('payment_status', 'paid')
                ->sum('total_price');
        });
        
        return view('admin.users', compact('users'));
    }
}