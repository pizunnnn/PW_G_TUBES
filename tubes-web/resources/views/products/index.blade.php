@extends('layouts.app')

@section('title', 'Products')

@section('content')
<div class="max-w-7xl mx-auto px-4">
    <!-- Slider Carousel -->
    @if($sliders->count() > 0)
    <div class="mb-8 rounded-xl overflow-hidden shadow-2xl" x-data="{
        currentSlide: 0,
        totalSlides: {{ $sliders->count() }},
        autoplay: null,
        init() {
            this.startAutoplay();
        },
        startAutoplay() {
            if (this.totalSlides > 1) {
                this.autoplay = setInterval(() => {
                    this.next();
                }, 5000);
            }
        },
        stopAutoplay() {
            if (this.autoplay) {
                clearInterval(this.autoplay);
            }
        },
        next() {
            this.currentSlide = (this.currentSlide + 1) % this.totalSlides;
        },
        prev() {
            this.currentSlide = (this.currentSlide - 1 + this.totalSlides) % this.totalSlides;
        },
        goTo(index) {
            this.currentSlide = index;
            this.stopAutoplay();
            this.startAutoplay();
        }
    }" @mouseenter="stopAutoplay()" @mouseleave="startAutoplay()">
        <div class="relative h-64 md:h-96">
            <!-- Slides -->
            @foreach($sliders as $index => $slider)
                <div x-show="currentSlide === {{ $index }}"
                     x-transition:enter="transition ease-out duration-500"
                     x-transition:enter-start="opacity-0 transform translate-x-full"
                     x-transition:enter-end="opacity-100 transform translate-x-0"
                     x-transition:leave="transition ease-in duration-500"
                     x-transition:leave-start="opacity-100 transform translate-x-0"
                     x-transition:leave-end="opacity-0 transform -translate-x-full"
                     class="absolute inset-0">
                    @if($slider->link_url !== '#')
                        <a href="{{ $slider->link_url }}" class="block w-full h-full">
                            <img src="{{ asset('storage/' . $slider->image) }}"
                                 alt="{{ $slider->title }}"
                                 class="w-full h-full object-cover">
                            @if($slider->title)
                                <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent flex items-end">
                                    <div class="p-8 text-white">
                                        <h2 class="text-3xl md:text-4xl font-bold drop-shadow-lg">{{ $slider->title }}</h2>
                                    </div>
                                </div>
                            @endif
                        </a>
                    @else
                        <img src="{{ asset('storage/' . $slider->image) }}"
                             alt="{{ $slider->title }}"
                             class="w-full h-full object-cover">
                        @if($slider->title)
                            <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent flex items-end">
                                <div class="p-8 text-white">
                                    <h2 class="text-3xl md:text-4xl font-bold drop-shadow-lg">{{ $slider->title }}</h2>
                                </div>
                            </div>
                        @endif
                    @endif
                </div>
            @endforeach

            <!-- Navigation Arrows -->
            @if($sliders->count() > 1)
            <button @click="prev()"
                    class="absolute left-4 top-1/2 transform -translate-y-1/2 bg-white/30 hover:bg-white/50 text-white rounded-full p-3 transition backdrop-blur-sm z-10">
                <i class="fas fa-chevron-left"></i>
            </button>
            <button @click="next()"
                    class="absolute right-4 top-1/2 transform -translate-y-1/2 bg-white/30 hover:bg-white/50 text-white rounded-full p-3 transition backdrop-blur-sm z-10">
                <i class="fas fa-chevron-right"></i>
            </button>

            <!-- Dots Indicator -->
            <div class="absolute bottom-4 left-1/2 transform -translate-x-1/2 flex gap-2 z-10">
                @foreach($sliders as $index => $slider)
                    <button @click="goTo({{ $index }})"
                            class="w-3 h-3 rounded-full transition-all duration-300"
                            :class="currentSlide === {{ $index }} ? 'bg-white w-8' : 'bg-white/50'">
                    </button>
                @endforeach
            </div>
            @endif
        </div>
    </div>
    @endif

    <!-- Running Text - Recent Transactions -->
    @if($recentTransactions->count() > 0)
    <div class="mb-8 bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-600 rounded-lg shadow-lg overflow-hidden">
        <div class="py-3 px-4">
            <div class="flex items-center gap-2 mb-2">
                <i class="fas fa-fire text-white animate-pulse"></i>
                <span class="text-white font-semibold text-sm">Transaksi Terbaru</span>
            </div>
            <div class="overflow-hidden relative">
                <div class="flex gap-8 animate-scroll whitespace-nowrap">
                    @foreach($recentTransactions as $transaction)
                        <span class="inline-flex items-center gap-2 text-white text-sm">
                            <i class="fas fa-check-circle text-green-300"></i>
                            <strong>{{ $transaction->user->name }}</strong>
                            <span class="opacity-90">baru membeli</span>
                            <strong class="text-yellow-300">{{ $transaction->product->name }}</strong>
                            <span class="opacity-75 text-xs">({{ $transaction->paid_at->diffForHumans() }})</span>
                        </span>
                    @endforeach
                    <!-- Duplicate for seamless loop -->
                    @foreach($recentTransactions as $transaction)
                        <span class="inline-flex items-center gap-2 text-white text-sm">
                            <i class="fas fa-check-circle text-green-300"></i>
                            <strong>{{ $transaction->user->name }}</strong>
                            <span class="opacity-90">baru membeli</span>
                            <strong class="text-yellow-300">{{ $transaction->product->name }}</strong>
                            <span class="opacity-75 text-xs">({{ $transaction->paid_at->diffForHumans() }})</span>
                        </span>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <style>
        @keyframes scroll {
            0% {
                transform: translateX(0);
            }
            100% {
                transform: translateX(-50%);
            }
        }

        .animate-scroll {
            animation: scroll 30s linear infinite;
        }

        .animate-scroll:hover {
            animation-play-state: paused;
        }
    </style>
    @endif

    <!-- Header -->
    <div class="mb-4">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-1">
            <i class="fas fa-store text-purple-600 dark:text-purple-400"></i> Katalog Produk
        </h1>
        <p class="text-sm text-gray-600 dark:text-gray-400">Temukan voucher game favoritmu dengan harga terbaik</p>
    </div>

    <div class="flex flex-col lg:flex-row gap-4" x-data="{ filterOpen: false, gridCols: 2 }">
        <!-- Sidebar Filters -->
        <aside class="lg:w-56 flex-shrink-0">
            <!-- Mobile Filter Toggle Button -->
            <button @click="filterOpen = !filterOpen"
                    class="lg:hidden w-full bg-white dark:bg-gray-800 rounded-lg shadow p-3 mb-2 flex items-center justify-between font-semibold text-gray-900 dark:text-white">
                <span>
                    <i class="fas fa-filter text-purple-600"></i> Filter & Kategori
                </span>
                <i class="fas" :class="filterOpen ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
            </button>

            <!-- Filter Content -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 sticky top-20 lg:block"
                 x-show="filterOpen || window.innerWidth >= 1024"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 transform -translate-y-2"
                 x-transition:enter-end="opacity-100 transform translate-y-0"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 transform translate-y-0"
                 x-transition:leave-end="opacity-0 transform -translate-y-2">
                <h2 class="text-sm font-bold mb-3 text-gray-900 dark:text-white">
                    <i class="fas fa-filter"></i> Filter
                </h2>

                <!-- Category Filter -->
                <div class="mb-4">
                    <h3 class="text-xs font-semibold text-gray-700 dark:text-gray-300 mb-2">Kategori</h3>
                    <form action="{{ route('products.index') }}" method="GET" id="filterForm">
                        <input type="hidden" name="search" value="{{ request('search') }}">
                        <input type="hidden" name="sort" value="{{ request('sort') }}">

                        <div class="space-y-1">
                            <label class="flex items-center p-1.5 rounded hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer transition">
                                <input type="radio" name="category" value="" {{ !request('category') ? 'checked' : '' }}
                                       onchange="this.form.submit()" class="text-purple-600 focus:ring-purple-500 w-3 h-3">
                                <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Semua</span>
                            </label>
                            @foreach($categories as $category)
                                <label class="flex items-center p-1.5 rounded hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer transition">
                                    <input type="radio" name="category" value="{{ $category->id }}"
                                           {{ request('category') == $category->id ? 'checked' : '' }}
                                           onchange="this.form.submit()" class="text-purple-600 focus:ring-purple-500 w-3 h-3">
                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">{{ $category->name }}</span>
                                    <span class="ml-auto text-xs bg-purple-100 dark:bg-purple-900 text-purple-600 dark:text-purple-300 px-1.5 py-0.5 rounded-full">{{ $category->products_count }}</span>
                                </label>
                            @endforeach
                        </div>
                    </form>
                </div>

                <!-- Price Range Info -->
                <div class="pt-3 border-t dark:border-gray-700">
                    <p class="text-xs text-gray-600 dark:text-gray-400">
                        <i class="fas fa-info-circle text-blue-500 dark:text-blue-400"></i>
                        Mulai Rp 5.000
                    </p>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="flex-1">
            <!-- Search & Sort Bar -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-3 mb-4">
                <div class="flex flex-col md:flex-row gap-4">
                    <!-- Live Search -->
                    <div class="flex-1" x-data="{ 
                        search: '{{ request('search') }}',
                        results: [],
                        loading: false,
                        searchProducts() {
                            if (this.search.length < 2) {
                                this.results = [];
                                return;
                            }
                            this.loading = true;
                            fetch(`{{ route('products.search') }}?q=${this.search}`)
                                .then(r => r.json())
                                .then(data => {
                                    this.results = data;
                                    this.loading = false;
                                });
                        }
                    }">
                        <div class="relative">
                            <input type="text"
                                   x-model="search"
                                   @input.debounce.300ms="searchProducts()"
                                   placeholder="Cari produk..."
                                   class="w-full pl-10 pr-4 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                            <i class="fas fa-search absolute left-3 top-3 text-gray-400 dark:text-gray-500"></i>
                            
                            <!-- Search Results Dropdown -->
                            <div x-show="results.length > 0"
                                 @click.away="results = []"
                                 class="absolute z-10 w-full mt-2 bg-white dark:bg-gray-700 rounded-lg shadow-xl border dark:border-gray-600">
                                <template x-for="product in results" :key="product.id">
                                    <a :href="`/products/${product.slug}`"
                                       class="flex items-center p-3 hover:bg-gray-50 dark:hover:bg-gray-600 border-b dark:border-gray-600 last:border-b-0">
                                        <div class="w-12 h-12 bg-gradient-to-br from-purple-400 to-indigo-600 rounded flex-shrink-0"></div>
                                        <div class="ml-3 flex-1">
                                            <p class="font-medium text-gray-900 dark:text-white" x-text="product.name"></p>
                                            <p class="text-sm text-purple-600 dark:text-purple-400 font-semibold" x-text="'Rp ' + product.price.toLocaleString('id-ID')"></p>
                                        </div>
                                    </a>
                                </template>
                            </div>
                        </div>
                        
                        <!-- Submit Search Button -->
                        <form action="{{ route('products.index') }}" method="GET" class="mt-2">
                            <input type="hidden" name="search" :value="search">
                            <input type="hidden" name="category" value="{{ request('category') }}">
                            <input type="hidden" name="sort" value="{{ request('sort') }}">
                            <button type="submit" class="w-full md:w-auto bg-purple-600 text-white px-6 py-2 rounded-lg hover:bg-purple-700 transition">
                                <i class="fas fa-search"></i> Cari
                            </button>
                        </form>
                    </div>
                    
                    <!-- Sort Dropdown -->
                    <div class="flex-shrink-0">
                        <form action="{{ route('products.index') }}" method="GET">
                            <input type="hidden" name="search" value="{{ request('search') }}">
                            <input type="hidden" name="category" value="{{ request('category') }}">
                            <select name="sort" onchange="this.form.submit()"
                                    class="w-full md:w-auto px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                                <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Terbaru</option>
                                <option value="popular" {{ request('sort') == 'popular' ? 'selected' : '' }}>Terpopuler</option>
                                <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Harga: Rendah ke Tinggi</option>
                                <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Harga: Tinggi ke Rendah</option>
                                <option value="name" {{ request('sort') == 'name' ? 'selected' : '' }}>Nama A-Z</option>
                            </select>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Results Count & Grid Toggle -->
            <div class="mb-3 flex items-center justify-between">
                <div class="text-sm text-gray-600 dark:text-gray-400">
                    <i class="fas fa-box"></i> Menampilkan {{ $products->count() }} dari {{ $products->total() }} produk
                </div>

                <!-- Grid Toggle Buttons (Mobile Only) -->
                <div class="flex gap-1 lg:hidden">
                    <button @click="gridCols = 2"
                            :class="gridCols === 2 ? 'bg-purple-600 text-white' : 'bg-gray-200 text-gray-600'"
                            class="px-3 py-1.5 rounded-lg text-xs font-semibold transition">
                        <i class="fas fa-th-large"></i> 2
                    </button>
                    <button @click="gridCols = 3"
                            :class="gridCols === 3 ? 'bg-purple-600 text-white' : 'bg-gray-200 text-gray-600'"
                            class="px-3 py-1.5 rounded-lg text-xs font-semibold transition">
                        <i class="fas fa-th"></i> 3
                    </button>
                </div>
            </div>

            <!-- Products Grid -->
            @if($products->count() > 0)
                <div class="grid gap-3 lg:grid-cols-4 xl:grid-cols-5"
                     :class="{
                         'grid-cols-2': gridCols === 2,
                         'grid-cols-3': gridCols === 3
                     }">
                    @foreach($products as $product)
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden card-hover">
                            <!-- Product Image -->
                            <a href="{{ route('products.show', $product->slug) }}" class="block">
                                <div class="h-32 bg-gradient-to-br from-purple-400 to-indigo-600 flex items-center justify-center relative overflow-hidden group">
                                    @if($product->image)
                                        <img src="{{ str_starts_with($product->image, 'http') ? $product->image : asset('storage/' . $product->image) }}"
                                             alt="{{ $product->name }}"
                                             class="h-full w-full object-cover group-hover:scale-110 transition duration-300">
                                    @else
                                        <i class="fas fa-gamepad text-4xl text-white"></i>
                                    @endif

                                    <!-- Stock Badge -->
                                    @if($product->stock < 10)
                                        <span class="absolute top-1 right-1 bg-red-500 text-white text-xs px-1.5 py-0.5 rounded-full font-semibold">
                                            <i class="fas fa-fire"></i> {{ $product->stock }}
                                        </span>
                                    @endif
                                </div>
                            </a>

                            <!-- Product Info -->
                            <div class="p-3">
                                <span class="text-xs text-purple-600 dark:text-purple-400 font-semibold uppercase">
                                    {{ $product->category->name }}
                                </span>
                                <h3 class="text-sm font-bold text-gray-900 dark:text-white mt-1 mb-2 line-clamp-2 hover:text-purple-600 dark:hover:text-purple-400 transition">
                                    <a href="{{ route('products.show', $product->slug) }}">{{ $product->name }}</a>
                                </h3>

                                <!-- Price -->
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-lg font-bold text-purple-600 dark:text-purple-400">
                                        {{ $product->price_formatted }}
                                    </span>
                                    <span class="text-xs text-gray-500 dark:text-gray-400">
                                        <i class="fas fa-box"></i> {{ $product->stock }}
                                    </span>
                                </div>

                                <!-- Actions -->
                                @auth
                                    <a href="{{ route('transactions.create', $product->id) }}"
                                       class="block w-full bg-gradient-to-r from-purple-600 to-indigo-600 text-white text-center py-2 rounded-lg text-sm font-semibold hover:from-purple-700 hover:to-indigo-700 transition">
                                        <i class="fas fa-shopping-cart"></i> Beli
                                    </a>
                                @else
                                    <a href="{{ route('login') }}"
                                       class="block w-full bg-gray-300 text-gray-700 text-center py-2 rounded-lg text-sm font-semibold hover:bg-gray-400 transition">
                                        <i class="fas fa-lock"></i> Login
                                    </a>
                                @endauth
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <!-- Pagination -->
                <div class="mt-4">
                    {{ $products->links() }}
                </div>
            @else
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-12 text-center">
                    <i class="fas fa-box-open text-6xl text-gray-300 dark:text-gray-600 mb-4"></i>
                    <h3 class="text-xl font-semibold text-gray-700 dark:text-gray-300 mb-2">Tidak ada produk ditemukan</h3>
                    <p class="text-gray-500 dark:text-gray-400 mb-6">Coba sesuaikan filter atau kata kunci pencarian</p>
                    <a href="{{ route('products.index') }}" class="inline-block bg-purple-600 text-white px-6 py-3 rounded-lg hover:bg-purple-700 transition">
                        <i class="fas fa-redo"></i> Reset Filter
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
