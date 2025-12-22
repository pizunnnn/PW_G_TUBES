@extends('layouts.app')

@section('title', 'Transaction Detail')

@section('content')
<div class="max-w-4xl mx-auto px-4">
    <!-- Header --> 
    <div class="mb-8">
        <a href="{{ route('transactions.index') }}" class="text-purple-600 hover:text-purple-700 font-semibold mb-4 inline-block">
            <i class="fas fa-arrow-left"></i> Back to Transactions
        </a>
        <h1 class="text-4xl font-bold text-gray-900">
            <i class="fas fa-file-invoice text-purple-600"></i> Transaction Detail
        </h1>
    </div>

    <div class="space-y-6">
        <!-- Transaction Info -->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <p class="text-gray-600 text-sm mb-1">Transaction Code</p>
                    <p class="font-mono text-2xl font-bold text-gray-900">#{{ $transaction->transaction_code }}</p>
                </div>
                <div class="text-right">
                    {!! $transaction->status_badge !!}
                    <p class="text-sm text-gray-600 mt-2">{{ $transaction->created_at->format('d M Y, H:i') }}</p>
                </div>
            </div>
            
            <!-- Product Info -->
            <div class="border-t pt-6">
                <div class="flex items-center gap-4">
                    <div class="w-24 h-24 bg-gradient-to-br from-purple-400 to-indigo-600 rounded-lg flex items-center justify-center flex-shrink-0">
                        @if($transaction->product->image)
                            <img src="{{ asset('storage/' . $transaction->product->image) }}" 
                                 alt="{{ $transaction->product->name }}" 
                                 class="w-full h-full object-cover rounded-lg">
                        @else
                            <i class="fas fa-gamepad text-3xl text-white"></i>
                        @endif
                    </div>
                    <div class="flex-1">
                        <h3 class="font-bold text-gray-900 text-xl mb-1">{{ $transaction->product->name }}</h3>
                        <p class="text-gray-600">{{ $transaction->product->category->name }}</p>
                        <p class="text-sm text-gray-600 mt-2">
                            <i class="fas fa-cubes"></i> Quantity: <span class="font-semibold">{{ $transaction->quantity }} pcs</span>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment & Price Summary -->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-4">
                <i class="fas fa-credit-card"></i> Payment Details
            </h2>
            
            <div class="space-y-3">
                <div class="flex justify-between text-gray-700">
                    <span>Payment Method</span>
                    <span class="font-semibold capitalize">{{ str_replace('_', ' ', $transaction->payment_method) }}</span>
                </div>
                
                @if($transaction->paid_at)
                    <div class="flex justify-between text-gray-700">
                        <span>Paid At</span>
                        <span class="font-semibold">{{ $transaction->paid_at->format('d M Y, H:i') }}</span>
                    </div>
                @endif
                
                <div class="border-t pt-3 mt-3">
                    <div class="flex justify-between text-gray-700 mb-2">
                        <span>Subtotal ({{ $transaction->quantity }} items)</span>
                        <span>{{ $transaction->total_price_formatted }}</span>
                    </div>
                    
                    <div class="flex justify-between text-lg font-bold text-gray-900 pt-3 border-t">
                        <span>Total</span>
                        <span class="text-purple-600">{{ $transaction->total_price_formatted }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Voucher Codes -->
        @if($transaction->payment_status === 'paid')
            <div class="bg-gradient-to-r from-green-50 to-emerald-50 rounded-xl shadow-lg p-6 border-2 border-green-200">
                <div class="flex items-center gap-2 mb-4">
                    <i class="fas fa-ticket text-2xl text-green-600"></i>
                    <h2 class="text-xl font-bold text-gray-900">Your Voucher Codes</h2>
                </div>
                
                @if($transaction->voucherCodes->count() > 0)
                    <div class="space-y-3">
                        @foreach($transaction->voucherCodes as $voucher)
                            <div class="bg-white rounded-lg p-4 border border-green-300" x-data="{ copied: false }">
                                <div class="flex items-center justify-between">
                                    <div class="flex-1">
                                        <p class="text-sm text-gray-600 mb-1">Voucher Code #{{ $loop->iteration }}</p>
                                        <code class="text-xl font-mono font-bold text-gray-900 select-all">{{ $voucher->code }}</code>
                                    </div>
                                    <button @click="
                                        navigator.clipboard.writeText('{{ $voucher->code }}');
                                        copied = true;
                                        setTimeout(() => copied = false, 2000);
                                    " class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition font-semibold">
                                        <span x-show="!copied"><i class="fas fa-copy"></i> Copy</span>
                                        <span x-show="copied" x-cloak><i class="fas fa-check"></i> Copied!</span>
                                    </button>
                                </div>
                                @if($voucher->is_used)
                                    <p class="text-sm text-gray-500 mt-2">
                                        <i class="fas fa-check-circle text-green-600"></i> Used on {{ $voucher->used_at->format('d M Y') }}
                                    </p>
                                @else
                                    <p class="text-sm text-green-600 mt-2">
                                        <i class="fas fa-circle-check"></i> Ready to use
                                    </p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                    
                    <div class="mt-4 p-3 bg-blue-50 rounded-lg text-sm text-blue-800">
                        <i class="fas fa-info-circle"></i> Save your voucher codes! You can always find them here or in your profile.
                    </div>
                @else
                    <p class="text-gray-600">Voucher codes are being generated...</p>
                @endif
            </div>
        @elseif($transaction->payment_status === 'pending')
            <div class="bg-yellow-50 rounded-xl shadow-lg p-6 border-2 border-yellow-200">
                <div class="flex items-center gap-3 mb-3">
                    <i class="fas fa-clock text-3xl text-yellow-600 animate-pulse"></i>
                    <h2 class="text-xl font-bold text-gray-900">Waiting for Payment</h2>
                </div>
                <p class="text-gray-700 mb-4">Please complete your payment to receive the voucher codes.</p>
                <button class="px-6 py-3 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition font-semibold">
                    <i class="fas fa-credit-card"></i> Pay Now
                </button>
            </div>
        @else
            <div class="bg-red-50 rounded-xl shadow-lg p-6 border-2 border-red-200">
                <div class="flex items-center gap-3">
                    <i class="fas fa-exclamation-circle text-3xl text-red-600"></i>
                    <div>
                        <h2 class="text-xl font-bold text-gray-900">Payment {{ ucfirst($transaction->payment_status) }}</h2>
                        <p class="text-gray-700">This transaction cannot be completed.</p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Download Invoice -->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <button class="w-full bg-gray-800 text-white py-3 rounded-lg hover:bg-gray-900 transition font-semibold">
                <i class="fas fa-file-pdf"></i> Download Invoice (PDF)
            </button>
            <p class="text-xs text-gray-500 text-center mt-2">PDF generation coming soon</p>
        </div>
    </div>
</div>
@endsection
