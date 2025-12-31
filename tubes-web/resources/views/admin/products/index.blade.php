@extends('layouts.app')

@section('title', 'Manage Products')

@section('content')
<div class="max-w-7xl mx-auto px-4">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800">
            <i class="fas fa-box"></i> Manage Products
        </h1>
        <a href="{{ route('admin.products.create') }}" class="bg-gradient-to-r from-blue-600 to-indigo-600 text-white px-6 py-3 rounded-lg hover:from-blue-700 hover:to-indigo-700 transition">
            <i class="fas fa-plus"></i> Add Product
        </a>
    </div>

    <!-- Search & Filter -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <form method="GET" action="{{ route('admin.products.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <input 
                type="text" 
                name="search" 
                value="{{ $search ?? '' }}"
                placeholder="Search products..." 
                class="px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600"
            >
            <select name="category" class="px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600">
                <option value="">All Categories</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ $category == $cat->id ? 'selected' : '' }}>
                        {{ $cat->name }}
                    </option>
                @endforeach
            </select>
            <div class="flex gap-2">
                <button type="submit" class="flex-1 bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition">
                    <i class="fas fa-search"></i> Search
                </button>
                @if($search || $category)
                    <a href="{{ route('admin.products.index') }}" class="bg-gray-300 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-400 transition">
                        <i class="fas fa-times"></i> Clear
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Image</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Category</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Price</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Stock</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($products as $product)
                    <tr>
                        <td class="px-6 py-4">
                            @if($product->image)
                                <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="w-16 h-16 object-cover rounded">
                            @else
                                <div class="w-16 h-16 bg-gray-200 rounded flex items-center justify-center">
                                    <i class="fas fa-image text-gray-400"></i>
                                </div>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="font-semibold text-gray-900">{{ $product->name }}</div>
                            <div class="text-xs text-gray-500">{{ Str::limit($product->description, 50) }}</div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $product->category->name }}</td>
                        <td class="px-6 py-4 font-semibold text-gray-900">{{ $product->price_formatted }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ number_format($product->stock) }}</td>
                        <td class="px-6 py-4">
                            <form action="{{ route('admin.products.toggle', $product) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="px-3 py-1 rounded-full text-xs font-semibold {{ $product->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $product->is_active ? 'Active' : 'Inactive' }}
                                </button>
                            </form>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex gap-2">
                                <a href="{{ route('admin.products.edit', $product) }}" class="text-blue-600 hover:text-blue-800">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.products.destroy', $product) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                            <i class="fas fa-inbox text-4xl mb-2"></i>
                            <p>No products found</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-6">
        {{ $products->links() }}
    </div>
</div>
@endsection