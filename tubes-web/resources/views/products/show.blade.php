@extends('layouts.app')

@section('title', $product->name)

@section('content')
<div class="max-w-7xl mx-auto px-4">
    <!-- Breadcrumb -->
    <nav class="mb-6 text-sm">
        <ol class="flex items-center space-x-2 text-gray-600">
            <li><a href="{{ route('home') }}" class="hover:text-purple-600"><i class="fas fa-home"></i></a></li>
            <li><i class="fas fa-chevron-right text-xs"></i></li>
            <li><a href="{{ route('products.index') }}" class="hover:text-purple-600">Products</a></li>
            <li><i class="fas fa-chevron-right text-xs"></i></li>
            <li class="text-purple-600 font-semibold">{{ $product->name }}</li>
        </ol>
    </nav>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-12">
        <!-- Product Image -->
        <div class="bg-white rounded-2xl shadow-xl p-8">
            <div class="aspect-square bg-gradient-to-br from-purple-400 to-indigo-600 rounded-xl flex items-center justify-center overflow-hidden">
                @if($product->image)
                    <img src="{{ asset('storage/' . $product->image) }}" 
                         alt="{{ $product->name }}" 
                         class="w-full h-full object-cover">
                @else
                    <i class="fas fa-gamepad text-9xl text-white"></i>
                @endif
            </div>
        </div>

        <!-- Product Info -->
        <div>
            <div class="bg-white rounded-2xl shadow-xl p-8">
                <span class="inline-block bg-purple-100 text-purple-600 px-3 py-1 rounded-full text-sm font-semibold mb-4">
                    {{ $product->category->name }}
                </span>
                
                <h1 class="text-4xl font-bold text-gray-900 mb-4">{{ $product->name }}</h1>
                
                <!-- Price -->
                <div class="mb-6">
                    <div class="flex items-baseline gap-3">
                        <span class="text-5xl font-bold text-purple-600">{{ $product->price_formatted }}</span>
                        <span class="text-gray-500 line-through text-xl">Rp {{ number_format($product->price * 1.2, 0, ',', '.') }}</span>
                    </div>
                    <p class="text-green-600 font-semibold mt-2">
                        <i class="fas fa-tag"></i> Save 20%!
                    </p>
                </div>
                
                <!-- Stock -->
                <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                    <div class="flex items-center justify-between">
                        <span class="text-gray-700 font-medium">
                            <i class="fas fa-box"></i> Stock Available
                        </span>
                        <span class="font-bold {{ $product->stock < 10 ? 'text-red-600' : 'text-green-600' }}">
                            {{ $product->stock }} pcs
                        </span>
                    </div>
                    @if($product->stock < 10)
                        <p class="text-sm text-red-600 mt-2">
                            <i class="fas fa-exclamation-triangle"></i> Hurry! Only {{ $product->stock }} left!
                        </p>
                    @endif
                </div>
                
                <!-- Description -->
                <div class="mb-6">
                    <h3 class="font-bold text-gray-900 mb-2">Description</h3>
                    <p class="text-gray-600 leading-relaxed">{{ $product->description ?? 'Get your game voucher instantly after payment!' }}</p>
                </div>
                
                <!-- Features -->
                <div class="mb-8 grid grid-cols-2 gap-4">
                    <div class="flex items-center gap-2 text-gray-700">
                        <i class="fas fa-bolt text-yellow-500"></i>
                        <span class="text-sm">Instant Delivery</span>
                    </div>
                    <div class="flex items-center gap-2 text-gray-700">
                        <i class="fas fa-shield-alt text-green-500"></i>
                        <span class="text-sm">100% Secure</span>
                    </div>
                    <div class="flex items-center gap-2 text-gray-700">
                        <i class="fas fa-check-circle text-blue-500"></i>
                        <span class="text-sm">Original Codes</span>
                    </div>
                    <div class="flex items-center gap-2 text-gray-700">
                        <i class="fas fa-headset text-purple-500"></i>
                        <span class="text-sm">24/7 Support</span>
                    </div>
                </div>
                
                <!-- Call to Action -->
                @auth
                    <a href="{{ route('transactions.create', $product->id) }}" 
                       class="block w-full bg-gradient-to-r from-purple-600 to-indigo-600 text-white text-center py-4 rounded-xl font-bold text-lg hover:from-purple-700 hover:to-indigo-700 transition shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                        <i class="fas fa-shopping-cart"></i> Buy Now
                    </a>
                @else
                    <a href="{{ route('login') }}" 
                       class="block w-full bg-gray-300 text-gray-700 text-center py-4 rounded-xl font-bold text-lg hover:bg-gray-400 transition">
                        <i class="fas fa-lock"></i> Login to Purchase
                    </a>
                @endauth
            </div>
        </div>
    </div>

    <!-- Related Products -->
    @if($relatedProducts->count() > 0)
        <div class="mb-12">
            <h2 class="text-3xl font-bold text-gray-900 mb-6">
                <i class="fas fa-fire text-orange-500"></i> Related Products
            </h2>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($relatedProducts as $related)
                    <div class="bg-white rounded-xl shadow-lg overflow-hidden card-hover">
                        <a href="{{ route('products.show', $related->slug) }}">
                            <div class="h-40 bg-gradient-to-br from-purple-400 to-indigo-600 flex items-center justify-center">
                                @if($related->image)
                                    <img src="{{ asset('storage/' . $related->image) }}" 
                                         alt="{{ $related->name }}" 
                                         class="h-full w-full object-cover">
                                @else
                                    <i class="fas fa-gamepad text-5xl text-white"></i>
                                @endif
                            </div>
                        </a>
                        <div class="p-4">
                            <h3 class="font-semibold text-gray-900 mb-2 line-clamp-2">{{ $related->name }}</h3>
                            <p class="text-lg font-bold text-purple-600 mb-3">{{ $related->price_formatted }}</p>
                            <a href="{{ route('products.show', $related->slug) }}" 
                               class="block w-full bg-purple-600 text-white text-center py-2 rounded-lg hover:bg-purple-700 transition text-sm font-semibold">
                                View Details
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
@endsection
