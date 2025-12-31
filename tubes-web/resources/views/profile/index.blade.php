@extends('layouts.app')

@section('title', 'Profil Saya')

@section('content')
<div class="max-w-6xl mx-auto px-4" x-data="{ activeTab: 'profile' }">
    <div class="mb-8">
        <h1 class="text-4xl font-bold text-gray-900 mb-2">
            <i class="fas fa-user-circle text-purple-600"></i> Profil Saya
        </h1>
        <p class="text-gray-600">Kelola pengaturan akun dan lihat aktivitas Anda</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Sidebar -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-xl shadow-lg p-6 sticky top-20">
                <!-- Profile Card -->
                <div class="text-center mb-6 pb-6 border-b">
                    <div class="w-24 h-24 bg-gradient-to-br from-purple-400 to-indigo-600 rounded-full mx-auto flex items-center justify-center mb-4">
                        <span class="text-3xl font-bold text-white">{{ substr($user->name, 0, 1) }}</span>
                    </div>
                    <h2 class="font-bold text-gray-900 text-xl">{{ $user->name }}</h2>
                    <p class="text-gray-600 text-sm">{{ $user->email }}</p>
                </div>
                
                <!-- Navigation -->
                <nav class="space-y-2">
                    <button @click="activeTab = 'profile'"
                            :class="activeTab === 'profile' ? 'bg-purple-50 text-purple-600 border-purple-600' : 'text-gray-700 hover:bg-gray-50'"
                            class="w-full text-left px-4 py-3 rounded-lg transition font-semibold border-2 border-transparent">
                        <i class="fas fa-user"></i> Edit Profil
                    </button>
                    <button @click="activeTab = 'password'"
                            :class="activeTab === 'password' ? 'bg-purple-50 text-purple-600 border-purple-600' : 'text-gray-700 hover:bg-gray-50'"
                            class="w-full text-left px-4 py-3 rounded-lg transition font-semibold border-2 border-transparent">
                        <i class="fas fa-lock"></i> Ubah Password
                    </button>
                    <button @click="activeTab = 'history'"
                            :class="activeTab === 'history' ? 'bg-purple-50 text-purple-600 border-purple-600' : 'text-gray-700 hover:bg-gray-50'"
                            class="w-full text-left px-4 py-3 rounded-lg transition font-semibold border-2 border-transparent">
                        <i class="fas fa-history"></i> Riwayat Pembelian
                    </button>
                    <a href="{{ route('profile.vouchers') }}"
                       class="block w-full text-left px-4 py-3 rounded-lg transition font-semibold border-2 border-transparent text-gray-700 hover:bg-gray-50">
                        <i class="fas fa-ticket"></i> Voucher Saya
                    </a>
                </nav>
            </div>
        </div>

        <!-- Main Content -->
        <div class="lg:col-span-2">
            <!-- Edit Profile Tab -->
            <div x-show="activeTab === 'profile'" x-transition class="bg-white rounded-xl shadow-lg p-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-6">
                    <i class="fas fa-user-edit text-purple-600"></i> Edit Profil
                </h2>
                
                <form action="{{ route('profile.update') }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="space-y-6">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Nama Lengkap</label>
                            <input type="text" name="name" value="{{ old('name', $user->name) }}"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                                   required>
                            @error('name')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Alamat Email</label>
                            <input type="email" name="email" value="{{ old('email', $user->email) }}"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                                   required>
                            @error('email')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div class="pt-4">
                            <button type="submit"
                                    class="w-full bg-gradient-to-r from-purple-600 to-indigo-600 text-white py-3 rounded-lg font-semibold hover:from-purple-700 hover:to-indigo-700 transition">
                                <i class="fas fa-save"></i> Simpan Perubahan
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Change Password Tab -->
            <div x-show="activeTab === 'password'" x-transition class="bg-white rounded-xl shadow-lg p-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-6">
                    <i class="fas fa-key text-purple-600"></i> Ubah Password
                </h2>
                
                <form action="{{ route('profile.password') }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="space-y-6">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Password Saat Ini</label>
                            <input type="password" name="current_password"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                                   required>
                            @error('current_password')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Password Baru</label>
                            <input type="password" name="password"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                                   required>
                            @error('password')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Konfirmasi Password Baru</label>
                            <input type="password" name="password_confirmation"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                                   required>
                        </div>
                        
                        <div class="pt-4">
                            <button type="submit"
                                    class="w-full bg-gradient-to-r from-purple-600 to-indigo-600 text-white py-3 rounded-lg font-semibold hover:from-purple-700 hover:to-indigo-700 transition">
                                <i class="fas fa-lock"></i> Perbarui Password
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Purchase History Tab -->
            <div x-show="activeTab === 'history'" x-transition class="bg-white rounded-xl shadow-lg p-8">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-2xl font-bold text-gray-900">
                        <i class="fas fa-shopping-bag text-purple-600"></i> Riwayat Pembelian
                    </h2>
                    <a href="{{ route('transactions.list') }}" class="text-purple-600 hover:text-purple-700 font-semibold text-sm">
                        Lihat Semua <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
                
                @if($purchaseHistory->count() > 0)
                    <div class="space-y-4">
                        @foreach($purchaseHistory as $transaction)
                            <div class="border border-gray-200 rounded-lg p-4 hover:border-purple-300 transition">
                                <div class="flex items-center gap-3">
                                    <div class="w-16 h-16 bg-gradient-to-br from-purple-400 to-indigo-600 rounded-lg flex items-center justify-center flex-shrink-0">
                                        @if($transaction->product->image)
                                            <img src="{{ str_starts_with($transaction->product->image, 'http') ? $transaction->product->image : asset('storage/' . $transaction->product->image) }}"
                                                 alt="{{ $transaction->product->name }}"
                                                 class="w-full h-full object-cover rounded-lg">
                                        @else
                                            <i class="fas fa-gamepad text-xl text-white"></i>
                                        @endif
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="font-semibold text-gray-900 truncate">{{ $transaction->product->name }}</p>
                                        <p class="text-sm text-gray-600">{{ $transaction->created_at->format('d M Y') }}</p>
                                    </div>
                                    <div class="text-right">
                                        {!! $transaction->status_badge !!}
                                        <p class="font-semibold text-purple-600 mt-1">{{ $transaction->total_price_formatted }}</p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-12">
                        <i class="fas fa-shopping-bag text-5xl text-gray-300 mb-3"></i>
                        <p class="text-gray-600">Belum ada riwayat pembelian</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
