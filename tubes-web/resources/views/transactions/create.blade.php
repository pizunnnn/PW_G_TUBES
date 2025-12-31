@extends('layouts.app')

@section('title', 'Pembayaran')

@section('content')
<div class="max-w-4xl mx-auto px-4">
    <h1 class="text-4xl font-bold text-gray-900 mb-8">
        <i class="fas fa-shopping-cart text-purple-600"></i> Pembayaran
    </h1>

    <form action="{{ route('transactions.store') }}" method="POST" id="checkoutForm" x-data="{
        quantity: 1,
        price: {{ $product->price }},
        productDiscount: {{ $product->hasDiscount() ? $product->getDiscountAmount() : 0 }},
        productDiscountType: '{{ $product->discount_type ?? '' }}',
        productDiscountValue: {{ $product->discount_value ?? 0 }},
        paymentMethod: 'midtrans',
        voucherCode: '',
        voucherDiscount: 0,
        voucherMessage: '',
        voucherApplied: false,
        applyingVoucher: false,
        showConfirmModal: false,
        accountFields: {},
        get subtotal() { return this.quantity * this.price; },
        get itemDiscount() { return this.quantity * this.productDiscount; },
        get totalBeforeVoucher() { return this.subtotal - this.itemDiscount; },
        get totalDiscount() { return this.itemDiscount + this.voucherDiscount; },
        get total() { return Math.max(0, this.totalBeforeVoucher - this.voucherDiscount); },
        get isFormValid() {
            @if($product->account_fields)
                @foreach($product->account_fields['fields'] ?? [] as $field)
                    @if($field['required'] ?? true)
                        if (!this.accountFields['{{ $field['name'] }}']) return false;
                    @endif
                @endforeach
            @endif
            return true;
        },
        async applyVoucher() {
            if (!this.voucherCode) return;

            this.applyingVoucher = true;
            this.voucherMessage = '';

            try {
                const response = await fetch('{{ route('transactions.apply-voucher') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        voucher_code: this.voucherCode,
                        product_id: {{ $product->id }},
                        amount: this.totalBeforeVoucher
                    })
                });

                const data = await response.json();

                if (data.success) {
                    this.voucherDiscount = data.discount;
                    this.voucherApplied = true;
                    this.voucherMessage = data.message;
                } else {
                    this.voucherDiscount = 0;
                    this.voucherApplied = false;
                    this.voucherMessage = data.message;
                }
            } catch (error) {
                this.voucherMessage = 'Gagal menerapkan voucher';
            } finally {
                this.applyingVoucher = false;
            }
        },
        removeVoucher() {
            this.voucherCode = '';
            this.voucherDiscount = 0;
            this.voucherApplied = false;
            this.voucherMessage = '';
        }
    }">
        @csrf
        <input type="hidden" name="product_id" value="{{ $product->id }}">
        <input type="hidden" name="voucher_code" x-model="voucherCode" x-bind:value="voucherApplied ? voucherCode : ''">

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Order Form -->
            <div class="space-y-6">
                <!-- Product Info -->
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">
                        <i class="fas fa-box"></i> Detail Produk
                    </h2>
                    
                    <div class="flex items-center gap-4">
                        <div class="w-24 h-24 bg-gradient-to-br from-purple-400 to-indigo-600 rounded-lg flex items-center justify-center flex-shrink-0">
                            @if($product->image)
                                <img src="{{ str_starts_with($product->image, 'http') ? $product->image : asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="w-full h-full object-cover rounded-lg">
                            @else
                                <i class="fas fa-gamepad text-3xl text-white"></i>
                            @endif
                        </div>
                        <div class="flex-1">
                            <h3 class="font-bold text-gray-900 text-lg">{{ $product->name }}</h3>
                            <p class="text-sm text-gray-600">{{ $product->category->name }}</p>
                            <p class="text-xl font-bold text-purple-600 mt-2">{{ $product->price_formatted }}</p>
                        </div>
                    </div>
                </div>

                <!-- Game Account Information -->
                @if($product->account_fields)
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <label class="block text-lg font-bold text-gray-900 mb-4">
                        <i class="fas fa-gamepad"></i> {{ $product->account_fields['title'] ?? 'Informasi Akun Game' }}
                    </label>

                    @foreach($product->account_fields['fields'] ?? [] as $field)
                        <div class="mb-4">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                {{ $field['label'] }}
                                @if($field['required'] ?? true)
                                    <span class="text-red-500">*</span>
                                @endif
                            </label>

                            @if($field['type'] === 'select')
                                <select name="{{ $field['name'] }}"
                                        x-model="accountFields['{{ $field['name'] }}']"
                                        @if($field['required'] ?? true) required @endif
                                        class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                                    <option value="">Pilih {{ $field['label'] }}</option>
                                    @foreach($field['options'] ?? [] as $option)
                                        <option value="{{ $option['value'] }}">{{ $option['label'] }}</option>
                                    @endforeach
                                </select>
                            @else
                                <input type="{{ $field['type'] ?? 'text' }}"
                                       name="{{ $field['name'] }}"
                                       x-model="accountFields['{{ $field['name'] }}']"
                                       placeholder="{{ $field['placeholder'] ?? '' }}"
                                       @if($field['required'] ?? true) required @endif
                                       class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                            @endif

                            @if(isset($field['hint']))
                                <p class="text-xs text-gray-500 mt-1">{{ $field['hint'] }}</p>
                            @endif

                            @error($field['name'])
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    @endforeach
                </div>
                @endif

                <!-- Quantity -->
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <label class="block text-lg font-bold text-gray-900 mb-4">
                        <i class="fas fa-sort-numeric-up"></i> Jumlah
                    </label>
                    
                    <div class="flex items-center gap-4">
                        <button type="button" @click="quantity = Math.max(1, quantity - 1)" 
                                class="w-12 h-12 bg-gray-200 rounded-lg hover:bg-gray-300 transition font-bold text-xl">
                            -
                        </button>
                        
                        <input type="number" name="quantity" x-model="quantity" min="1" max="{{ $product->stock }}" 
                               class="w-24 text-center text-2xl font-bold border-2 border-purple-200 rounded-lg py-2 focus:ring-2 focus:ring-purple-500">
                        
                        <button type="button" @click="quantity = Math.min({{ $product->stock }}, quantity + 1)" 
                                class="w-12 h-12 bg-gray-200 rounded-lg hover:bg-gray-300 transition font-bold text-xl">
                            +
                        </button>
                        
                        <span class="text-gray-600 ml-4">
                            <i class="fas fa-box"></i> Maks: {{ $product->stock }}
                        </span>
                    </div>
                    
                    @error('quantity')
                        <p class="text-red-600 text-sm mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Payment Method -->
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <label class="block text-lg font-bold text-gray-900 mb-4">
                        <i class="fas fa-credit-card"></i> Metode Pembayaran
                    </label>
                    
                    <div class="space-y-3">
                        <label class="flex items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-purple-500 transition has-[:checked]:border-purple-600 has-[:checked]:bg-purple-50">
                            <input type="radio" name="payment_method" value="midtrans" checked
                                   @click="paymentMethod = 'midtrans'"
                                   class="text-purple-600 focus:ring-purple-500">
                            <div class="ml-3 flex-1">
                                <div class="font-semibold text-gray-900">Midtrans Virtual Account</div>
                                <div class="text-sm text-gray-600">BCA, BNI, BRI, Mandiri, Permata</div>
                            </div>
                            <i class="fas fa-university text-2xl text-purple-600"></i>
                        </label>

                        <!-- Bank Selection for Midtrans -->
                        <div x-show="paymentMethod === 'midtrans'" x-transition class="ml-4 p-4 bg-purple-50 rounded-lg border-2 border-purple-200">
                            <label class="block text-sm font-semibold text-gray-900 mb-2">
                                <i class="fas fa-building"></i> Pilih Bank
                            </label>
                            <select name="bank" required class="w-full px-4 py-3 border-2 border-purple-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                                <option value="bca">BCA (Bank Central Asia)</option>
                                <option value="bni">BNI (Bank Negara Indonesia)</option>
                                <option value="bri">BRI (Bank Rakyat Indonesia)</option>
                                <option value="mandiri">Mandiri</option>
                                <option value="permata">Permata Bank</option>
                            </select>
                        </div>

                        <label class="flex items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-purple-500 transition has-[:checked]:border-purple-600 has-[:checked]:bg-purple-50">
                            <input type="radio" name="payment_method" value="qris"
                                   @click="paymentMethod = 'qris'"
                                   class="text-purple-600 focus:ring-purple-500">
                            <div class="ml-3 flex-1">
                                <div class="font-semibold text-gray-900">QRIS</div>
                                <div class="text-sm text-gray-600">Semua e-wallet & mobile banking (GoPay, OVO, DANA, dll)</div>
                            </div>
                            <i class="fas fa-qrcode text-2xl text-purple-600"></i>
                        </label>

                        <label class="flex items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-purple-500 transition has-[:checked]:border-purple-600 has-[:checked]:bg-purple-50">
                            <input type="radio" name="payment_method" value="bank_transfer"
                                   @click="paymentMethod = 'bank_transfer'"
                                   class="text-purple-600 focus:ring-purple-500">
                            <div class="ml-3 flex-1">
                                <div class="font-semibold text-gray-900">Transfer Bank (Manual)</div>
                                <div class="text-sm text-gray-600">Transfer manual ke rekening kami</div>
                            </div>
                            <i class="fas fa-building-columns text-2xl text-purple-600"></i>
                        </label>
                    </div>
                    
                    @error('payment_method')
                        <p class="text-red-600 text-sm mt-2">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Order Summary -->
            <div>
                <div class="bg-white rounded-xl shadow-lg p-6 sticky top-20">
                    <h2 class="text-xl font-bold text-gray-900 mb-6">
                        <i class="fas fa-file-invoice"></i> Ringkasan Pesanan
                    </h2>

                    <div class="space-y-4 mb-6">
                        <div class="flex justify-between text-gray-700">
                            <span>Harga per item</span>
                            <span class="font-semibold" x-text="'Rp ' + price.toLocaleString('id-ID')"></span>
                        </div>

                        <div class="flex justify-between text-gray-700">
                            <span>Jumlah</span>
                            <span class="font-semibold" x-text="quantity + ' pcs'"></span>
                        </div>

                        <div class="border-t pt-4">
                            <div class="flex justify-between text-gray-700 mb-2">
                                <span>Subtotal</span>
                                <span class="font-semibold" x-text="'Rp ' + subtotal.toLocaleString('id-ID')"></span>
                            </div>

                            <!-- Product Discount -->
                            <div x-show="itemDiscount > 0" class="flex justify-between text-sm text-green-600">
                                <span>
                                    Diskon
                                    <span x-show="productDiscountType === 'percentage'" x-text="'(' + productDiscountValue + '%)'"></span>
                                </span>
                                <span x-text="'- Rp ' + itemDiscount.toLocaleString('id-ID')"></span>
                            </div>

                            <!-- Voucher Discount -->
                            <div x-show="voucherDiscount > 0" class="flex justify-between text-sm text-green-600 items-center">
                                <span class="flex items-center gap-1">
                                    <i class="fas fa-ticket-alt"></i> Voucher
                                </span>
                                <div class="flex items-center gap-2">
                                    <span x-text="'- Rp ' + voucherDiscount.toLocaleString('id-ID')"></span>
                                    <button type="button" @click="removeVoucher()" class="text-red-600 hover:text-red-700">
                                        <i class="fas fa-times-circle"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="border-t pt-4">
                            <div class="flex justify-between items-baseline">
                                <span class="text-lg font-bold text-gray-900">Total</span>
                                <div class="text-right">
                                    <div class="text-2xl font-bold text-purple-600" x-text="'Rp ' + total.toLocaleString('id-ID')"></div>
                                    <div x-show="totalDiscount > 0" class="text-sm text-gray-500 line-through" x-text="'Rp ' + subtotal.toLocaleString('id-ID')"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Voucher Code Input -->
                        <div class="border-t pt-4" x-show="!voucherApplied">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-ticket-alt"></i> Punya Kode Voucher?
                            </label>
                            <div class="flex gap-2">
                                <input type="text"
                                       x-model="voucherCode"
                                       placeholder="Masukkan kode voucher"
                                       class="flex-1 px-4 py-2 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent uppercase">
                                <button type="button"
                                        @click="applyVoucher()"
                                        :disabled="applyingVoucher || !voucherCode"
                                        class="px-6 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition disabled:opacity-50 disabled:cursor-not-allowed">
                                    <span x-show="!applyingVoucher">Pakai</span>
                                    <span x-show="applyingVoucher"><i class="fas fa-spinner fa-spin"></i></span>
                                </button>
                            </div>
                            <p x-show="voucherMessage"
                               class="text-sm mt-2"
                               :class="voucherApplied ? 'text-green-600' : 'text-red-600'"
                               x-text="voucherMessage"></p>
                        </div>
                    </div>

                    <button type="button"
                            @click="showConfirmModal = true"
                            :disabled="!isFormValid"
                            class="w-full bg-gradient-to-r from-purple-600 to-indigo-600 text-white py-4 rounded-xl font-bold text-lg hover:from-purple-700 hover:to-indigo-700 transition shadow-lg hover:shadow-xl transform hover:-translate-y-1 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none">
                        <i class="fas fa-lock"></i> Lanjutkan ke Pembayaran
                    </button>

                    <p x-show="!isFormValid" class="text-sm text-red-600 text-center mt-2">
                        <i class="fas fa-exclamation-circle"></i> Harap lengkapi semua field yang required
                    </p>

                    <div class="mt-4 p-3 bg-blue-50 rounded-lg text-sm text-blue-800">
                        <i class="fas fa-info-circle"></i> Topup akan diproses setelah konfirmasi pembayaran
                    </div>
                </div>
            </div>
        </div>

        <!-- Confirmation Modal -->
        <div x-show="showConfirmModal"
             x-cloak
             @click.away="showConfirmModal = false"
             class="fixed inset-0 z-50 overflow-y-auto"
             aria-labelledby="modal-title"
             role="dialog"
             aria-modal="true">
            <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <!-- Background overlay -->
                <div x-show="showConfirmModal"
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
                     aria-hidden="true"></div>

                <!-- Center modal -->
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <!-- Modal panel -->
                <div x-show="showConfirmModal"
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">

                    <div class="bg-gradient-to-r from-purple-600 to-indigo-600 px-6 py-4">
                        <h3 class="text-2xl font-bold text-white flex items-center gap-2">
                            <i class="fas fa-shield-alt"></i>
                            Konfirmasi Pembayaran
                        </h3>
                    </div>

                    <div class="bg-white px-6 py-6">
                        <div class="mb-6">
                            <p class="text-gray-700 mb-4">Pastikan data berikut sudah benar sebelum melanjutkan pembayaran:</p>

                            <div class="bg-gray-50 rounded-lg p-4 space-y-3">
                                <div class="flex justify-between items-center border-b pb-2">
                                    <span class="text-gray-600">Produk</span>
                                    <span class="font-semibold text-gray-900">{{ $product->name }}</span>
                                </div>

                                <div class="flex justify-between items-center border-b pb-2">
                                    <span class="text-gray-600">Jumlah</span>
                                    <span class="font-semibold text-gray-900" x-text="quantity + ' pcs'"></span>
                                </div>

                                <div class="flex justify-between items-center border-b pb-2">
                                    <span class="text-gray-600">Metode Pembayaran</span>
                                    <span class="font-semibold text-gray-900 capitalize" x-text="paymentMethod === 'midtrans' ? 'Virtual Account' : paymentMethod === 'qris' ? 'QRIS' : 'Bank Transfer'"></span>
                                </div>

                                <div x-show="totalDiscount > 0" class="flex justify-between items-center border-b pb-2 text-green-600">
                                    <span>Total Diskon</span>
                                    <span class="font-semibold" x-text="'- Rp ' + totalDiscount.toLocaleString('id-ID')"></span>
                                </div>

                                <div class="flex justify-between items-center pt-2">
                                    <span class="text-lg font-bold text-gray-900">Total Bayar</span>
                                    <span class="text-2xl font-bold text-purple-600" x-text="'Rp ' + total.toLocaleString('id-ID')"></span>
                                </div>
                            </div>

                            <div class="mt-4 p-4 bg-yellow-50 border-l-4 border-yellow-400 rounded">
                                <div class="flex items-start">
                                    <i class="fas fa-exclamation-triangle text-yellow-600 mt-1 mr-2"></i>
                                    <p class="text-sm text-yellow-800">
                                        <strong>Perhatian:</strong> Setelah pembayaran dibuat, pastikan Anda menyelesaikan pembayaran dalam waktu yang ditentukan.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="flex gap-3">
                            <button type="button"
                                    @click="showConfirmModal = false"
                                    class="flex-1 px-6 py-3 border-2 border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition font-semibold">
                                <i class="fas fa-times"></i> Batal
                            </button>
                            <button type="button"
                                    @click="$el.closest('form').submit()"
                                    class="flex-1 px-6 py-3 bg-gradient-to-r from-purple-600 to-indigo-600 text-white rounded-lg hover:from-purple-700 hover:to-indigo-700 transition font-semibold shadow-lg">
                                <i class="fas fa-check"></i> Ya, Lanjutkan
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<style>
    [x-cloak] { display: none !important; }
</style>

@endsection
