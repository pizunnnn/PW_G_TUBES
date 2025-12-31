@extends('layouts.app')

@section('title', 'Manage Transactions')

@section('content')
<div class="max-w-7xl mx-auto px-4">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800">
            <i class="fas fa-receipt"></i> Manage Transactions
        </h1>
        <a href="{{ route('admin.transactions.export-pdf') }}?status={{ $status }}&date_from={{ $dateFrom }}&date_to={{ $dateTo }}" 
           class="bg-red-600 text-white px-6 py-3 rounded-lg hover:bg-red-700 transition">
            <i class="fas fa-file-pdf"></i> Export PDF
        </a>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <input 
                type="text" 
                name="search" 
                value="{{ $search ?? '' }}" 
                placeholder="Search code, user, product..." 
                class="px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-600"
            >
            <select name="status" class="px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-600">
                <option value="">All Status</option>
                <option value="pending" {{ $status == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="paid" {{ $status == 'paid' ? 'selected' : '' }}>Paid</option>
                <option value="failed" {{ $status == 'failed' ? 'selected' : '' }}>Failed</option>
                <option value="expired" {{ $status == 'expired' ? 'selected' : '' }}>Expired</option>
            </select>
            <input 
                type="date" 
                name="date_from" 
                value="{{ $dateFrom ?? '' }}" 
                placeholder="From Date"
                class="px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-600"
            >
            <input 
                type="date" 
                name="date_to" 
                value="{{ $dateTo ?? '' }}" 
                placeholder="To Date"
                class="px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-600"
            >
            <div class="flex gap-2">
                <button type="submit" class="flex-1 bg-yellow-600 text-white px-6 py-2 rounded-lg hover:bg-yellow-700 transition">
                    <i class="fas fa-search"></i> Filter
                </button>
                @if($search || $status || $dateFrom || $dateTo)
                    <a href="{{ route('admin.transactions.index') }}" class="bg-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-400 transition">
                        <i class="fas fa-times"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm">Total Transactions</p>
                    <p class="text-3xl font-bold text-gray-800">{{ $transactions->total() }}</p>
                </div>
                <div class="bg-blue-100 rounded-full p-4">
                    <i class="fas fa-receipt text-3xl text-blue-600"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm">Paid</p>
                    <p class="text-3xl font-bold text-green-800">{{ \App\Models\Transaction::paid()->count() }}</p>
                </div>
                <div class="bg-green-100 rounded-full p-4">
                    <i class="fas fa-check-circle text-3xl text-green-600"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm">Pending</p>
                    <p class="text-3xl font-bold text-yellow-800">{{ \App\Models\Transaction::pending()->count() }}</p>
                </div>
                <div class="bg-yellow-100 rounded-full p-4">
                    <i class="fas fa-clock text-3xl text-yellow-600"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm">Failed</p>
                    <p class="text-3xl font-bold text-red-800">{{ \App\Models\Transaction::failed()->count() }}</p>
                </div>
                <div class="bg-red-100 rounded-full p-4">
                    <i class="fas fa-times-circle text-3xl text-red-600"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Code</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">User</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Product</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Qty</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($transactions as $t)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 font-mono text-sm font-semibold text-gray-900">{{ $t->transaction_code }}</td>
                    <td class="px-6 py-4">
                        <div class="text-sm font-semibold text-gray-900">{{ $t->user->name }}</div>
                        <div class="text-xs text-gray-500">{{ $t->user->email }}</div>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ $t->product->name }}</td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ $t->quantity }}x</td>
                    <td class="px-6 py-4 font-semibold text-gray-900">{{ $t->total_price_formatted }}</td>
                    <td class="px-6 py-4">
                        @if($t->payment_status === 'paid')
                            <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-xs font-semibold">
                                <i class="fas fa-check-circle"></i> Paid
                            </span>
                        @elseif($t->payment_status === 'pending')
                            <span class="px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs font-semibold">
                                <i class="fas fa-clock"></i> Pending
                            </span>
                        @elseif($t->payment_status === 'failed')
                            <span class="px-3 py-1 bg-red-100 text-red-800 rounded-full text-xs font-semibold">
                                <i class="fas fa-times-circle"></i> Failed
                            </span>
                        @else
                            <span class="px-3 py-1 bg-gray-100 text-gray-800 rounded-full text-xs font-semibold">
                                {{ ucfirst($t->payment_status) }}
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ $t->created_at->format('d M Y H:i') }}</td>
                    <td class="px-6 py-4">
                        <a href="{{ route('admin.transactions.show', $t) }}" 
                           class="text-blue-600 hover:text-blue-800 font-semibold">
                            <i class="fas fa-eye"></i> View
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-6 py-12 text-center text-gray-500">
                        <i class="fas fa-inbox text-4xl mb-2"></i>
                        <p>No transactions found</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-6">
        {{ $transactions->links() }}
    </div>
</div>
@endsection