@extends('layouts.app')

@section('title', 'Tambah Produk Baru')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">
                <i class="fas fa-plus-circle text-purple-600"></i> Tambah Produk Baru
            </h1>
            <p class="text-gray-600 mt-2">Create a new product in your store</p>
        </div>

        <!-- Form -->
        <div class="bg-white rounded-xl shadow-lg p-8">
            <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data" x-data="{
                hasDiscount: false,
                discountType: 'percentage',
                price: {{ old('price', 0) }},
                discountValue: {{ old('discount_value', 0) }},
                get discountAmount() {
                    if (!this.hasDiscount || !this.discountValue) return 0;
                    if (this.discountType === 'percentage') {
                        return (this.price * this.discountValue) / 100;
                    }
                    return this.discountValue;
                },
                get finalHarga() {
                    return Math.max(0, this.price - this.discountAmount);
                }
            }">
                @csrf

                <!-- Nama Produk -->
                <div class="mb-6">
                    <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">
                        Nama Produk <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="text"
                        name="name"
                        id="name"
                        value="{{ old('name') }}"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-600 focus:border-transparent @error('name') border-red-500 @enderror"
                        placeholder="e.g., 100 Diamonds"
                        required
                    >
                    @error('name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Kategori -->
                <div class="mb-6">
                    <label for="category_id" class="block text-sm font-semibold text-gray-700 mb-2">
                        Kategori <span class="text-red-500">*</span>
                    </label>
                    <select
                        name="category_id"
                        id="category_id"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-600 focus:border-transparent @error('category_id') border-red-500 @enderror"
                        required
                    >
                        <option value="">Select Game</option>
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

                <!-- Harga -->
                <div class="mb-6">
                    <label for="price" class="block text-sm font-semibold text-gray-700 mb-2">
                        Harga (Rp) <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="number"
                        name="price"
                        id="price"
                        x-model.number="price"
                        value="{{ old('price') }}"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-600 focus:border-transparent @error('price') border-red-500 @enderror"
                        placeholder="15000"
                        required
                    >
                    @error('price')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Discount -->
                <div class="mb-6">
                    <div class="flex items-center justify-between mb-3">
                        <label class="block text-sm font-semibold text-gray-700">
                            <i class="fas fa-tag text-purple-600"></i> Diskon Produk
                        </label>
                        <label class="flex items-center">
                            <input
                                type="checkbox"
                                x-model="hasDiscount"
                                class="w-4 h-4 text-purple-600 border-gray-300 rounded focus:ring-purple-500"
                            >
                            <span class="ml-2 text-sm font-medium text-gray-700">Aktifkan Diskon</span>
                        </label>
                    </div>

                    <div x-show="hasDiscount" x-transition class="space-y-4 p-4 bg-purple-50 rounded-lg border-2 border-purple-200">
                        <!-- Tipe Diskon -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Tipe Diskon
                            </label>
                            <div class="flex gap-4">
                                <label class="flex items-center">
                                    <input
                                        type="radio"
                                        name="discount_type"
                                        value="percentage"
                                        x-model="discountType"
                                        class="w-4 h-4 text-purple-600 border-gray-300 focus:ring-purple-500"
                                    >
                                    <span class="ml-2 text-sm font-medium text-gray-700">Percentage (%)</span>
                                </label>
                                <label class="flex items-center">
                                    <input
                                        type="radio"
                                        name="discount_type"
                                        value="fixed"
                                        x-model="discountType"
                                        class="w-4 h-4 text-purple-600 border-gray-300 focus:ring-purple-500"
                                    >
                                    <span class="ml-2 text-sm font-medium text-gray-700">Fixed Amount (Rp)</span>
                                </label>
                            </div>
                        </div>

                        <!-- Nilai Diskon -->
                        <div>
                            <label for="discount_value" class="block text-sm font-semibold text-gray-700 mb-2">
                                Nilai Diskon
                                <span x-show="discountType === 'percentage'" class="text-gray-500 font-normal">(Masukkan 0-100)</span>
                                <span x-show="discountType === 'fixed'" class="text-gray-500 font-normal">(Masukkan jumlah dalam Rupiah)</span>
                            </label>
                            <input
                                type="number"
                                name="discount_value"
                                id="discount_value"
                                x-model.number="discountValue"
                                min="0"
                                :max="discountType === 'percentage' ? 100 : price"
                                step="0.01"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-600 focus:border-transparent"
                                :placeholder="discountType === 'percentage' ? '20' : '5000'"
                            >
                        </div>

                        <!-- Harga Preview -->
                        <div class="bg-white rounded-lg p-4 border-2 border-purple-300">
                            <div class="text-sm text-gray-600 mb-2">Harga Preview:</div>
                            <div class="flex items-baseline justify-between">
                                <div>
                                    <div class="text-lg text-gray-500 line-through" x-show="discountAmount > 0">
                                        <span x-text="'Rp ' + price.toLocaleString('id-ID')"></span>
                                    </div>
                                    <div class="text-2xl font-bold text-purple-600">
                                        <span x-text="'Rp ' + finalHarga.toLocaleString('id-ID')"></span>
                                    </div>
                                </div>
                                <div x-show="discountAmount > 0" class="text-right">
                                    <div class="text-sm text-green-600 font-semibold">
                                        Save <span x-text="'Rp ' + discountAmount.toLocaleString('id-ID')"></span>
                                    </div>
                                    <div class="text-xs text-gray-500" x-show="discountType === 'percentage'">
                                        (<span x-text="discountValue"></span>% off)
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Stok -->
                <div class="mb-6">
                    <label for="stock" class="block text-sm font-semibold text-gray-700 mb-2">
                        Stok <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="number"
                        name="stock"
                        id="stock"
                        value="{{ old('stock', 100) }}"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-600 focus:border-transparent @error('stock') border-red-500 @enderror"
                        placeholder="100"
                        required
                    >
                    @error('stock')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Deskripsi -->
                <div class="mb-6">
                    <label for="description" class="block text-sm font-semibold text-gray-700 mb-2">
                        Deskripsi
                    </label>
                    <textarea
                        name="description"
                        id="description"
                        rows="4"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-600 focus:border-transparent @error('description') border-red-500 @enderror"
                        placeholder="Product description..."
                    >{{ old('description') }}</textarea>
                    @error('description')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Image -->
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-3">
                        Product Image
                    </label>

                    <!-- Image Type Selection -->
                    <div class="flex gap-4 mb-4">
                        <label class="flex items-center">
                            <input
                                type="radio"
                                name="image_type"
                                value="url"
                                checked
                                class="w-4 h-4 text-purple-600 border-gray-300 focus:ring-purple-500"
                                onchange="toggleImageInput('url')"
                            >
                            <span class="ml-2 text-sm font-medium text-gray-700">External URL</span>
                        </label>
                        <label class="flex items-center">
                            <input
                                type="radio"
                                name="image_type"
                                value="upload"
                                class="w-4 h-4 text-purple-600 border-gray-300 focus:ring-purple-500"
                                onchange="toggleImageInput('upload')"
                            >
                            <span class="ml-2 text-sm font-medium text-gray-700">Upload File</span>
                        </label>
                    </div>

                    <!-- URL Input -->
                    <div id="url-input" class="mb-2">
                        <input
                            type="text"
                            name="image_url"
                            id="image_url"
                            value="{{ old('image_url') }}"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-600 focus:border-transparent @error('image') border-red-500 @enderror"
                            placeholder="https://example.com/image.jpg"
                        >
                        <p class="text-sm text-gray-500 mt-1">Enter external image URL (e.g., from CDN)</p>
                    </div>

                    <!-- File Upload -->
                    <div id="file-input" class="hidden">
                        <input
                            type="file"
                            name="image_file"
                            id="image_file"
                            accept="image/*"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-600 focus:border-transparent @error('image') border-red-500 @enderror"
                            onchange="previewImage(event)"
                        >
                        <p class="text-sm text-gray-500 mt-1">Upload image file (JPG, PNG, max 2MB)</p>
                        <div id="image-preview" class="mt-3 hidden">
                            <img src="" alt="Preview" class="w-32 h-32 object-cover rounded-lg border-2 border-gray-300">
                        </div>
                    </div>

                    @error('image')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Aktif Status -->
                <div class="mb-6">
                    <label class="flex items-center">
                        <input
                            type="checkbox"
                            name="is_active"
                            value="1"
                            {{ old('is_active', true) ? 'checked' : '' }}
                            class="w-4 h-4 text-purple-600 border-gray-300 rounded focus:ring-purple-500"
                        >
                        <span class="ml-2 text-sm font-semibold text-gray-700">Aktif (visible to customers)</span>
                    </label>
                </div>

                <!-- Buttons -->
                <div class="flex space-x-4">
                    <button
                        type="submit"
                        class="flex-1 bg-gradient-to-r from-purple-600 to-indigo-600 text-white font-semibold py-3 px-6 rounded-lg hover:from-purple-700 hover:to-indigo-700 transition"
                    >
                        <i class="fas fa-save"></i> Create Product
                    </button>
                    <a
                        href="{{ route('admin.products.index') }}"
                        class="flex-1 bg-gray-600 text-white font-semibold py-3 px-6 rounded-lg hover:bg-gray-700 transition text-center"
                    >
                        <i class="fas fa-times"></i> Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function toggleImageInput(type) {
        const urlInput = document.getElementById('url-input');
        const fileInput = document.getElementById('file-input');

        if (type === 'url') {
            urlInput.classList.remove('hidden');
            fileInput.classList.add('hidden');
            document.getElementById('image_file').value = '';
            document.getElementById('image-preview').classList.add('hidden');
        } else {
            urlInput.classList.add('hidden');
            fileInput.classList.remove('hidden');
            document.getElementById('image_url').value = '';
        }
    }

    function previewImage(event) {
        const preview = document.getElementById('image-preview');
        const previewImg = preview.querySelector('img');
        const file = event.target.files[0];

        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                previewImg.src = e.target.result;
                preview.classList.remove('hidden');
            };
            reader.readAsDataURL(file);
        } else {
            preview.classList.add('hidden');
        }
    }
</script>
@endsection
