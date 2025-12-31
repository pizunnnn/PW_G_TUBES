@extends('layouts.app')

@section('title', 'Manage Voucher Codes')

@section('content')
<div class="max-w-7xl mx-auto px-4">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800">
            <i class="fas fa-ticket"></i> Manage Voucher Codes
        </h1>
        <a href="{{ route('admin.voucher-codes.create') }}" class="bg-gradient-to-r from-green-600 to-teal-600 text-white px-6 py-3 rounded-lg hover:from-green-700 hover:to-teal-700 transition">
            <i class="fas fa-plus"></i> Generate Codes
        </a>
    </div>

    <!-- Search & Filter -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <form method="GET" action="{{ route('admin.voucher-codes.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <input 
                type="text" 
                name="search" 
                value="{{ $search ?? '' }}"
                placeholder="Search by code..." 
                class="px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-green-600"
            >
            <select name="product" class="px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-green-600">
                <option value="">All Products</option>
                @foreach($products as $prod)
                    <option value="{{ $prod->id }}" {{ $product == $prod->id ? 'selected' : '' }}>
                        {{ $prod->name }}
                    </option>
                @endforeach
            </select>
            <select name="status" class="px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-green-600">
                <option value="">All Status</option>
                <option value="unused" {{ $status == 'unused' ? 'selected' : '' }}>Unused</option>
                <option value="used" {{ $status == 'used' ? 'selected' : '' }}>Used</option>
            </select>
            <div class="flex gap-2 md:col-span-3">
                <button type="submit" class="flex-1 bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700 transition">
                    <i class="fas fa-search"></i> Search
                </button>
                @if($search || $product || $status)
                    <a href="{{ route('admin.voucher-codes.index') }}" class="bg-gray-300 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-400 transition">
                        <i class="fas fa-times"></i> Clear
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm">Total Codes</p>
                    <p class="text-3xl font-bold text-gray-800">{{ $voucherCodes->total() }}</p>
                </div>
                <div class="bg-blue-100 rounded-full p-4">
                    <i class="fas fa-ticket text-3xl text-blue-600"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm">Unused Codes</p>
                    <p class="text-3xl font-bold text-green-800">{{ \App\Models\VoucherCode::unused()->count() }}</p>
                </div>
                <div class="bg-green-100 rounded-full p-4">
                    <i class="fas fa-check-circle text-3xl text-green-600"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm">Used Codes</p>
                    <p class="text-3xl font-bold text-red-800">{{ \App\Models\VoucherCode::used()->count() }}</p>
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
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Product</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Transaction</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">User</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Created</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($voucherCodes as $code)
                    <tr>
                        <td class="px-6 py-4">
                            <span class="font-mono font-bold text-gray-900">{{ $code->code }}</span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            {{ $code->product->name }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            @if($code->transaction)
                                <a href="{{ route('admin.transactions.show', $code->transaction) }}" class="text-blue-600 hover:text-blue-800">
                                    {{ $code->transaction->transaction_code }}
                                </a>
                            @else
                                <span class="text-gray-400">Manual</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            @if($code->transaction)
                                {{ $code->transaction->user->name }}
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @if($code->is_used)
                                <span class="px-3 py-1 bg-red-100 text-red-800 rounded-full text-xs font-semibold">
                                    <i class="fas fa-times-circle"></i> Used
                                </span>
                                @if($code->used_at)
                                    <p class="text-xs text-gray-500 mt-1">{{ $code->used_at->format('d M Y H:i') }}</p>
                                @endif
                            @else
                                <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-xs font-semibold">
                                    <i class="fas fa-check-circle"></i> Available
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            {{ $code->created_at->format('d M Y') }}
                        </td>
                        <td class="px-6 py-4">
                            @if(!$code->is_used && !$code->transaction_id)
                                <form action="{{ route('admin.voucher-codes.destroy', $code) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure? This action cannot be undone!')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            @else
                                <span class="text-gray-400" title="Cannot delete used or assigned codes">
                                    <i class="fas fa-lock"></i>
                                </span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                            <i class="fas fa-inbox text-4xl mb-2"></i>
                            <p>No voucher codes found</p>
                            <a href="{{ route('admin.voucher-codes.create') }}" class="text-blue-600 hover:text-blue-800 mt-2 inline-block">
                                Generate your first codes
                            </a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-6">
        {{ $voucherCodes->links() }}
    </div>
</div>
@endsection