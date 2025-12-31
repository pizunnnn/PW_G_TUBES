@extends('layouts.app')

@section('title', 'Detail Transaksi')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">
                    <i class="fas fa-receipt text-purple-600"></i> Detail Transaksi
                </h1>
                <p class="text-gray-600 mt-2">Lihat informasi lengkap transaksi</p>
            </div>
            <a href="{{ route('admin.transactions.index') }}" class="bg-gray-600 text-white px-6 py-3 rounded-lg hover:bg-gray-700 transition font-semibold">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>

        <!-- Transaction Info -->
        <div class="bg-white rounded-xl shadow-lg p-8 mb-6">
            <div class="flex items-center justify-between mb-6 pb-6 border-b">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">{{ $transaction->transaction_code }}</h2>
                    <p class="text-gray-600">{{ $transaction->created_at->format('d F Y, H:i') }}</p>
                </div>
                <div class="flex flex-col gap-2">
                    <div>
                        <span class="text-xs text-gray-600 mr-2">Pembayaran:</span>
                        @if($transaction->payment_status === 'paid')
                            <span class="px-4 py-2 inline-flex text-sm leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                <i class="fas fa-check-circle mr-2"></i> Dibayar
                            </span>
                        @elseif($transaction->payment_status === 'pending')
                            <span class="px-4 py-2 inline-flex text-sm leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                <i class="fas fa-clock mr-2"></i> Pending
                            </span>
                        @else
                            <span class="px-4 py-2 inline-flex text-sm leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                <i class="fas fa-times-circle mr-2"></i> Gagal
                            </span>
                        @endif
                    </div>
                    <div>
                        <span class="text-xs text-gray-600 mr-2">Topup:</span>
                        @if($transaction->topup_status === 'completed')
                            <span class="px-4 py-2 inline-flex text-sm leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                <i class="fas fa-check-double mr-2"></i> Selesai
                            </span>
                        @elseif($transaction->topup_status === 'pending')
                            <span class="px-4 py-2 inline-flex text-sm leading-5 font-semibold rounded-full bg-orange-100 text-orange-800">
                                <i class="fas fa-hourglass-half mr-2"></i> Pending
                            </span>
                        @else
                            <span class="px-4 py-2 inline-flex text-sm leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                <i class="fas fa-exclamation-triangle mr-2"></i> Gagal
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Pelanggan Info -->
            <div class="mb-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">
                    <i class="fas fa-user text-purple-600"></i> Informasi Pelanggan
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-600">Nama</p>
                        <p class="font-semibold text-gray-900">{{ $transaction->user->name }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Email</p>
                        <p class="font-semibold text-gray-900">{{ $transaction->user->email }}</p>
                    </div>
                    @if($transaction->user->phone)
                        <div>
                            <p class="text-sm text-gray-600">Telepon</p>
                            <p class="font-semibold text-gray-900">{{ $transaction->user->phone }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Produk Info -->
            <div class="mb-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">
                    <i class="fas fa-box text-purple-600"></i> Informasi Produk
                </h3>
                <div class="flex items-center gap-4 p-4 bg-gray-50 rounded-lg">
                    <div class="w-20 h-20 bg-gradient-to-br from-purple-400 to-indigo-600 rounded-lg flex items-center justify-center flex-shrink-0">
                        @if($transaction->product->image)
                            <img src="{{ str_starts_with($transaction->product->image, 'http') ? $transaction->product->image : asset('storage/' . $transaction->product->image) }}"
                                 alt="{{ $transaction->product->name }}"
                                 class="w-full h-full object-cover rounded-lg">
                        @else
                            <i class="fas fa-gamepad text-white text-2xl"></i>
                        @endif
                    </div>
                    <div class="flex-1">
                        <h4 class="font-bold text-gray-900">{{ $transaction->product->name }}</h4>
                        <p class="text-sm text-gray-600">{{ $transaction->product->category->name }}</p>
                        <p class="text-sm text-gray-600 mt-1">
                            <i class="fas fa-cubes"></i> Quantity: <span class="font-semibold">{{ $transaction->quantity }}</span>
                        </p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm text-gray-600">Harga Satuan</p>
                        <p class="text-xl font-bold text-purple-600">Rp {{ number_format($transaction->product->price, 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>

            <!-- Game Account Info -->
            @if($transaction->game_user_id || $transaction->game_zone_id)
                <div class="mb-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">
                        <i class="fas fa-gamepad text-purple-600"></i> Game Account Information
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @if($transaction->game_user_id)
                            <div>
                                <p class="text-sm text-gray-600">User ID</p>
                                <p class="font-semibold text-gray-900">{{ $transaction->game_user_id }}</p>
                            </div>
                        @endif
                        @if($transaction->game_zone_id)
                            <div>
                                <p class="text-sm text-gray-600">Zone ID</p>
                                <p class="font-semibold text-gray-900">{{ $transaction->game_zone_id }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Voucher Code -->
            @if($transaction->voucherCode)
                <div class="mb-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">
                        <i class="fas fa-ticket-alt text-purple-600"></i> Voucher Code
                    </h3>
                    <div class="bg-purple-50 border-2 border-purple-300 rounded-lg p-6 text-center">
                        <p class="text-sm text-purple-600 mb-2">Game Voucher Code</p>
                        <p class="text-3xl font-mono font-bold text-purple-900">{{ $transaction->voucherCode->code }}</p>
                    </div>
                </div>
            @endif

            <!-- Payment Info -->
            <div>
                <h3 class="text-lg font-bold text-gray-900 mb-4">
                    <i class="fas fa-credit-card text-purple-600"></i> Informasi Pembayaran
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                    <div>
                        <p class="text-sm text-gray-600">Metode Pembayaran</p>
                        <p class="font-semibold text-gray-900">{{ ucfirst($transaction->payment_method ?? 'Midtrans') }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Status Pembayaran</p>
                        <p class="font-semibold text-gray-900">{{ ucfirst($transaction->payment_status) }}</p>
                    </div>
                </div>

                <!-- Rincian Harga -->
                <div class="bg-gray-50 rounded-lg p-6">
                    <h4 class="font-semibold text-gray-900 mb-4">Rincian Harga</h4>
                    <div class="space-y-3">
                        @php
                            $subtotal = $transaction->product->price * $transaction->quantity;
                        @endphp

                        <div class="flex justify-between text-gray-700">
                            <span>Subtotal ({{ $transaction->quantity }} × Rp {{ number_format($transaction->product->price, 0, ',', '.') }})</span>
                            <span class="font-semibold">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                        </div>

                        @if($transaction->discount_amount > 0)
                            <div class="flex justify-between text-green-600">
                                <span>
                                    <i class="fas fa-tag"></i> Discount
                                    @if($transaction->voucherCode)
                                        <span class="text-xs">({{ $transaction->voucherCode->code }})</span>
                                    @endif
                                </span>
                                <span class="font-semibold">- Rp {{ number_format($transaction->discount_amount, 0, ',', '.') }}</span>
                            </div>
                        @endif

                        <div class="border-t pt-3 flex justify-between text-gray-900">
                            <span class="text-lg font-bold">Total Dibayar</span>
                            <span class="text-2xl font-bold text-purple-600">Rp {{ number_format($transaction->total_price, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Perbarui Status Topup Form -->
        @if($transaction->payment_status === 'paid' && $transaction->topup_status !== 'completed')
            <div class="bg-white rounded-xl shadow-lg p-8">
                <h3 class="text-lg font-bold text-gray-900 mb-4">
                    <i class="fas fa-edit text-purple-600"></i> Perbarui Status Topup
                </h3>
                <p class="text-sm text-gray-600 mb-4">
                    Pembayaran telah dikonfirmasi oleh Midtrans. Perbarui status pengiriman topup di bawah.
                </p>
                <form action="{{ route('admin.transactions.update-status', $transaction) }}" method="POST">
                    @csrf
                    <div class="flex gap-4">
                        <select name="topup_status" class="flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-600">
                            <option value="pending" {{ $transaction->topup_status === 'pending' ? 'selected' : '' }}>Pending (Belum dikirim)</option>
                            <option value="completed" {{ $transaction->topup_status === 'completed' ? 'selected' : '' }}>Completed (Sudah dikirim)</option>
                            <option value="failed" {{ $transaction->topup_status === 'failed' ? 'selected' : '' }}>Failed (Gagal kirim)</option>
                        </select>
                        <button type="submit" class="bg-purple-600 text-white px-6 py-3 rounded-lg hover:bg-purple-700 transition font-semibold">
                            <i class="fas fa-save"></i> Perbarui Status
                        </button>
                    </div>
                </form>
            </div>
        @elseif($transaction->payment_status !== 'paid')
            <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-8">
                <div class="flex items-center gap-3">
                    <i class="fas fa-info-circle text-yellow-600 text-2xl"></i>
                    <div>
                        <h3 class="font-bold text-yellow-900">Menunggu Pembayaran</h3>
                        <p class="text-sm text-yellow-700">Status topup hanya bisa diperbarui setelah pembayaran dikonfirmasi by Midtrans.</p>
                    </div>
                </div>
            </div>
        @else
            <div class="bg-blue-50 border border-blue-200 rounded-xl p-8">
                <div class="flex items-center gap-3">
                    <i class="fas fa-check-circle text-blue-600 text-2xl"></i>
                    <div>
                        <h3 class="font-bold text-blue-900">Topup Selesai</h3>
                        <p class="text-sm text-blue-700">Voucher telah dikirim ke pelanggan.</p>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
