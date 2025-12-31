@extends('layouts.app')

@section('title', 'Voucher Saya')

@section('content')
<div class="max-w-6xl mx-auto px-4">
    <div class="mb-8">
        <a href="{{ route('profile.index') }}" class="text-purple-600 hover:text-purple-700 font-semibold mb-4 inline-block">
            <i class="fas fa-arrow-left"></i> Kembali ke Profil
        </a>
        <h1 class="text-4xl font-bold text-gray-900 mb-2">
            <i class="fas fa-ticket text-purple-600"></i> Voucher Saya
        </h1>
        <p class="text-gray-600">Semua kode voucher yang Anda beli dalam satu tempat</p>
    </div>

    @if($vouchers->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @foreach($vouchers as $voucher)
                <div class="bg-gradient-to-br from-purple-50 to-indigo-50 rounded-xl shadow-lg overflow-hidden border-2 {{ $voucher->is_used ? 'border-gray-300' : 'border-purple-300' }}">
                    <div class="p-6">
                        <!-- Product Info -->
                        <div class="flex items-center gap-4 mb-4">
                            <div class="w-16 h-16 bg-gradient-to-br from-purple-400 to-indigo-600 rounded-lg flex items-center justify-center flex-shrink-0">
                                @if($voucher->product->image)
                                    <img src="{{ str_starts_with($voucher->product->image, 'http') ? $voucher->product->image : asset('storage/' . $voucher->product->image) }}"
                                         alt="{{ $voucher->product->name }}"
                                         class="w-full h-full object-cover rounded-lg">
                                @else
                                    <i class="fas fa-gamepad text-2xl text-white"></i>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <h3 class="font-bold text-gray-900 truncate">{{ $voucher->product->name }}</h3>
                                <p class="text-sm text-gray-600">{{ $voucher->transaction->transaction_code }}</p>
                            </div>
                        </div>
                        
                        <!-- Voucher Code -->
                        <div class="bg-white rounded-lg p-4 mb-4 border-2 border-dashed {{ $voucher->is_used ? 'border-gray-300' : 'border-purple-300' }}">
                            <p class="text-xs text-gray-600 mb-1">Kode Voucher</p>
                            <div class="flex items-center justify-between">
                                <code class="text-xl font-mono font-bold {{ $voucher->is_used ? 'text-gray-400' : 'text-gray-900' }} select-all">
                                    {{ $voucher->code }}
                                </code>
                            </div>
                        </div>
                        
                        <!-- Actions -->
                        <div class="flex gap-2">
                            @if(!$voucher->is_used)
                                <button @click="
                                    navigator.clipboard.writeText('{{ $voucher->code }}');
                                    $el.innerHTML = '<i class=\'fas fa-check\'></i> Copied!';
                                    setTimeout(() => $el.innerHTML = '<i class=\'fas fa-copy\'></i> Salin Kode', 2000);
                                " class="flex-1 bg-purple-600 text-white py-2.5 rounded-lg hover:bg-purple-700 transition font-semibold">
                                    <i class="fas fa-copy"></i> Salin Kode
                                </button>
                            @else
                                <button disabled class="flex-1 bg-gray-300 text-gray-600 py-2.5 rounded-lg font-semibold cursor-not-allowed">
                                    <i class="fas fa-check-circle"></i> Sudah Digunakan
                                </button>
                            @endif
                            
                            <a href="{{ route('transactions.detail', $voucher->transaction->transaction_code) }}" 
                               class="px-4 py-2.5 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition font-semibold">
                                <i class="fas fa-eye"></i>
                            </a>
                        </div>
                        
                        <!-- Status -->
                        <div class="mt-4 flex items-center justify-between text-sm">
                            @if($voucher->is_used)
                                <span class="text-gray-600">
                                    <i class="fas fa-circle-check text-green-600"></i> Digunakan pada {{ $voucher->used_at->format('d M Y') }}
                                </span>
                            @else
                                <span class="text-green-600 font-semibold">
                                    <i class="fas fa-circle-check"></i> Siap digunakan
                                </span>
                            @endif
                            <span class="text-gray-500">{{ $voucher->created_at->diffForHumans() }}</span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        
        <!-- Pagination -->
        <div class="mt-8">
            {{ $vouchers->links() }}
        </div>
    @else
        <div class="bg-white rounded-xl shadow-lg p-12 text-center">
            <i class="fas fa-ticket text-6xl text-gray-300 mb-4"></i>
            <h3 class="text-xl font-semibold text-gray-700 mb-2">Belum ada voucher</h3>
            <p class="text-gray-500 mb-6">Beli produk untuk mendapatkan kode voucher</p>
            <a href="{{ route('products.index') }}"
               class="inline-block bg-gradient-to-r from-purple-600 to-indigo-600 text-white px-8 py-3 rounded-lg hover:from-purple-700 hover:to-indigo-700 transition font-semibold">
                <i class="fas fa-store"></i> Jelajahi Produk
            </a>
        </div>
    @endif
</div>
@endsection
