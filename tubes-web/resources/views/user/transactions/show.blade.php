@extends('layouts.app')

@section('title', 'Transaction Detail')

@section('content')
<div class="max-w-4xl mx-auto px-4">
    <div class="mb-6">
        <a href="{{ route('user.transactions.index') }}" class="text-blue-600 hover:text-blue-800">
            <i class="fas fa-arrow-left"></i> Back to My Transactions
        </a>
    </div>

    <!-- Transaction Info Card -->
    <div class="bg-white rounded-lg shadow p-8 mb-6">
        <div class="flex justify-between items-start mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-800 mb-2">Transaction Detail</h1>
                <p class="font-mono text-gray-600">{{ $transaction->transaction_code }}</p>
            </div>
            <div>
                @if($transaction->payment_status === 'paid')
                    <span class="px-4 py-2 bg-green-100 text-green-800 rounded-full text-sm font-semibold">
                        <i class="fas fa-check-circle"></i> Paid
                    </span>
                @elseif($transaction->payment_status === 'pending')
                    <span class="px-4 py-2 bg-yellow-100 text-yellow-800 rounded-full text-sm font-semibold">
                        <i class="fas fa-clock"></i> Pending Payment
                    </span>
                @else
                    <span class="px-4 py-2 bg-red-100 text-red-800 rounded-full text-sm font-semibold">
                        <i class="fas fa-times-circle"></i> {{ ucfirst($transaction->payment_status) }}
                    </span>
                @endif
            </div>
        </div>

        <!-- Product Info -->
        <div class="border-t pt-6 mb-6">
            <h3 class="font-bold text-gray-800 mb-4">Product Information</h3>
            <div class="flex items-center mb-4">
                @if($transaction->product->image)
                    <img src="{{ asset('storage/' . $transaction->product->image) }}" 
                         alt="{{ $transaction->product->name }}" 
                         class="w-24 h-24 object-cover rounded-lg mr-4">
                @endif
                <div>
                    <h4 class="font-bold text-lg text-gray-900">{{ $transaction->product->name }}</h4>
                    <p class="text-gray-600">{{ $transaction->product->category->name }}</p>
                    <p class="text-gray-500 text-sm mt-1">{{ $transaction->product->description }}</p>
                </div>
            </div>
        </div>

        <!-- Payment Details -->
        <div class="border-t pt-6">
            <h3 class="font-bold text-gray-800 mb-4">Payment Details</h3>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <p class="text-gray-600 text-sm">Price per Item</p>
                    <p class="font-semibold text-gray-900">{{ $transaction->product->price_formatted }}</p>
                </div>
                <div>
                    <p class="text-gray-600 text-sm">Quantity</p>
                    <p class="font-semibold text-gray-900">{{ $transaction->quantity }}x</p>
                </div>
                <div>
                    <p class="text-gray-600 text-sm">Payment Method</p>
                    <p class="font-semibold text-gray-900">{{ $transaction->payment_method ?? 'Pending' }}</p>
                </div>
                <div>
                    <p class="text-gray-600 text-sm">Transaction Date</p>
                    <p class="font-semibold text-gray-900">{{ $transaction->created_at->format('d M Y H:i') }}</p>
                </div>
                @if($transaction->paid_at)
                <div>
                    <p class="text-gray-600 text-sm">Payment Date</p>
                    <p class="font-semibold text-gray-900">{{ $transaction->paid_at->format('d M Y H:i') }}</p>
                </div>
                @endif
            </div>
            <div class="border-t mt-4 pt-4">
                <div class="flex justify-between items-center">
                    <p class="text-lg font-bold text-gray-800">Total Payment</p>
                    <p class="text-2xl font-bold text-blue-600">{{ $transaction->total_price_formatted }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Voucher Codes Section -->
    @if($transaction->payment_status === 'paid' && $transaction->voucherCodes->count() > 0)
    <div class="bg-gradient-to-br from-green-50 to-teal-50 rounded-lg shadow-lg p-8 border-2 border-green-200">
        <div class="flex items-center mb-6">
            <div class="bg-green-600 rounded-full p-3 mr-4">
                <i class="fas fa-ticket text-white text-2xl"></i>
            </div>
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Your Voucher Codes</h2>
                <p class="text-gray-600">These codes have been sent to your email: <strong>{{ $transaction->user->email }}</strong></p>
            </div>
        </div>

        <div class="bg-white rounded-lg p-6">
            <div class="space-y-4">
                @foreach($transaction->voucherCodes as $code)
                <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border-2 border-dashed border-blue-300 rounded-lg p-5 flex justify-between items-center hover:shadow-md transition">
                    <div class="flex-1">
                        <p class="text-sm text-gray-600 mb-1">Code #{{ $loop->iteration }}</p>
                        <div class="flex items-center">
                            <code class="text-2xl font-bold font-mono text-gray-900 tracking-wider" id="code-{{ $code->id }}">
                                {{ $code->code }}
                            </code>
                            @if($code->is_used)
                                <span class="ml-3 px-3 py-1 bg-red-100 text-red-800 rounded-full text-xs font-semibold">
                                    <i class="fas fa-times-circle"></i> Used
                                </span>
                            @endif
                        </div>
                        @if($code->used_at)
                            <p class="text-xs text-gray-500 mt-1">Used on: {{ $code->used_at->format('d M Y H:i') }}</p>
                        @endif
                    </div>
                    @if(!$code->is_used)
                    <button 
                        onclick="copyCode('{{ $code->code }}', {{ $code->id }})"
                        class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition flex items-center gap-2">
                        <i class="fas fa-copy"></i>
                        <span class="copy-text-{{ $code->id }}">Copy</span>
                    </button>
                    @endif
                </div>
                @endforeach
            </div>

            <div class="mt-6 bg-yellow-50 border-l-4 border-yellow-600 p-4 rounded">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-info-circle text-yellow-600 text-xl"></i>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-semibold text-yellow-800">How to Use</h3>
                        <ol class="text-sm text-yellow-700 mt-2 list-decimal list-inside space-y-1">
                            <li>Copy your voucher code by clicking the "Copy" button</li>
                            <li>Open {{ $transaction->product->name }}</li>
                            <li>Go to the redeem/voucher section</li>
                            <li>Paste and redeem your code</li>
                            <li>Enjoy your purchase! 🎮</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @elseif($transaction->payment_status === 'pending')
    <div class="bg-yellow-50 border-l-4 border-yellow-600 p-6 rounded-lg">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fas fa-clock text-yellow-600 text-2xl"></i>
            </div>
            <div class="ml-3">
                <h3 class="text-lg font-semibold text-yellow-800">Payment Pending</h3>
                <p class="text-yellow-700 mt-2">Your payment is still being processed. Voucher codes will be generated after successful payment.</p>
                <p class="text-sm text-yellow-600 mt-2">Check your email for payment instructions, or contact support if you've already paid.</p>
            </div>
        </div>
    </div>
    @else
    <div class="bg-red-50 border-l-4 border-red-600 p-6 rounded-lg">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fas fa-times-circle text-red-600 text-2xl"></i>
            </div>
            <div class="ml-3">
                <h3 class="text-lg font-semibold text-red-800">Transaction {{ ucfirst($transaction->payment_status) }}</h3>
                <p class="text-red-700 mt-2">This transaction did not complete successfully. No voucher codes were generated.</p>
                <a href="{{ route('home') }}" class="text-red-600 hover:text-red-800 font-semibold mt-2 inline-block">
                    Try purchasing again →
                </a>
            </div>
        </div>
    </div>
    @endif
</div>

<script>
function copyCode(code, id) {
    // Copy to clipboard
    navigator.clipboard.writeText(code).then(function() {
        // Change button text
        const btn = document.querySelector('.copy-text-' + id);
        btn.textContent = 'Copied!';
        btn.parentElement.classList.add('bg-green-600');
        btn.parentElement.classList.remove('bg-blue-600');
        
        // Reset after 2 seconds
        setTimeout(function() {
            btn.textContent = 'Copy';
            btn.parentElement.classList.remove('bg-green-600');
            btn.parentElement.classList.add('bg-blue-600');
        }, 2000);
    }, function(err) {
        alert('Failed to copy: ' + err);
    });
}
</script>
@endsection