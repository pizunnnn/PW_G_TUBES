@extends('layouts.app')

@section('title', 'Edit Slider')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-bold text-gray-900">
                <i class="fas fa-edit text-purple-600"></i> Edit Slider
            </h1>
            <a href="{{ route('admin.sliders.index') }}" class="bg-gray-600 text-white px-6 py-3 rounded-lg hover:bg-gray-700 transition font-semibold">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>

        <!-- Form -->
        <div class="bg-white rounded-xl shadow-lg p-8">
            <form action="{{ route('admin.sliders.update', $slider) }}" method="POST" enctype="multipart/form-data"
                  x-data="{
                      linkType: '{{ old('link_type', $slider->link_type) }}',
                      imagePreview: null,
                      previewImage(event) {
                          const file = event.target.files[0];
                          if (file) {
                              this.imagePreview = URL.createObjectURL(file);
                          }
                      }
                  }">
                @csrf
                @method('PUT')

                <div class="space-y-6">
                    <!-- Judul -->
                    <div>
                        <label for="title" class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-heading text-purple-600"></i> Judul Slider <span class="text-gray-500 font-normal">(Opsional)</span>
                        </label>
                        <input
                            type="text"
                            name="title"
                            id="title"
                            value="{{ old('title', $slider->title) }}"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-600 focus:border-transparent @error('title') border-red-500 @enderror"
                            placeholder="Promo Spesial Tahun Baru"
                        >
                        @error('title')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Gambar Slider -->
                    <div>
                        <label for="image" class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-image text-purple-600"></i> Gambar Slider
                        </label>

                        <!-- Current Image -->
                        <div class="mb-4">
                            <p class="text-sm font-semibold text-gray-700 mb-2">Gambar Saat Ini:</p>
                            <img src="{{ asset('storage/' . $slider->image) }}" class="rounded-lg max-h-48 object-cover" alt="Current slider">
                        </div>

                        <input
                            type="file"
                            name="image"
                            id="image"
                            accept="image/*"
                            @change="previewImage($event)"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-600 focus:border-transparent @error('image') border-red-500 @enderror"
                        >
                        <p class="text-xs text-gray-500 mt-1">Format: JPG, PNG, WEBP. Max: 2MB. Kosongkan jika tidak ingin mengganti</p>
                        @error('image')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror

                        <!-- Preview New Image -->
                        <div x-show="imagePreview" class="mt-4">
                            <p class="text-sm font-semibold text-gray-700 mb-2">Preview Gambar Baru:</p>
                            <img :src="imagePreview" class="rounded-lg max-h-64 object-cover" alt="Preview">
                        </div>
                    </div>

                    <!-- Tipe Link -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-3">
                            <i class="fas fa-link text-purple-600"></i> Slider Link Ke <span class="text-red-500">*</span>
                        </label>
                        <div class="space-y-3">
                            <label class="flex items-center p-4 border-2 rounded-lg cursor-pointer transition"
                                   :class="linkType === 'none' ? 'border-purple-600 bg-purple-50' : 'border-gray-300 hover:border-purple-300'">
                                <input
                                    type="radio"
                                    name="link_type"
                                    value="none"
                                    x-model="linkType"
                                    {{ old('link_type', $slider->link_type) === 'none' ? 'checked' : '' }}
                                    class="w-4 h-4 text-purple-600"
                                >
                                <span class="ml-3 text-sm font-medium text-gray-900">Tidak ada link (Hanya tampilan)</span>
                            </label>

                            <label class="flex items-center p-4 border-2 rounded-lg cursor-pointer transition"
                                   :class="linkType === 'product' ? 'border-purple-600 bg-purple-50' : 'border-gray-300 hover:border-purple-300'">
                                <input
                                    type="radio"
                                    name="link_type"
                                    value="product"
                                    x-model="linkType"
                                    {{ old('link_type', $slider->link_type) === 'product' ? 'checked' : '' }}
                                    class="w-4 h-4 text-purple-600"
                                >
                                <span class="ml-3 text-sm font-medium text-gray-900">Link ke Produk</span>
                            </label>

                            <label class="flex items-center p-4 border-2 rounded-lg cursor-pointer transition"
                                   :class="linkType === 'url' ? 'border-purple-600 bg-purple-50' : 'border-gray-300 hover:border-purple-300'">
                                <input
                                    type="radio"
                                    name="link_type"
                                    value="url"
                                    x-model="linkType"
                                    {{ old('link_type', $slider->link_type) === 'url' ? 'checked' : '' }}
                                    class="w-4 h-4 text-purple-600"
                                >
                                <span class="ml-3 text-sm font-medium text-gray-900">Link ke URL Custom</span>
                            </label>
                        </div>
                        @error('link_type')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Pilih Produk (jika link_type = product) -->
                    <div x-show="linkType === 'product'" x-transition>
                        <label for="product_id" class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-box text-purple-600"></i> Pilih Produk
                        </label>
                        <select
                            name="link_value"
                            id="product_id"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-600 focus:border-transparent @error('link_value') border-red-500 @enderror"
                        >
                            <option value="">-- Pilih Produk --</option>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}" {{ old('link_value', $slider->link_value) == $product->id ? 'selected' : '' }}>
                                    {{ $product->name }} ({{ $product->category->name }})
                                </option>
                            @endforeach
                        </select>
                        @error('link_value')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- URL Custom (jika link_type = url) -->
                    <div x-show="linkType === 'url'" x-transition>
                        <label for="url" class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-globe text-purple-600"></i> URL Tujuan
                        </label>
                        <input
                            type="text"
                            name="link_value"
                            id="url"
                            value="{{ old('link_value', $slider->link_value) }}"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-600 focus:border-transparent @error('link_value') border-red-500 @enderror"
                            placeholder="https://example.com"
                        >
                        <p class="text-xs text-gray-500 mt-1">Masukkan URL lengkap (termasuk https://)</p>
                        @error('link_value')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Urutan -->
                    <div>
                        <label for="order" class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-sort-numeric-down text-purple-600"></i> Urutan Tampilan <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="number"
                            name="order"
                            id="order"
                            value="{{ old('order', $slider->order) }}"
                            min="0"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-600 focus:border-transparent @error('order') border-red-500 @enderror"
                            required
                        >
                        <p class="text-xs text-gray-500 mt-1">Slider dengan urutan lebih kecil akan ditampilkan lebih dulu</p>
                        @error('order')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Status Aktif -->
                    <div>
                        <label class="flex items-center">
                            <input
                                type="checkbox"
                                name="is_active"
                                value="1"
                                {{ old('is_active', $slider->is_active) ? 'checked' : '' }}
                                class="w-4 h-4 text-purple-600 border-gray-300 rounded focus:ring-purple-500"
                            >
                            <span class="ml-2 text-sm font-medium text-gray-700">Aktifkan slider ini</span>
                        </label>
                    </div>

                    <!-- Buttons -->
                    <div class="flex gap-4 pt-4">
                        <button
                            type="submit"
                            class="flex-1 bg-gradient-to-r from-purple-600 to-indigo-600 text-white font-semibold py-3 px-4 rounded-lg hover:from-purple-700 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 transition"
                        >
                            <i class="fas fa-save"></i> Perbarui Slider
                        </button>
                        <a
                            href="{{ route('admin.sliders.index') }}"
                            class="flex-1 bg-gray-600 text-white font-semibold py-3 px-4 rounded-lg hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition text-center"
                        >
                            <i class="fas fa-times"></i> Batal
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
