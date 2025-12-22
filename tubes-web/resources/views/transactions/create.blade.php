@extends('layouts.app')

@section('title', 'Checkout')

@section('content')
<div class="max-w-4xl mx-auto px-4">
    <h1 class="text-4xl font-bold text-gray-900 mb-8">
        <i class="fas fa-shopping-cart text-purple-600"></i> Checkout
    </h1>

    <form action="{{ route('transactions.store') }}" method="POST" x-data="{
        quantity: 1,
        price: {{ $product->price }},
        get total() { return this.quantity * this.price; }
    }">
        @csrf
        <input type="hidden" name="product_id" value="{{ $product->id }}">
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Order Form -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Product Info -->
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">
                        <i class="fas fa-box"></i> Product Details
                    </h2>
                    
                    <div class="flex items-center gap-4">
                        <div class="w-24 h-24 bg-gradient-to-br from-purple-400 to-indigo-600 rounded-lg flex items-center justify-center flex-shrink-0">
                            @if($product->image)
                                <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="w-full h-full object-cover rounded-lg">
                            @else
                                <i class="fas fa-gamepad text-3xl text-white"></i>
                            @endif
                        </div>
                        <div class="flex-1">
                            <h3 class="font-bold text-gray-900 text-lg">{{ $product->name }}</h3>
                            <p class="text-sm text-gray-600">{{ $product->category->name }}</p>
                            <p class="text-xl font-bold text-purple-600 mt-2">{{ $product->price_formatted }}</p>
                        </div>
                    </div>
                </div>

                <!-- Quantity -->
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <label class="block text-lg font-bold text-gray-900 mb-4">
                        <i class="fas fa-sort-numeric-up"></i> Quantity
                    </label>
                    
                    <div class="flex items-center gap-4">
                        <button type="button" @click="quantity = Math.max(1, quantity - 1)" 
                                class="w-12 h-12 bg-gray-200 rounded-lg hover:bg-gray-300 transition font-bold text-xl">
                            -
                        </button>
                        
                        <input type="number" name="quantity" x-model="quantity" min="1" max="{{ $product->stock }}" 
                               class="w-24 text-center text-2xl font-bold border-2 border-purple-200 rounded-lg py-2 focus:ring-2 focus:ring-purple-500">
                        
                        <button type="button" @click="quantity = Math.min({{ $product->stock }}, quantity + 1)" 
                                class="w-12 h-12 bg-gray-200 rounded-lg hover:bg-gray-300 transition font-bold text-xl">
                            +
                        </button>
                        
                        <span class="text-gray-600 ml-4">
                            <i class="fas fa-box"></i> Max: {{ $product->stock }}
                        </span>
                    </div>
                    
                    @error('quantity')
                        <p class="text-red-600 text-sm mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Payment Method -->
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <label class="block text-lg font-bold text-gray-900 mb-4">
                        <i class="fas fa-credit-card"></i> Payment Method
                    </label>
                    
                    <div class="space-y-3">
                        <label class="flex items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-purple-500 transition has-[:checked]:border-purple-600 has-[:checked]:bg-purple-50">
                            <input type="radio" name="payment_method" value="midtrans" checked class="text-purple-600 focus:ring-purple-500">
                            <div class="ml-3 flex-1">
                                <div class="font-semibold text-gray-900">Midtrans Payment Gateway</div>
                                <div class="text-sm text-gray-600">Credit Card, E-Wallet, Bank Transfer</div>
                            </div>
                            <i class="fas fa-credit-card text-2xl text-purple-600"></i>
                        </label>
                        
                        <label class="flex items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-purple-500 transition has-[:checked]:border-purple-600 has-[:checked]:bg-purple-50">
                            <input type="radio" name="payment_method" value="qris" class="text-purple-600 focus:ring-purple-500">
                            <div class="ml-3 flex-1">
                                <div class="font-semibold text-gray-900">QRIS</div>
                                <div class="text-sm text-gray-600">Scan QR Code to pay</div>
                            </div>
                            <i class="fas fa-qrcode text-2xl text-purple-600"></i>
                        </label>
                        
                        <label class="flex items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-purple-500 transition has-[:checked]:border-purple-600 has-[:checked]:bg-purple-50">
                            <input type="radio" name="payment_method" value="bank_transfer" class="text-purple-600 focus:ring-purple-500">
                            <div class="ml-3 flex-1">
                                <div class="font-semibold text-gray-900">Bank Transfer</div>
                                <div class="text-sm text-gray-600">Manual transfer to our account</div>
                            </div>
                            <i class="fas fa-building-columns text-2xl text-purple-600"></i>
                        </label>
                    </div>
                    
                    @error('payment_method')
                        <p class="text-red-600 text-sm mt-2">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Order Summary -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-xl shadow-lg p-6 sticky top-20">
                    <h2 class="text-xl font-bold text-gray-900 mb-6">
                        <i class="fas fa-file-invoice"></i> Order Summary
                    </h2>
                    
                    <div class="space-y-4 mb-6">
                        <div class="flex justify-between text-gray-700">
                            <span>Price per item</span>
                            <span class="font-semibold">{{ $product->price_formatted }}</span>
                        </div>
                        
                        <div class="flex justify-between text-gray-700">
                            <span>Quantity</span>
                            <span class="font-semibold" x-text="quantity + ' pcs'"></span>
                        </div>
                        
                        <div class="border-t pt-4">
                            <div class="flex justify-between text-gray-700 mb-2">
                                <span>Subtotal</span>
                                <span class="font-semibold" x-text="'Rp ' + total.toLocaleString('id-ID')"></span>
                            </div>
                            <div class="flex justify-between text-sm text-green-600">
                                <span>Discount (20%)</span>
                                <span x-text="'- Rp ' + (total * 0.2).toLocaleString('id-ID')"></span>
                            </div>
                        </div>
                        
                        <div class="border-t pt-4">
                            <div class="flex justify-between items-baseline">
                                <span class="text-lg font-bold text-gray-900">Total</span>
                                <div class="text-right">
                                    <div class="text-2xl font-bold text-purple-600" x-text="'Rp ' + (total * 0.8).toLocaleString('id-ID')"></div>
                                    <div class="text-sm text-gray-500 line-through" x-text="'Rp ' + total.toLocaleString('id-ID')"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <button type="submit" 
                            class="w-full bg-gradient-to-r from-purple-600 to-indigo-600 text-white py-4 rounded-xl font-bold text-lg hover:from-purple-700 hover:to-indigo-700 transition shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                        <i class="fas fa-lock"></i> Proceed to Payment
                    </button>
                    
                    <div class="mt-4 p-3 bg-blue-50 rounded-lg text-sm text-blue-800">
                        <i class="fas fa-info-circle"></i> Your voucher code will be delivered instantly after successful payment
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
