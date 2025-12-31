@extends('layouts.app')

@section('title', 'User Detail')

@section('content')
<div class="max-w-7xl mx-auto px-4">
    <div class="mb-6">
        <a href="{{ route('admin.users.index') }}" class="text-blue-600 hover:text-blue-800">
            <i class="fas fa-arrow-left"></i> Back to Users
        </a>
    </div>

    <!-- User Info Card -->
    <div class="bg-white rounded-lg shadow p-8 mb-6">
        <div class="flex items-start justify-between mb-6">
            <div class="flex items-center">
                <div class="w-20 h-20 bg-gradient-to-br from-purple-400 to-indigo-600 rounded-full flex items-center justify-center text-white text-3xl font-bold mr-4">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">{{ $user->name }}</h1>
                    @if($user->isAdmin())
                        <span class="px-3 py-1 bg-purple-100 text-purple-800 rounded-full text-sm font-semibold">
                            <i class="fas fa-crown"></i> Admin
                        </span>
                    @else
                        <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm font-semibold">
                            <i class="fas fa-user"></i> User
                        </span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Contact Info -->
        <div class="border-t pt-6">
            <h3 class="font-bold text-gray-800 mb-4">Contact Information</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <p class="text-gray-600 text-sm mb-1">
                        <i class="fas fa-envelope text-gray-400 mr-2"></i> Email
                    </p>
                    <p class="font-semibold text-gray-900">{{ $user->email }}</p>
                </div>
                <div>
                    <p class="text-gray-600 text-sm mb-1">
                        <i class="fas fa-phone text-gray-400 mr-2"></i> Phone Number
                    </p>
                    <p class="font-semibold text-gray-900">{{ $user->phone ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-gray-600 text-sm mb-1">
                        <i class="fas fa-calendar text-gray-400 mr-2"></i> Registered Date
                    </p>
                    <p class="font-semibold text-gray-900">{{ $user->created_at->format('d F Y H:i') }}</p>
                </div>
                <div>
                    <p class="text-gray-600 text-sm mb-1">
                        <i class="fas fa-clock text-gray-400 mr-2"></i> Last Updated
                    </p>
                    <p class="font-semibold text-gray-900">{{ $user->updated_at->diffForHumans() }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm">Total Transactions</p>
                    <p class="text-3xl font-bold text-blue-600">{{ $stats['total_transactions'] }}</p>
                </div>
                <div class="bg-blue-100 rounded-full p-3">
                    <i class="fas fa-receipt text-2xl text-blue-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm">Total Spent</p>
                    <p class="text-xl font-bold text-green-600">Rp {{ number_format($stats['total_spent'], 0, ',', '.') }}</p>
                </div>
                <div class="bg-green-100 rounded-full p-3">
                    <i class="fas fa-wallet text-2xl text-green-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm">Completed</p>
                    <p class="text-3xl font-bold text-purple-600">{{ $stats['completed_transactions'] }}</p>
                </div>
                <div class="bg-purple-100 rounded-full p-3">
                    <i class="fas fa-check-circle text-2xl text-purple-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm">Pending</p>
                    <p class="text-3xl font-bold text-yellow-600">{{ $stats['pending_transactions'] }}</p>
                </div>
                <div class="bg-yellow-100 rounded-full p-3">
                    <i class="fas fa-clock text-2xl text-yellow-600"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Transaction History -->
    <div class="bg-white rounded-lg shadow">
        <div class="p-6 border-b">
            <h2 class="text-xl font-bold text-gray-800">
                <i class="fas fa-history"></i> Transaction History
            </h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Transaction Code</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Product</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Quantity</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($user->transactions as $transaction)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 font-mono text-sm">{{ $transaction->transaction_code }}</td>
                            <td class="px-6 py-4 text-sm text-gray-900">{{ $transaction->product->name }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $transaction->quantity }}x</td>
                            <td class="px-6 py-4 font-semibold text-gray-900">{{ $transaction->total_price_formatted }}</td>
                            <td class="px-6 py-4">
                                @if($transaction->payment_status === 'paid')
                                    <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs font-semibold">Paid</span>
                                @elseif($transaction->payment_status === 'pending')
                                    <span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs font-semibold">Pending</span>
                                @else
                                    <span class="px-2 py-1 bg-red-100 text-red-800 rounded-full text-xs font-semibold">{{ ucfirst($transaction->payment_status) }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $transaction->created_at->format('d M Y') }}</td>
                            <td class="px-6 py-4">
                                <a href="{{ route('admin.transactions.show', $transaction) }}" class="text-blue-600 hover:text-blue-800">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-gray-500">
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