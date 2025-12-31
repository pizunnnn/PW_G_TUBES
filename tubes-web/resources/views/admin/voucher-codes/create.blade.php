@extends('layouts.app')

@section('title', 'Buat Voucher Diskon')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">
                <i class="fas fa-plus-circle text-purple-600"></i> Buat Voucher Diskon
            </h1>
            <p class="text-gray-600 mt-2">Buat kode voucher diskon baru untuk pelanggan</p>
        </div>

        <!-- Form -->
        <div class="bg-white rounded-xl shadow-lg p-8">
            <form action="{{ route('admin.voucher-codes.store') }}" method="POST" x-data="{
                diskonType: '{{ old('discount_type', 'percentage') }}',
                diskonValue: {{ old('discount_value', 0) }},
                minPurchase: {{ old('min_purchase', 0) }},
                get diskonPreview() {
                    if (!this.discountValue || !this.minPurchase) return 'Masukkan nilai untuk pratinjau';
                    if (this.discountType === 'percentage') {
                        let diskon = (this.minPurchase * this.discountValue) / 100;
                        return 'Contoh: Pembelian min Rp ' + this.minPurchase.toLocaleString('id-ID') + ' mendapat Rp ' + diskon.toLocaleString('id-ID') + ' diskon (' + this.discountValue + '% off)';
                    } else {
                        return 'Contoh: Pembelian min Rp ' + this.minPurchase.toLocaleString('id-ID') + ' mendapat Rp ' + this.discountValue.toLocaleString('id-ID') + ' diskon';
                    }
                }
            }">
                @csrf

                <!-- Kode Voucher -->
                <div class="mb-6">
                    <label for="code" class="block text-sm font-semibold text-gray-700 mb-2">
                        Kode Voucher <span class="text-gray-500 font-normal">(Leave empty to auto-generate)</span>
                    </label>
                    <div class="flex gap-2">
                        <input
                            type="text"
                            name="code"
                            id="code"
                            value="{{ old('code') }}"
                            class="flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-600 focus:border-transparent font-mono uppercase @error('code') border-red-500 @enderror"
                            placeholder="e.g., WELCOME20"
                        >
                        <button
                            type="button"
                            onclick="generateCode()"
                            class="bg-purple-600 text-white px-4 py-3 rounded-lg hover:bg-purple-700 transition font-semibold whitespace-nowrap"
                        >
                            <i class="fas fa-random"></i> Generate
                        </button>
                    </div>
                    @error('code')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Tipe Diskon & Value -->
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-3">
                        Discount <span class="text-red-500">*</span>
                    </label>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Tipe Diskon -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Tipe Diskon</label>
                            <div class="space-y-2">
                                <label class="flex items-center p-3 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-purple-500 transition has-[:checked]:border-purple-600 has-[:checked]:bg-purple-50">
                                    <input
                                        type="radio"
                                        name="discount_type"
                                        value="percentage"
                                        x-model="discountType"
                                        {{ old('discount_type', 'percentage') === 'percentage' ? 'checked' : '' }}
                                        class="text-purple-600 focus:ring-purple-500"
                                        required
                                    >
                                    <span class="ml-2 text-sm font-medium">Percentage (%)</span>
                                </label>
                                <label class="flex items-center p-3 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-purple-500 transition has-[:checked]:border-purple-600 has-[:checked]:bg-purple-50">
                                    <input
                                        type="radio"
                                        name="discount_type"
                                        value="fixed"
                                        x-model="discountType"
                                        {{ old('discount_type') === 'fixed' ? 'checked' : '' }}
                                        class="text-purple-600 focus:ring-purple-500"
                                    >
                                    <span class="ml-2 text-sm font-medium">Fixed Amount (Rp)</span>
                                </label>
                            </div>
                        </div>

                        <!-- Nilai Diskon -->
                        <div>
                            <label for="discount_value" class="block text-sm font-medium text-gray-700 mb-2">
                                Nilai Diskon
                            </label>
                            <input
                                type="number"
                                name="discount_value"
                                id="discount_value"
                                x-model.number="discountValue"
                                value="{{ old('discount_value') }}"
                                min="0"
                                step="0.01"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-600 focus:border-transparent @error('discount_value') border-red-500 @enderror"
                                :placeholder="discountType === 'percentage' ? '20' : '5000'"
                                required
                            >
                            <p class="text-xs text-gray-500 mt-1" x-show="discountType === 'percentage'">Enter percentage (0-100)</p>
                            <p class="text-xs text-gray-500 mt-1" x-show="discountType === 'fixed'">Enter amount in Rupiah</p>
                            @error('discount_value')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Game Category -->
                <div class="mb-6">
                    <label for="category_id" class="block text-sm font-semibold text-gray-700 mb-2">
                        Applicable Game <span class="text-gray-500 font-normal">(Optional - leave empty for all games)</span>
                    </label>
                    <select
                        name="category_id"
                        id="category_id"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-600 focus:border-transparent @error('category_id') border-red-500 @enderror"
                    >
                        <option value="">All Games</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    <p class="text-xs text-gray-500 mt-1">Restrict voucher to specific game only</p>
                    @error('category_id')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Min. Pembelian & Maks Diskon -->
                <div class="mb-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Min. Pembelian -->
                        <div>
                            <label for="min_purchase" class="block text-sm font-semibold text-gray-700 mb-2">
                                Minimum Purchase (Rp) <span class="text-gray-500 font-normal">(Optional)</span>
                            </label>
                            <input
                                type="number"
                                name="min_purchase"
                                id="min_purchase"
                                x-model.number="minPurchase"
                                value="{{ old('min_purchase') }}"
                                min="0"
                                step="1000"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-600 focus:border-transparent @error('min_purchase') border-red-500 @enderror"
                                placeholder="e.g., 50000"
                            >
                            <p class="text-xs text-gray-500 mt-1">Minimum amount required to use this voucher</p>
                            @error('min_purchase')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Maks Diskon (for percentage only) -->
                        <div x-show="discountType === 'percentage'">
                            <label for="max_discount" class="block text-sm font-semibold text-gray-700 mb-2">
                                Maks Diskon (Rp) <span class="text-gray-500 font-normal">(Optional)</span>
                            </label>
                            <input
                                type="number"
                                name="max_discount"
                                id="max_discount"
                                value="{{ old('max_discount') }}"
                                min="0"
                                step="1000"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-600 focus:border-transparent @error('max_discount') border-red-500 @enderror"
                                placeholder="e.g., 10000"
                            >
                            <p class="text-xs text-gray-500 mt-1">Jumlah diskon maksimum (batas untuk persentase)</p>
                            @error('max_discount')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Discount Preview -->
                    <div class="mt-3 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                        <p class="text-sm text-blue-800" x-text="discountPreview"></p>
                    </div>
                </div>

                <!-- Validity Period -->
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-3">
                        Validity Period <span class="text-gray-500 font-normal">(Optional)</span>
                    </label>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="valid_from" class="block text-sm font-medium text-gray-700 mb-2">Valid From</label>
                            <input
                                type="datetime-local"
                                name="valid_from"
                                id="valid_from"
                                value="{{ old('valid_from') }}"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-600 focus:border-transparent @error('valid_from') border-red-500 @enderror"
                            >
                            @error('valid_from')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="valid_until" class="block text-sm font-medium text-gray-700 mb-2">Berlaku Hingga</label>
                            <input
                                type="datetime-local"
                                name="valid_until"
                                id="valid_until"
                                value="{{ old('valid_until') }}"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-600 focus:border-transparent @error('valid_until') border-red-500 @enderror"
                            >
                            @error('valid_until')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Leave empty for no expiration date</p>
                </div>

                <!-- Batas Penggunaan -->
                <div class="mb-6">
                    <label for="usage_limit" class="block text-sm font-semibold text-gray-700 mb-2">
                        Batas Penggunaan <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="number"
                        name="usage_limit"
                        id="usage_limit"
                        value="{{ old('usage_limit', 100) }}"
                        min="1"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-600 focus:border-transparent @error('usage_limit') border-red-500 @enderror"
                        required
                    >
                    <p class="text-xs text-gray-500 mt-1">Maximum number of times this voucher can be used</p>
                    @error('usage_limit')
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
                        <span class="ml-2 text-sm font-semibold text-gray-700">Aktif (users can use this voucher immediately)</span>
                    </label>
                </div>

                <!-- Buttons -->
                <div class="flex space-x-4">
                    <button
                        type="submit"
                        class="flex-1 bg-gradient-to-r from-purple-600 to-indigo-600 text-white font-semibold py-3 px-6 rounded-lg hover:from-purple-700 hover:to-indigo-700 transition"
                    >
                        <i class="fas fa-save"></i> Create Voucher
                    </button>
                    <a
                        href="{{ route('admin.voucher-codes.index') }}"
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
    function generateCode() {
        const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        let code = '';
        for (let i = 0; i < 8; i++) {
            code += chars.charAt(Math.floor(Math.random() * chars.length));
        }
        document.getElementById('code').value = code;
    }
</script>
@endsection
