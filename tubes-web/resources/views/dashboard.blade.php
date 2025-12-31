@extends('layouts.app')

@section('title', 'Home')

@section('content')
<div class="max-w-7xl mx-auto px-4">

    <!-- ================= HERO ================= -->
    <div class="bg-gradient-to-r from-purple-600 to-indigo-600 rounded-lg shadow-lg p-10 mb-10 text-white">
        <h1 class="text-4xl font-bold mb-2">
            Selamat Datang di ROCKETEER 🚀
        </h1>
        <p class="text-lg">Voucher game terlengkap dengan harga terbaik</p>
    </div>

    <!-- ================= KATEGORI ================= -->
    @if($categories->count())
    <div class="mb-12">
        <h2 class="text-2xl font-bold mb-4 text-gray-800 flex items-center gap-2">
            🗂️ Kategori
        </h2>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <!-- Semua Produk -->
            <a href="{{ route('home') }}"
               class="border-2 border-purple-500 rounded-lg p-5 text-center hover:bg-purple-50 transition">
                <div class="text-purple-600 text-3xl mb-2">
                    <i class="fas fa-th-large"></i>
                </div>
                <h3 class="font-semibold text-purple-600">Semua Produk</h3>
                <p class="text-sm text-gray-500">
                    {{ $products->count() }} products
                </p>
            </a>

            <!-- Loop kategori -->
            @foreach($categories as $cat)
                <a href="{{ route('category.filter', $cat->id) }}"
                   class="bg-white rounded-lg shadow p-5 text-center hover:shadow-lg transition">
                    <div class="text-gray-600 text-3xl mb-2">
                        <i class="fas fa-gamepad"></i>
                    </div>
                    <h3 class="font-semibold text-gray-800">
                        {{ $cat->name }}
                    </h3>
                    <p class="text-sm text-gray-500">
                        {{ $cat->products_count }} products
                    </p>
                </a>
            @endforeach
        </div>
    </div>
    @endif

    <!-- ================= GAME POPULER (API PUBLIK) ================= -->
    @php
        $popularGames = $popularGames ?? collect();
    @endphp

    @if(count($popularGames))
    <div class="mb-12">
        <h2 class="text-2xl font-bold mb-4 text-gray-800">
            🎮 Game Populer (API RAWG)
        </h2>

        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
            @foreach($popularGames as $game)
                <div class="bg-white rounded-lg shadow p-3 text-center">
                    <img
                        src="{{ $game['background_image'] ?? '' }}"
                        alt="{{ $game['name'] }}"
                        class="h-32 w-full object-cover rounded mb-2"
                    >
                    <p class="text-sm font-semibold text-gray-800">
                        {{ $game['name'] }}
                    </p>
                    <p class="text-xs text-gray-500">
                        ⭐ {{ number_format($game['rating'], 1) }}
                    </p>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- ================= PRODUK ================= -->
    <div class="mb-12">
        <h2 class="text-2xl font-bold mb-4 text-gray-800 flex items-center gap-2">
            🔥 Produk Terpopuler
        </h2>

        @if($products->count())
        <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-6">
            @foreach($products as $product)
                <div class="bg-white rounded-lg shadow hover:shadow-xl transition overflow-hidden">
                    <div class="h-48 bg-gradient-to-br from-purple-400 to-indigo-600 flex items-center justify-center">
                        @if($product->image)
                            <img src="{{ asset('storage/' . $product->image) }}"
                                 alt="{{ $product->name }}"
                                 class="h-full w-full object-cover">
                        @else
                            <i class="fas fa-gamepad text-6xl text-white"></i>
                        @endif
                    </div>

                    <div class="p-4">
                        <h3 class="font-semibold text-gray-800 mb-1">
                            {{ $product->name }}
                        </h3>
                        <p class="text-sm text-gray-500 mb-2">
                            {{ $product->category->name }}
                        </p>
                        <p class="text-xl font-bold text-purple-600 mb-3">
                            {{ $product->price_formatted }}
                        </p>

                        @auth
                            <button class="w-full bg-purple-600 text-white py-2 rounded-lg hover:bg-purple-700 transition">
                                🛒 Beli Sekarang
                            </button>
                        @else
                            <a href="{{ route('login') }}"
                               class="block w-full bg-gray-300 text-gray-700 py-2 rounded-lg text-center">
                                Login untuk Beli
                            </a>
                        @endauth
                    </div>
                </div>
            @endforeach
        </div>
        @else
            <div class="bg-white rounded-lg shadow p-10 text-center text-gray-500">
                Belum ada produk tersedia
            </div>
        @endif
    </div>

</div>
@endsection
