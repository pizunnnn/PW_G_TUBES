@extends('layouts.app')

@section('title', 'Products')

@section('content')
<div class="max-w-7xl mx-auto px-4">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-4xl font-bold text-gray-900 mb-2">
            <i class="fas fa-store text-purple-600"></i> Product Catalog
        </h1>
        <p class="text-gray-600">Temukan voucher game favoritmu dengan harga terbaik</p>
    </div>

    <div class="flex flex-col lg:flex-row gap-8">
        <!-- Sidebar Filters -->
        <aside class="lg:w-64 flex-shrink-0">
            <div class="bg-white rounded-lg shadow-lg p-6 sticky top-20">
                <h2 class="text-lg font-bold mb-4 text-gray-900">
                    <i class="fas fa-filter"></i> Filters
                </h2>
                
                <!-- Category Filter -->
                <div class="mb-6">
                    <h3 class="font-semibold text-gray-700 mb-3">Categories</h3>
                    <form action="{{ route('products.index') }}" method="GET" id="filterForm">
                        <input type="hidden" name="search" value="{{ request('search') }}">
                        <input type="hidden" name="sort" value="{{ request('sort') }}">
                        
                        <div class="space-y-2">
                            <label class="flex items-center p-2 rounded hover:bg-gray-50 cursor-pointer transition">
                                <input type="radio" name="category" value="" {{ !request('category') ? 'checked' : '' }} 
                                       onchange="this.form.submit()" class="text-purple-600 focus:ring-purple-500">
                                <span class="ml-2 text-gray-700">All Categories</span>
                            </label>
                            @foreach($categories as $category)
                                <label class="flex items-center p-2 rounded hover:bg-gray-50 cursor-pointer transition">
                                    <input type="radio" name="category" value="{{ $category->id }}" 
                                           {{ request('category') == $category->id ? 'checked' : '' }}
                                           onchange="this.form.submit()" class="text-purple-600 focus:ring-purple-500">
                                    <span class="ml-2 text-gray-700">{{ $category->name }}</span>
                                    <span class="ml-auto text-xs bg-purple-100 text-purple-600 px-2 py-1 rounded-full">{{ $category->products_count }}</span>
                                </label>
                            @endforeach
                        </div>
                    </form>
                </div>
                
                <!-- Price Range Info -->
                <div class="pt-6 border-t">
                    <h3 class="font-semibold text-gray-700 mb-2">Price Range</h3>
                    <p class="text-sm text-gray-600">
                        <i class="fas fa-info-circle text-blue-500"></i> 
                        Harga mulai dari Rp 5.000
                    </p>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="flex-1">
            <!-- Search & Sort Bar -->
            <div class="bg-white rounded-lg shadow-lg p-4 mb-6">
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
                                   placeholder="Search products..."
                                   class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                            <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                            
                            <!-- Search Results Dropdown -->
                            <div x-show="results.length > 0" 
                                 @click.away="results = []"
                                 class="absolute z-10 w-full mt-2 bg-white rounded-lg shadow-xl border">
                                <template x-for="product in results" :key="product.id">
                                    <a :href="`/products/${product.slug}`" 
                                       class="flex items-center p-3 hover:bg-gray-50 border-b last:border-b-0">
                                        <div class="w-12 h-12 bg-gradient-to-br from-purple-400 to-indigo-600 rounded flex-shrink-0"></div>
                                        <div class="ml-3 flex-1">
                                            <p class="font-medium text-gray-900" x-text="product.name"></p>
                                            <p class="text-sm text-purple-600 font-semibold" x-text="'Rp ' + product.price.toLocaleString('id-ID')"></p>
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
                                <i class="fas fa-search"></i> Search
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
                                <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Newest</option>
                                <option value="popular" {{ request('sort') == 'popular' ? 'selected' : '' }}>Most Popular</option>
                                <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Price: Low to High</option>
                                <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Price: High to Low</option>
                                <option value="name" {{ request('sort') == 'name' ? 'selected' : '' }}>Name A-Z</option>
                            </select>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Results Count -->
            <div class="mb-4 text-gray-600">
                <i class="fas fa-box"></i> Showing {{ $products->count() }} of {{ $products->total() }} products
            </div>

            <!-- Products Grid -->
            @if($products->count() > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    @foreach($products as $product)
                        <div class="bg-white rounded-xl shadow-lg overflow-hidden card-hover">
                            <!-- Product Image -->
                            <a href="{{ route('products.show', $product->slug) }}" class="block">
                                <div class="h-48 bg-gradient-to-br from-purple-400 to-indigo-600 flex items-center justify-center relative overflow-hidden group">
                                    @if($product->image)
                                        <img src="{{ asset('storage/' . $product->image) }}" 
                                             alt="{{ $product->name }}" 
                                             class="h-full w-full object-cover group-hover:scale-110 transition duration-300">
                                    @else
                                        <i class="fas fa-gamepad text-6xl text-white"></i>
                                    @endif
                                    
                                    <!-- Stock Badge -->
                                    @if($product->stock < 10)
                                        <span class="absolute top-2 right-2 bg-red-500 text-white text-xs px-2 py-1 rounded-full font-semibold">
                                            <i class="fas fa-fire"></i> {{ $product->stock }} left
                                        </span>
                                    @endif
                                </div>
                            </a>
                            
                            <!-- Product Info -->
                            <div class="p-4">
                                <span class="text-xs text-purple-600 font-semibold uppercase tracking-wide">
                                    {{ $product->category->name }}
                                </span>
                                <h3 class="font-bold text-gray-900 mt-1 mb-2 line-clamp-2 hover:text-purple-600 transition">
                                    <a href="{{ route('products.show', $product->slug) }}">{{ $product->name }}</a>
                                </h3>
                                
                                <!-- Price -->
                                <div class="flex items-center justify-between mb-4">
                                    <span class="text-2xl font-bold text-purple-600">
                                        {{ $product->price_formatted }}
                                    </span>
                                    <span class="text-sm text-gray-500">
                                        <i class="fas fa-box"></i> {{ $product->stock }}
                                    </span>
                                </div>
                                
                                <!-- Actions -->
                                @auth
                                    <a href="{{ route('transactions.create', $product->id) }}" 
                                       class="block w-full bg-gradient-to-r from-purple-600 to-indigo-600 text-white text-center py-2.5 rounded-lg font-semibold hover:from-purple-700 hover:to-indigo-700 transition shadow-md hover:shadow-lg">
                                        <i class="fas fa-shopping-cart"></i> Buy Now
                                    </a>
                                @else
                                    <a href="{{ route('login') }}" 
                                       class="block w-full bg-gray-300 text-gray-700 text-center py-2.5 rounded-lg font-semibold hover:bg-gray-400 transition">
                                        <i class="fas fa-lock"></i> Login to Buy
                                    </a>
                                @endauth
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <!-- Pagination -->
                <div class="mt-8">
                    {{ $products->links() }}
                </div>
            @else
                <div class="bg-white rounded-lg shadow-lg p-12 text-center">
                    <i class="fas fa-box-open text-6xl text-gray-300 mb-4"></i>
                    <h3 class="text-xl font-semibold text-gray-700 mb-2">No products found</h3>
                    <p class="text-gray-500 mb-6">Try adjusting your filters or search terms</p>
                    <a href="{{ route('products.index') }}" class="inline-block bg-purple-600 text-white px-6 py-3 rounded-lg hover:bg-purple-700 transition">
                        <i class="fas fa-redo"></i> Reset Filters
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
