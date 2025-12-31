@extends('layouts.app')

@section('title', 'Transaction Detail')

@section('content')
<div class="max-w-4xl mx-auto px-4">
    <div class="mb-6">
        <a href="{{ route('admin.transactions.index') }}" class="text-blue-600 hover:text-blue-800">
            <i class="fas fa-arrow-left"></i> Back to Transactions
        </a>
    </div>

    <div class="bg-white rounded-lg shadow p-8 mb-6">
        <div class="flex justify-between items-start mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-800 mb-2">Transaction Detail</h1>
                <p class="font-mono text-lg text-gray-600">{{ $transaction->transaction_code }}</p>
            </div>
            <div>
                @if($transaction->payment_status === 'paid')
                    <span class="px-4 py-2 bg-green-100 text-green-800 rounded-full text-sm font-semibold">
                        <i class="fas fa-check-circle"></i> Paid
                    </span>
                @elseif($transaction->payment_status === 'pending')
                    <span class="px-4 py-2 bg-yellow-100 text-yellow-800 rounded-full text-sm font-semibold">
                        <i class="fas fa-clock"></i> Pending
                    </span>
                @elseif($transaction->payment_status === 'failed')
                    <span class="px-4 py-2 bg-red-100 text-red-800 rounded-full text-sm font-semibold">
                        <i class="fas fa-times-circle"></i> Failed
                    </span>
                @else
                    <span class="px-4 py-2 bg-gray-100 text-gray-800 rounded-full text-sm font-semibold">
                        {{ ucfirst($transaction->payment_status) }}
                    </span>
                @endif
            </div>
        </div>

        <!-- User Info -->
        <div class="border-t pt-6 mb-6">
            <h3 class="font-bold text-gray-800 mb-4">Customer Information</h3>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <p class="text-gray-600 text-sm">Name</p>
                    <p class="font-semibold text-gray-900">{{ $transaction->user->name }}</p>
                </div>
                <div>
                    <p class="text-gray-600 text-sm">Email</p>
                    <p class="font-semibold text-gray-900">{{ $transaction->user->email }}</p>
                </div>
                @if($transaction->user->phone)
                <div>
                    <p class="text-gray-600 text-sm">Phone</p>
                    <p class="font-semibold text-gray-900">{{ $transaction->user->phone }}</p>
                </div>
                @endif
                <div>
                    <p class="text-gray-600 text-sm">User Role</p>
                    <p class="font-semibold text-gray-900">{{ ucfirst($transaction->user->role) }}</p>
                </div>
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
                    <p class="font-semibold text-gray-900">{{ $transaction->payment_method ?? '-' }}</p>
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
                @if($transaction->midtrans_order_id)
                <div>
                    <p class="text-gray-600 text-sm">Midtrans Order ID</p>
                    <p class="font-mono text-sm text-gray-900">{{ $transaction->midtrans_order_id }}</p>
                </div>
                @endif
            </div>
            <div class="border-t mt-4 pt-4">
                <div class="flex justify-between items-center">
                    <p class="text-lg font-bold text-gray-800">Total Payment</p>
                    <p class="text-2xl font-bold text-purple-600">{{ $transaction->total_price_formatted }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Voucher Codes -->
    @if($transaction->voucherCodes->count() > 0)
    <div class="bg-white rounded-lg shadow p-8 mb-6">
        <h3 class="font-bold text-gray-800 mb-4">
            <i class="fas fa-ticket"></i> Voucher Codes ({{ $transaction->voucherCodes->count() }})
        </h3>
        <div class="grid grid-cols-1 gap-3">
            @foreach($transaction->voucherCodes as $code)
            <div class="bg-gray-50 border-2 border-dashed border-gray-300 rounded-lg p-4 flex justify-between items-center">
                <div>
                    <span class="font-mono text-lg font-bold text-gray-900">{{ $code->code }}</span>
                    @if($code->is_used)
                        <span class="ml-2 text-xs bg-red-100 text-red-800 px-2 py-1 rounded">
                            Used {{ $code->used_at ? '- ' . $code->used_at->format('d M Y H:i') : '' }}
                        </span>
                    @else
                        <span class="ml-2 text-xs bg-green-100 text-green-800 px-2 py-1 rounded">Available</span>
                    @endif
                </div>
                <span class="text-sm text-gray-500">Code #{{ $loop->iteration }}</span>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Update Status Form -->
    @if(!$transaction->isPaid())
    <div class="bg-white rounded-lg shadow p-8">
        <h3 class="font-bold text-gray-800 mb-4">Update Payment Status</h3>
        <form action="{{ route('admin.transactions.update-status', $transaction) }}" method="POST">
            @csrf
            <div class="flex gap-4 items-end">
                <div class="flex-1">
                    <label class="block mb-2 font-semibold text-gray-700">Status</label>
                    <select name="payment_status" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-600">
                        <option value="pending" {{ $transaction->payment_status == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="paid" {{ $transaction->payment_status == 'paid' ? 'selected' : '' }}>Paid</option>
                        <option value="failed" {{ $transaction->payment_status == 'failed' ? 'selected' : '' }}>Failed</option>
                        <option value="expired" {{ $transaction->payment_status == 'expired' ? 'selected' : '' }}>Expired</option>
                    </select>
                </div>
                <button type="submit" class="bg-blue-600 text-white px-8 py-2 rounded-lg hover:bg-blue-700 transition font-semibold">
                    <i class="fas fa-save"></i> Update Status
                </button>
            </div>
            <p class="text-sm text-yellow-600 mt-2">
                <i class="fas fa-info-circle"></i> Note: Changing status to "Paid" will automatically generate voucher codes and send email to customer.
            </p>
        </form>
    </div>
    @endif
</div>
@endsection