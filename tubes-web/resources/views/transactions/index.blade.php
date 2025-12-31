@extends('layouts.app')

@section('title', 'Transaksi Saya')

@section('content')
<div class="max-w-6xl mx-auto px-4">
    <div class="mb-8">
        <h1 class="text-4xl font-bold text-gray-900 mb-2">
            <i class="fas fa-receipt text-purple-600"></i> Transaksi Saya
        </h1>
        <p class="text-gray-600">Lacak pesanan dan unduh kode voucher</p>
    </div>

    @if($transactions->count() > 0)
        <div class="space-y-4">
            @foreach($transactions as $transaction)
                <div class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition">
                    <div class="p-6">
                        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                            <!-- Transaction Info -->
                            <div class="flex items-start gap-4 flex-1">
                                <div class="w-20 h-20 bg-gradient-to-br from-purple-400 to-indigo-600 rounded-lg flex items-center justify-center flex-shrink-0">
                                    @if($transaction->product->image)
                                        <img src="{{ str_starts_with($transaction->product->image, 'http') ? $transaction->product->image : asset('storage/' . $transaction->product->image) }}"
                                             alt="{{ $transaction->product->name }}"
                                             class="w-full h-full object-cover rounded-lg">
                                    @else
                                        <i class="fas fa-gamepad text-2xl text-white"></i>
                                    @endif
                                </div>
                                
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="font-mono text-sm text-gray-600">#{{ $transaction->transaction_code }}</span>
                                        {!! $transaction->status_badge !!}
                                    </div>
                                    <h3 class="font-bold text-gray-900 text-lg mb-1">{{ $transaction->product->name }}</h3>
                                    <div class="flex flex-wrap gap-3 text-sm text-gray-600">
                                        <span><i class="fas fa-calendar"></i> {{ $transaction->created_at->format('d M Y H:i') }}</span>
                                        <span><i class="fas fa-cubes"></i> {{ $transaction->quantity }} pcs</span>
                                        <span class="font-semibold text-purple-600">{{ $transaction->total_price_formatted }}</span>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Actions -->
                            <div class="flex gap-3">
                                <a href="{{ route('transactions.detail', $transaction->transaction_code) }}"
                                   class="px-6 py-2.5 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition font-semibold">
                                    <i class="fas fa-eye"></i> Lihat Detail
                                </a>

                                @if($transaction->payment_status === 'pending')
                                    <form action="{{ route('transactions.cancel', $transaction->transaction_code) }}"
                                          method="POST"
                                          onsubmit="return confirm('Yakin mau cancel transaksi ini? Stock akan dikembalikan.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="px-6 py-2.5 bg-red-600 text-white rounded-lg hover:bg-red-700 transition font-semibold">
                                            <i class="fas fa-times-circle"></i> Batal
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                        
                        <!-- Status Progress (for pending) -->
                        @if($transaction->payment_status === 'pending')
                            <div class="mt-4 p-4 bg-yellow-50 rounded-lg border border-yellow-200">
                                <div class="flex items-center gap-2 text-yellow-800">
                                    <i class="fas fa-clock animate-pulse"></i>
                                    <span class="font-semibold">Menunggu pembayaran</span>
                                </div>
                                <p class="text-sm text-yellow-700 mt-1">Silakan selesaikan pembayaran untuk menerima kode voucher</p>
                            </div>
                        @endif
                        
                        <!-- Quick Voucher Preview (for paid) -->
                        @if($transaction->payment_status === 'paid' && $transaction->voucherCodes->count() > 0)
                            <div class="mt-4 p-4 bg-green-50 rounded-lg border border-green-200">
                                <div class="flex items-center gap-2 text-green-800 mb-2">
                                    <i class="fas fa-check-circle"></i>
                                    <span class="font-semibold">Voucher Siap!</span>
                                </div>
                                <div class="flex gap-2 flex-wrap">
                                    @foreach($transaction->voucherCodes->take(3) as $voucher)
                                        <code class="px-3 py-1.5 bg-white border border-green-300 rounded font-mono text-sm text-gray-900">
                                            {{ $voucher->code }}
                                        </code>
                                    @endforeach
                                    @if($transaction->voucherCodes->count() > 3)
                                        <span class="px-3 py-1.5 bg-white border border-green-300 rounded text-sm text-gray-600">
                                            +{{ $transaction->voucherCodes->count() - 3 }} lagi
                                        </span>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
        
        <!-- Pagination -->
        <div class="mt-8">
            {{ $transactions->links() }}
        </div>
    @else
        <div class="bg-white rounded-xl shadow-lg p-12 text-center">
            <i class="fas fa-receipt text-6xl text-gray-300 mb-4"></i>
            <h3 class="text-xl font-semibold text-gray-700 mb-2">Belum ada transaksi</h3>
            <p class="text-gray-500 mb-6">Mulai berbelanja untuk melihat pesanan Anda</p>
            <a href="{{ route('products.index') }}"
               class="inline-block bg-gradient-to-r from-purple-600 to-indigo-600 text-white px-8 py-3 rounded-lg hover:from-purple-700 hover:to-indigo-700 transition font-semibold">
                <i class="fas fa-store"></i> Jelajahi Produk
            </a>
        </div>
    @endif
</div>
@endsection
