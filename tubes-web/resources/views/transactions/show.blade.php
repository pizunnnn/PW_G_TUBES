@extends('layouts.app')

@section('title', 'Detail Transaksi')

@section('content')
<div class="max-w-4xl mx-auto px-4">
    <!-- Header --> 
    <div class="mb-8">
        <a href="{{ route('transactions.list') }}" class="text-purple-600 hover:text-purple-700 font-semibold mb-4 inline-block">
            <i class="fas fa-arrow-left"></i> Kembali ke Transaksi
        </a>
        <h1 class="text-4xl font-bold text-gray-900">
            <i class="fas fa-file-invoice text-purple-600"></i> Detail Transaksi
        </h1>
    </div>

    <div class="space-y-6">
        <!-- Transaction Info -->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <p class="text-gray-600 text-sm mb-1">Kode Transaksi</p>
                    <p class="font-mono text-2xl font-bold text-gray-900">#{{ $transaction->transaction_code }}</p>
                </div>
                <div class="text-right">
                    {!! $transaction->status_badge !!}
                    <p class="text-sm text-gray-600 mt-2">{{ $transaction->created_at->format('d M Y, H:i') }}</p>
                </div>
            </div>
            
            <!-- Product Info -->
            <div class="border-t pt-6">
                <div class="flex items-center gap-4">
                    <div class="w-24 h-24 bg-gradient-to-br from-purple-400 to-indigo-600 rounded-lg flex items-center justify-center flex-shrink-0">
                        @if($transaction->product->image)
                            <img src="{{ str_starts_with($transaction->product->image, 'http') ? $transaction->product->image : asset('storage/' . $transaction->product->image) }}"
                                 alt="{{ $transaction->product->name }}"
                                 class="w-full h-full object-cover rounded-lg">
                        @else
                            <i class="fas fa-gamepad text-3xl text-white"></i>
                        @endif
                    </div>
                    <div class="flex-1">
                        <h3 class="font-bold text-gray-900 text-xl mb-1">{{ $transaction->product->name }}</h3>
                        <p class="text-gray-600">{{ $transaction->product->category->name }}</p>
                        <p class="text-sm text-gray-600 mt-2">
                            <i class="fas fa-cubes"></i> Jumlah: <span class="font-semibold">{{ $transaction->quantity }} pcs</span>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment & Price Summary -->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-4">
                <i class="fas fa-credit-card"></i> Detail Pembayaran
            </h2>
            
            <div class="space-y-3">
                <div class="flex justify-between text-gray-700">
                    <span>Metode Pembayaran</span>
                    <span class="font-semibold capitalize">{{ str_replace('_', ' ', $transaction->payment_method) }}</span>
                </div>

                @if($transaction->paid_at)
                    <div class="flex justify-between text-gray-700">
                        <span>Dibayar Pada</span>
                        <span class="font-semibold">{{ $transaction->paid_at->format('d M Y, H:i') }}</span>
                    </div>
                @endif

                <div class="border-t pt-3 mt-3">
                    <div class="flex justify-between text-gray-700 mb-2">
                        <span>Subtotal ({{ $transaction->quantity }} item)</span>
                        <span>{{ $transaction->total_price_formatted }}</span>
                    </div>
                    
                    <div class="flex justify-between text-lg font-bold text-gray-900 pt-3 border-t">
                        <span>Total</span>
                        <span class="text-purple-600">{{ $transaction->total_price_formatted }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Topup Status -->
        @if($transaction->payment_status === 'paid')
            @if($transaction->topup_status === 'completed')
                <div class="bg-gradient-to-r from-green-50 to-emerald-50 rounded-xl shadow-lg p-6 border-2 border-green-200">
                    <div class="flex items-center gap-3 mb-3">
                        <i class="fas fa-check-circle text-4xl text-green-600"></i>
                        <div>
                            <h2 class="text-xl font-bold text-gray-900">Topup Selesai!</h2>
                            <p class="text-gray-700">{{ $transaction->product->name }} Anda telah dikirim ke akun game</p>
                        </div>
                    </div>

                    @if($transaction->game_user_id || $transaction->game_server)
                        <div class="mt-4 bg-white rounded-lg p-4 border border-green-300">
                            <p class="text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-gamepad text-purple-600"></i> Detail Akun Game:
                            </p>
                            <div class="space-y-1 text-sm">
                                @if($transaction->game_user_id)
                                    <p class="text-gray-600">User ID: <span class="font-mono font-bold text-gray-900">{{ $transaction->game_user_id }}</span></p>
                                @endif
                                @if($transaction->game_server)
                                    <p class="text-gray-600">Server: <span class="font-semibold text-gray-900">{{ $transaction->game_server }}</span></p>
                                @endif
                            </div>
                        </div>
                    @endif

                    <div class="mt-4 p-3 bg-blue-50 rounded-lg text-sm text-blue-800">
                        <i class="fas fa-info-circle"></i> Silakan cek kotak masuk in-game atau saldo akun Anda. Mungkin butuh beberapa menit untuk muncul.
                    </div>
                </div>
            @elseif($transaction->topup_status === 'failed')
                <div class="bg-red-50 rounded-xl shadow-lg p-6 border-2 border-red-200">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-exclamation-triangle text-4xl text-red-600"></i>
                        <div>
                            <h2 class="text-xl font-bold text-gray-900">Topup Gagal</h2>
                            <p class="text-gray-700">Ada masalah saat mengirim topup ke akun game Anda. Silakan hubungi support.</p>
                        </div>
                    </div>
                </div>
            @else
                <div class="bg-yellow-50 rounded-xl shadow-lg p-6 border-2 border-yellow-200">
                    <div class="flex items-center gap-3 mb-3">
                        <i class="fas fa-hourglass-half text-4xl text-yellow-600 animate-pulse"></i>
                        <div>
                            <h2 class="text-xl font-bold text-gray-900">Memproses Topup Anda</h2>
                            <p class="text-gray-700">Pembayaran Anda telah dikonfirmasi. Kami sedang mengirim {{ $transaction->product->name }} ke akun game Anda.</p>
                        </div>
                    </div>

                    @if($transaction->game_user_id || $transaction->game_server)
                        <div class="mt-4 bg-white rounded-lg p-4 border border-yellow-300">
                            <p class="text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-gamepad text-purple-600"></i> Topup akan dikirim ke:
                            </p>
                            <div class="space-y-1 text-sm">
                                @if($transaction->game_user_id)
                                    <p class="text-gray-600">User ID: <span class="font-mono font-bold text-gray-900">{{ $transaction->game_user_id }}</span></p>
                                @endif
                                @if($transaction->game_server)
                                    <p class="text-gray-600">Server: <span class="font-semibold text-gray-900">{{ $transaction->game_server }}</span></p>
                                @endif
                            </div>
                        </div>
                    @endif

                    <div class="mt-4 p-3 bg-blue-50 rounded-lg text-sm text-blue-800">
                        <i class="fas fa-info-circle"></i> Biasanya memakan waktu beberapa menit. Anda akan diberi tahu setelah selesai.
                    </div>
                </div>
            @endif
        @elseif($transaction->payment_status === 'pending')
            <div class="bg-yellow-50 rounded-xl shadow-lg p-6 border-2 border-yellow-200">
                <div class="flex items-center gap-3 mb-3">
                    <i class="fas fa-clock text-3xl text-yellow-600 animate-pulse"></i>
                    <h2 class="text-xl font-bold text-gray-900">Menunggu Pembayaran</h2>
                </div>
                <p class="text-gray-700 mb-4">Silakan selesaikan pembayaran untuk menerima kode voucher.</p>

                @if(in_array($transaction->payment_method, ['midtrans', 'qris']))
                    @if($paymentData)
                        <div class="bg-white p-4 rounded-lg border-2 border-gray-200 mb-4">
                            {{-- QRIS Payment --}}
                            @if($transaction->payment_method === 'qris' && isset($paymentData['qr_string']))
                                <p class="font-semibold text-gray-900 mb-3 text-center">Scan QR Code untuk Bayar:</p>
                                <div class="flex justify-center mb-4">
                                    <img src="{{ $paymentData['qr_string'] }}" alt="QRIS Code" class="w-64 h-64 border-4 border-purple-300 rounded-lg">
                                </div>
                                <p class="text-center text-sm text-gray-600 mb-2">
                                    <i class="fas fa-mobile-alt"></i> Gunakan aplikasi e-wallet atau mobile banking untuk scan
                                </p>

                            {{-- Virtual Account Payment --}}
                            @elseif(isset($paymentData['va_number']))
                                <p class="font-semibold text-gray-900 mb-3">Transfer ke Virtual Account:</p>
                                <div class="mb-4 p-3 bg-purple-50 rounded-lg">
                                    <p class="text-sm text-gray-700 mb-1">Bank {{ strtoupper($paymentData['bank']) }}</p>
                                    <div class="flex items-center justify-between">
                                        <p class="font-mono font-bold text-2xl text-purple-600">{{ $paymentData['va_number'] }}</p>
                                        <button onclick="copyText('{{ $paymentData['va_number'] }}')"
                                                class="px-3 py-1 bg-purple-600 text-white rounded text-sm hover:bg-purple-700">
                                            <i class="fas fa-copy"></i> Copy
                                        </button>
                                    </div>
                                </div>

                            {{-- Mandiri Bill Payment --}}
                            @elseif(isset($paymentData['bill_key']))
                                <p class="font-semibold text-gray-900 mb-3">Mandiri Bill Payment:</p>
                                <div class="space-y-2">
                                    <div class="p-3 bg-purple-50 rounded-lg">
                                        <p class="text-sm text-gray-700 mb-1">Biller Code</p>
                                        <p class="font-mono font-bold text-xl text-purple-600">{{ $paymentData['biller_code'] }}</p>
                                    </div>
                                    <div class="p-3 bg-purple-50 rounded-lg">
                                        <p class="text-sm text-gray-700 mb-1">Bill Key</p>
                                        <p class="font-mono font-bold text-xl text-purple-600">{{ $paymentData['bill_key'] }}</p>
                                    </div>
                                </div>
                            @endif

                            <div class="mt-3 pt-3 border-t">
                                <p class="text-sm text-gray-700">Total yang harus dibayar:</p>
                                <p class="font-bold text-xl text-gray-900">Rp {{ number_format($transaction->total_price, 0, ',', '.') }}</p>
                            </div>

                            @if(isset($paymentData['expiry_time']))
                                <p class="text-xs text-gray-500 mt-2">
                                    <i class="fas fa-clock"></i> Expired: {{ \Carbon\Carbon::parse($paymentData['expiry_time'])->format('d M Y, H:i') }}
                                </p>
                            @endif
                        </div>
                        <p class="text-sm text-gray-600">
                            <i class="fas fa-info-circle"></i> Selesaikan pembayaran sebelum expired, voucher akan otomatis dikirim setelah pembayaran dikonfirmasi.
                        </p>
                    @else
                        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
                            <i class="fas fa-exclamation-triangle"></i> Gagal membuat pembayaran. Silakan coba lagi.
                        </div>
                    @endif
                @elseif($transaction->payment_method === 'bank_transfer')
                    <div class="bg-white p-4 rounded-lg border-2 border-gray-200 mb-4">
                        <p class="font-semibold text-gray-900 mb-2">Instruksi Transfer Bank:</p>
                        <p class="text-sm text-gray-700">Silakan transfer ke:</p>
                        <p class="font-mono font-bold text-lg text-purple-600 my-2">BCA 1234567890</p>
                        <p class="text-sm text-gray-700">a/n ROCKETEER Store</p>
                        <p class="text-sm text-gray-700 mt-2">Jumlah: <span class="font-bold">Rp {{ number_format($transaction->total_price, 0, ',', '.') }}</span></p>
                    </div>
                    <p class="text-sm text-gray-600">Setelah transfer, voucher akan diproses dalam 1x24 jam</p>
                @elseif($transaction->payment_method === 'qris')
                    <div class="bg-white p-4 rounded-lg border-2 border-gray-200 mb-4 text-center">
                        <p class="font-semibold text-gray-900 mb-3">Scan Kode QR untuk Bayar:</p>
                        <div class="w-48 h-48 bg-gray-100 mx-auto rounded-lg flex items-center justify-center">
                            <i class="fas fa-qrcode text-6xl text-gray-400"></i>
                        </div>
                        <p class="text-sm text-gray-600 mt-3">Kode QR akan dibuat setelah integrasi payment gateway</p>
                    </div>
                @endif
            </div>
        @else
            <div class="bg-red-50 rounded-xl shadow-lg p-6 border-2 border-red-200">
                <div class="flex items-center gap-3">
                    <i class="fas fa-exclamation-circle text-3xl text-red-600"></i>
                    <div>
                        <h2 class="text-xl font-bold text-gray-900">Payment {{ ucfirst($transaction->payment_status) }}</h2>
                        <p class="text-gray-700">Transaksi ini tidak dapat diselesaikan.</p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Download Invoice -->
        @if($transaction->payment_status === 'paid')
        <div class="bg-white rounded-xl shadow-lg p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-4">
                <i class="fas fa-file-invoice"></i> Invoice
            </h2>
            <a href="{{ route('transactions.invoice', $transaction->transaction_code) }}"
               class="block w-full bg-gradient-to-r from-red-600 to-pink-600 text-white text-center py-4 rounded-lg hover:from-red-700 hover:to-pink-700 transition font-semibold shadow-lg hover:shadow-xl">
                <i class="fas fa-file-pdf"></i> Unduh Invoice (PDF)
            </a>
            <p class="text-xs text-gray-500 text-center mt-3">
                <i class="fas fa-info-circle"></i> Klik untuk mengunduh invoice transaksi Anda sebagai PDF
            </p>
        </div>
        @endif
    </div>
</div>

@if($transaction->payment_status === 'pending' && in_array($transaction->payment_method, ['midtrans', 'qris']) && $paymentData)
<script>
    function copyText(text) {
        navigator.clipboard.writeText(text).then(function() {
            alert('Berhasil dicopy: ' + text);
        }, function(err) {
            console.error('Failed to copy: ', err);
        });
    }
</script>
@endif

@endsection
