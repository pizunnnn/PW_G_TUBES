@extends('layouts.app')

@section('title', 'My Transactions')

@section('content')
<div class="max-w-7xl mx-auto px-4">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800">
            <i class="fas fa-receipt"></i> My Transactions
        </h1>
        <a href="{{ route('home') }}" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition">
            <i class="fas fa-shopping-cart"></i> Continue Shopping
        </a>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Transaction Code</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Product</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Game Info (API)</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Qty</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-gray-200">
            @forelse($transactions as $transaction)
                <tr>
                    <td class="px-6 py-4 font-mono font-semibold">
                        {{ $transaction->transaction_code }}
                    </td>

                    <td class="px-6 py-4">
                        <div class="flex items-center">
                            @if($transaction->product->image)
                                <img src="{{ asset('storage/' . $transaction->product->image) }}"
                                     class="w-12 h-12 object-cover rounded mr-3">
                            @endif
                            <div>
                                <p class="font-semibold">{{ $transaction->product->name }}</p>
                                <p class="text-xs text-gray-500">
                                    {{ $transaction->product->category->name }}
                                </p>
                            </div>
                        </div>
                    </td>

                    <td class="px-6 py-4 text-sm">{{ $transaction->quantity }}x</td>

                    <td class="px-6 py-4 font-semibold">
                        {{ $transaction->total_price_formatted }}
                    </td>

                    <td class="px-6 py-4">
                        @if($transaction->payment_status === 'paid')
                            <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-xs">
                                ✔ Paid
                            </span>
                        @elseif($transaction->payment_status === 'pending')
                            <span class="px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs">
                                ⏳ Pending
                            </span>
                        @else
                            <span class="px-3 py-1 bg-red-100 text-red-800 rounded-full text-xs">
                                ✖ Failed
                            </span>
                        @endif
                    </td>

                    <td class="px-6 py-4 text-sm text-gray-600">
                        {{ $transaction->created_at->format('d M Y H:i') }}
                    </td>

                    <td class="px-6 py-4">
                        <a href="{{ route('user.transactions.show', $transaction) }}"
                           class="text-blue-600 hover:text-blue-800 font-semibold">
                            <i class="fas fa-eye"></i> View
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center py-12 text-gray-500">
                        You haven't made any transactions yet
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $transactions->links() }}
    </div>
</div>
@endsection
