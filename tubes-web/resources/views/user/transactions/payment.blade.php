@extends('layouts.app')

@section('title', 'Payment')

@section('content')
<div class="max-w-2xl mx-auto px-4 py-8">
    <div class="bg-white rounded-lg shadow-lg p-8">
        <div class="text-center mb-6">
            <div class="bg-purple-100 rounded-full w-20 h-20 flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-credit-card text-4xl text-purple-600"></i>
            </div>
            <h1 class="text-2xl font-bold text-gray-800 mb-2">Complete Your Payment</h1>
            <p class="text-gray-600">Selesaikan pembayaran untuk mendapatkan voucher code</p>
        </div>

        <!-- Transaction Details -->
        <div class="bg-gray-50 rounded-lg p-6 mb-6">
            <h3 class="font-bold text-gray-800 mb-4">Transaction Details</h3>
            
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-gray-600">Transaction Code:</span>
                    <span class="font-mono font-semibold text-gray-900">{{ $transaction->transaction_code }}</span>
                </div>
                
                <div class="flex justify-between">
                    <span class="text-gray-600">Product:</span>
                    <span class="font-semibold text-gray-900">{{ $transaction->product->name }}</span>
                </div>
                
                <div class="flex justify-between">
                    <span class="text-gray-600">Quantity:</span>
                    <span class="font-semibold text-gray-900">{{ $transaction->quantity }}x</span>
                </div>
                
                <div class="flex justify-between">
                    <span class="text-gray-600">Price per Item:</span>
                    <span class="font-semibold text-gray-900">{{ $transaction->product->price_formatted }}</span>
                </div>
                
                <div class="border-t pt-3 mt-3">
                    <div class="flex justify-between items-center">
                        <span class="text-lg font-bold text-gray-800">Total Payment:</span>
                        <span class="text-2xl font-bold text-purple-600">{{ $transaction->total_price_formatted }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment Info -->
        <div class="bg-blue-50 border-l-4 border-blue-600 p-4 mb-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-info-circle text-blue-600 text-xl"></i>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-semibold text-blue-800">What happens next?</h3>
                    <ul class="text-sm text-blue-700 mt-2 list-disc list-inside space-y-1">
                        <li>Click "Pay Now" button below</li>
                        <li>Choose your preferred payment method</li>
                        <li>Complete the payment</li>
                        <li>Voucher codes will be sent to your email automatically</li>
                        <li>You can also view codes in "My Transactions" page</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Pay Button -->
        <button 
            id="pay-button" 
            class="w-full bg-gradient-to-r from-purple-600 to-indigo-600 text-white py-4 rounded-lg text-lg font-bold hover:from-purple-700 hover:to-indigo-700 transition shadow-lg hover:shadow-xl">
            <i class="fas fa-lock mr-2"></i> Pay Now - {{ $transaction->total_price_formatted }}
        </button>

        <p class="text-center text-sm text-gray-500 mt-4">
            <i class="fas fa-shield-alt"></i> Secure payment powered by Midtrans
        </p>

        <!-- Cancel Link -->
        <div class="text-center mt-4">
            <a href="{{ route('user.transactions.index') }}" class="text-gray-600 hover:text-gray-800 text-sm">
                Cancel and go back
            </a>
        </div>
    </div>
</div>

<!-- Midtrans Snap JS -->
<script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}"></script>

<script>
    const payButton = document.getElementById('pay-button');
    
    payButton.addEventListener('click', function () {
        // Disable button to prevent double click
        payButton.disabled = true;
        payButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Processing...';
        
        window.snap.pay('{{ $snapToken }}', {
            onSuccess: function(result) {
                console.log('Payment success:', result);
                window.location.href = '{{ route("user.transactions.show", $transaction) }}';
            },
            onPending: function(result) {
                console.log('Payment pending:', result);
                window.location.href = '{{ route("user.transactions.show", $transaction) }}';
            },
            onError: function(result) {
                console.error('Payment error:', result);
                alert('Payment failed! Please try again.');
                
                // Re-enable button
                payButton.disabled = false;
                payButton.innerHTML = '<i class="fas fa-lock mr-2"></i> Pay Now - {{ $transaction->total_price_formatted }}';
            },
            onClose: function() {
                console.log('Payment popup closed');
                
                // Re-enable button
                payButton.disabled = false;
                payButton.innerHTML = '<i class="fas fa-lock mr-2"></i> Pay Now - {{ $transaction->total_price_formatted }}';
            }
        });
    });
</script>
@endsection