@extends('layouts.app')

@section('title', 'Home')

@section('content')
<div class="max-w-7xl mx-auto px-4">
    <!-- Hero Section -->
    <div class="bg-gradient-to-r from-purple-600 to-indigo-600 rounded-lg shadow-lg p-12 mb-8 text-white">
        <h1 class="text-4xl font-bold mb-4">
            <i class="fas fa-rocket"></i> Selamat Datang di ROCKERRT!
        </h1>
        <p class="text-xl mb-6">Voucher game terlengkap dengan harga terbaik</p>
        
        @auth
            <p class="text-lg">Halo, <span class="font-bold">{{ auth()->user()->name }}</span>! 👋</p>
        @else
            <div class="space-x-4">
                <a href="{{ route('login') }}" class="bg-white text-purple-600 px-6 py-3 rounded-lg font-semibold hover:bg-gray-100 inline-block">
                    <i class="fas fa-sign-in-alt"></i> Login
                </a>
                <a href="{{ route('register') }}" class="bg-transparent border-2 border-white text-white px-6 py-3 rounded-lg font-semibold hover:bg-white hover:text-purple-600 inline-block">
                    <i class="fas fa-user-plus"></i> Register
                </a>
            </div>
        @endauth
    </div>

    <!-- Categories Section -->
    @if($categories->count() > 0)
        <div class="mb-8">
            <h2 class="text-2xl font-bold mb-4 text-gray-800">
                <i class="fas fa-layer-group"></i> Kategori
            </h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                @foreach($categories as $category)
                    <div class="bg-white rounded-lg shadow p-6 text-center hover:shadow-lg transition cursor-pointer">
                        <i class="fas fa-gamepad text-4xl text-purple-600 mb-2"></i>
                        <h3 class="font-semibold text-gray-800">{{ $category->name }}</h3>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Products Section -->
    <div class="mb-8">
        <h2 class="text-2xl font-bold mb-4 text-gray-800">
            <i class="fas fa-fire"></i> Produk Terpopuler
        </h2>
        
        @if($products->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-6">
                @foreach($products as $product)
                    <div class="bg-white rounded-lg shadow hover:shadow-xl transition overflow-hidden">
                        <div class="h-48 bg-gradient-to-br from-purple-400 to-indigo-600 flex items-center justify-center">
                            @if($product->image)
                                <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="h-full w-full object-cover">
                            @else
                                <i class="fas fa-gamepad text-6xl text-white"></i>
                            @endif
                        </div>
                        <div class="p-4">
                            <h3 class="font-semibold text-gray-800 mb-2">{{ $product->name }}</h3>
                            <p class="text-sm text-gray-600 mb-2">{{ $product->category->name }}</p>
                            <p class="text-2xl font-bold text-purple-600 mb-3">{{ $product->price_formatted }}</p>
                            
                            @auth
                                <button class="w-full bg-gradient-to-r from-purple-600 to-indigo-600 text-white py-2 rounded-lg font-semibold hover:from-purple-700 hover:to-indigo-700 transition">
                                    <i class="fas fa-shopping-cart"></i> Beli Sekarang
                                </button>
                            @else
                                <a href="{{ route('login') }}" class="block w-full bg-gray-300 text-gray-700 py-2 rounded-lg font-semibold text-center hover:bg-gray-400 transition">
                                    <i class="fas fa-lock"></i> Login untuk Beli
                                </a>
                            @endauth
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="bg-white rounded-lg shadow p-12 text-center">
                <i class="fas fa-box-open text-6xl text-gray-400 mb-4"></i>
                <p class="text-gray-600 text-lg">Belum ada produk tersedia</p>
            </div>
        @endif
    </div>
</div>
@endsection