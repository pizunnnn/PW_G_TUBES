@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<div class="max-w-7xl mx-auto px-4">
    <h1 class="text-3xl font-bold mb-8 text-gray-800">
        <i class="fas fa-gauge"></i> Admin Dashboard
    </h1>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Total Users -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm">Total Users</p>
                    <p class="text-3xl font-bold text-gray-800">{{ $totalUsers }}</p>
                </div>
                <div class="bg-blue-100 rounded-full p-4">
                    <i class="fas fa-users text-3xl text-blue-600"></i>
                </div>
            </div>
        </div>

        <!-- Total Categories -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm">Total Categories</p>
                    <p class="text-3xl font-bold text-gray-800">{{ $totalCategories }}</p>
                </div>
                <div class="bg-green-100 rounded-full p-4">
                    <i class="fas fa-layer-group text-3xl text-green-600"></i>
                </div>
            </div>
        </div>

        <!-- Total Products -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm">Total Products</p>
                    <p class="text-3xl font-bold text-gray-800">{{ $totalProducts }}</p>
                </div>
                <div class="bg-purple-100 rounded-full p-4">
                    <i class="fas fa-box text-3xl text-purple-600"></i>
                </div>
            </div>
        </div>

        <!-- Total Revenue -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm">Total Revenue</p>
                    <p class="text-2xl font-bold text-gray-800">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</p>
                </div>
                <div class="bg-yellow-100 rounded-full p-4">
                    <i class="fas fa-dollar-sign text-3xl text-yellow-600"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
<div class="mb-8">
    <h2 class="text-xl font-bold mb-4 text-gray-800">Quick Actions</h2>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <a href="{{ route('admin.categories.index') }}" class="bg-green-100 p-6 rounded-lg text-center hover:bg-green-200 transition">
            <i class="fas fa-layer-group text-4xl text-green-600 mb-2"></i>
            <p class="font-semibold text-gray-800">Manage Categories</p>
        </a>
        <a href="#" class="bg-blue-100 p-6 rounded-lg text-center hover:bg-blue-200 transition">
            <i class="fas fa-box text-4xl text-blue-600 mb-2"></i>
            <p class="font-semibold text-gray-800">Manage Products</p>
        </a>
        <a href="#" class="bg-yellow-100 p-6 rounded-lg text-center hover:bg-yellow-200 transition">
            <i class="fas fa-receipt text-4xl text-yellow-600 mb-2"></i>
            <p class="font-semibold text-gray-800">Transactions</p>
        </a>
        <a href="#" class="bg-purple-100 p-6 rounded-lg text-center hover:bg-purple-200 transition">
            <i class="fas fa-users text-4xl text-purple-600 mb-2"></i>
            <p class="font-semibold text-gray-800">Users</p>
        </a>
    </div>
</div>

    <!-- Recent Transactions -->
    <div class="bg-white rounded-lg shadow">
        <div class="p-6 border-b">
            <h2 class="text-xl font-bold text-gray-800">
                <i class="fas fa-receipt"></i> Recent Transactions
            </h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Transaction Code</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">User</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Product</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($recentTransactions as $transaction)
                        <tr>
                            <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $transaction->transaction_code }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $transaction->user->name }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $transaction->product->name }}</td>
                            <td class="px-6 py-4 text-sm font-semibold text-gray-900">{{ $transaction->total_price_formatted }}</td>
                            <td class="px-6 py-4 text-sm">
                                @if($transaction->payment_status === 'paid')
                                    <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs font-semibold">Paid</span>
                                @elseif($transaction->payment_status === 'pending')
                                    <span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs font-semibold">Pending</span>
                                @else
                                    <span class="px-2 py-1 bg-red-100 text-red-800 rounded-full text-xs font-semibold">{{ ucfirst($transaction->payment_status) }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $transaction->created_at->format('d M Y') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                <i class="fas fa-inbox text-4xl mb-2"></i>
                                <p>No transactions yet</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection