@extends('layouts.app')

@section('title', 'Add Product')

@section('content')
<div class="max-w-4xl mx-auto px-4">
    <div class="mb-6">
        <a href="{{ route('admin.products.index') }}" class="text-blue-600 hover:text-blue-800">
            <i class="fas fa-arrow-left"></i> Back to Products
        </a>
    </div>

    <div class="bg-white rounded-lg shadow p-8">
        <h1 class="text-2xl font-bold mb-6 text-gray-800">
            <i class="fas fa-plus"></i> Add New Product
        </h1>

        <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <!-- Category -->
            <div class="mb-6">
                <label class="block text-gray-700 font-semibold mb-2">Category *</label>
                <select name="category_id" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600 @error('category_id') border-red-500 @enderror" required>
                    <option value="">Select Category</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
                @error('category_id')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Product Name -->
            <div class="mb-6">
                <label class="block text-gray-700 font-semibold mb-2">Product Name *</label>
                <input 
                    type="text" 
                    name="name" 
                    value="{{ old('name') }}"
                    placeholder="e.g., Mobile Legends 100 Diamonds"
                    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600 @error('name') border-red-500 @enderror" 
                    required
                >
                @error('name')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Description -->
            <div class="mb-6">
                <label class="block text-gray-700 font-semibold mb-2">Description *</label>
                <textarea 
                    name="description" 
                    rows="4"
                    placeholder="Product description..."
                    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600 @error('description') border-red-500 @enderror" 
                    required
                >{{ old('description') }}</textarea>
                @error('description')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Price & Stock -->
            <div class="grid grid-cols-2 gap-6 mb-6">
                <div>
                    <label class="block text-gray-700 font-semibold mb-2">Price (Rp) *</label>
                    <input 
                        type="number" 
                        name="price" 
                        value="{{ old('price') }}"
                        placeholder="10000"
                        min="0"
                        step="0.01"
                        class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600 @error('price') border-red-500 @enderror" 
                        required
                    >
                    @error('price')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-gray-700 font-semibold mb-2">Stock (Display Only)</label>
                    <input 
                        type="number" 
                        name="stock" 
                        value="{{ old('stock', 999) }}"
                        placeholder="999"
                        min="0"
                        class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600 @error('stock') border-red-500 @enderror" 
                        required
                    >
                    @error('stock')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Code Format -->
            <div class="mb-6">
                <label class="block text-gray-700 font-semibold mb-2">Voucher Code Format *</label>
                <input 
                    type="text" 
                    name="code_format" 
                    value="{{ old('code_format', 'XXXX-XXXX-XXXX') }}"
                    placeholder="XXXX-XXXX-XXXX"
                    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600 @error('code_format') border-red-500 @enderror" 
                    required
                >
                <p class="text-sm text-gray-500 mt-1">Use 'X' for random characters. Example: MLBB-XXXX-XXXX</p>
                @error('code_format')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Product Image -->
            <div class="mb-6">
                <label class="block text-gray-700 font-semibold mb-2">Product Image</label>
                <input 
                    type="file" 
                    name="image" 
                    accept="image/jpeg,image/png,image/jpg"
                    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600 @error('image') border-red-500 @enderror"
                >
                <p class="text-sm text-gray-500 mt-1">Max size: 2MB. Formats: JPG, JPEG, PNG</p>
                @error('image')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Status -->
            <div class="mb-6">
                <label class="flex items-center">
                    <input 
                        type="checkbox" 
                        name="is_active" 
                        value="1" 
                        {{ old('is_active', true) ? 'checked' : '' }}
                        class="w-4 h-4 text-blue-600 rounded focus:ring-blue-500"
                    >
                    <span class="ml-2 text-gray-700">Active (Product will be visible to users)</span>
                </label>
            </div>

            <!-- Submit Button -->
            <div class="flex gap-4">
                <button type="submit" class="bg-blue-600 text-white px-8 py-3 rounded-lg hover:bg-blue-700 transition">
                    <i class="fas fa-save"></i> Save Product
                </button>
                <a href="{{ route('admin.products.index') }}" class="bg-gray-300 text-gray-700 px-8 py-3 rounded-lg hover:bg-gray-400 transition">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection