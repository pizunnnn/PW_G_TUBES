@extends('layouts.app')

@section('title', 'Kelola Kode Voucher')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">
                    <i class="fas fa-ticket-alt text-purple-600"></i> Voucher Diskon
                </h1>
                <p class="text-gray-600 mt-2">Buat dan kelola kode voucher diskon</p>
            </div>
            <a href="{{ route('admin.voucher-codes.create') }}" class="bg-gradient-to-r from-purple-600 to-indigo-600 text-white px-6 py-3 rounded-lg hover:from-purple-700 hover:to-indigo-700 transition font-semibold">
                <i class="fas fa-plus"></i> Create Voucher
            </a>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Status</label>
                    <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-600">
                        <option value="">All Status</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Aktif</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Kadaluarsa</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Game</label>
                    <select name="category" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-600">
                        <option value="">All Games</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Search</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Voucher code..." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-600">
                </div>
                <div class="flex items-end">
                    <button type="submit" class="w-full bg-purple-600 text-white px-6 py-2 rounded-lg hover:bg-purple-700 transition font-semibold">
                        <i class="fas fa-search"></i> Filter
                    </button>
                </div>
                <div class="flex items-end">
                    <a href="{{ route('admin.voucher-codes.index') }}" class="w-full bg-gray-600 text-white px-6 py-2 rounded-lg hover:bg-gray-700 transition font-semibold text-center">
                        <i class="fas fa-redo"></i> Reset
                    </a>
                </div>
            </form>
        </div>

        <!-- Kode Vouchers Table -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Code</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Diskon</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Game</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Min. Pembelian</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Usage</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Berlaku Hingga</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($voucherCodes as $code)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm font-mono font-bold text-purple-600 bg-purple-50 px-3 py-1 rounded">{{ $code->code }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($code->discount_type === 'percentage')
                                        <span class="text-sm font-semibold text-green-600">{{ $code->discount_value }}% OFF</span>
                                        @if($code->max_discount)
                                            <div class="text-xs text-gray-500">Max: Rp {{ number_format($code->max_discount, 0, ',', '.') }}</div>
                                        @endif
                                    @else
                                        <span class="text-sm font-semibold text-green-600">Rp {{ number_format($code->discount_value, 0, ',', '.') }} OFF</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($code->category)
                                        <span class="text-sm text-gray-900">{{ $code->category->name }}</span>
                                    @else
                                        <span class="text-sm text-gray-500 italic">All Games</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($code->min_purchase)
                                        <span class="text-sm text-gray-600">Rp {{ number_format($code->min_purchase, 0, ',', '.') }}</span>
                                    @else
                                        <span class="text-sm text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm">
                                        <span class="font-semibold {{ $code->used_count >= $code->usage_limit ? 'text-red-600' : 'text-gray-900' }}">
                                            {{ $code->used_count }}/{{ $code->usage_limit }}
                                        </span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-1.5 mt-1">
                                        <div class="bg-purple-600 h-1.5 rounded-full" style="width: {{ min(100, ($code->used_count / $code->usage_limit) * 100) }}%"></div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    @if($code->valid_until)
                                        {{ $code->valid_until->format('d M Y') }}
                                        @if($code->valid_until->isPast())
                                            <span class="text-xs text-red-600 block">Kadaluarsa</span>
                                        @endif
                                    @else
                                        <span class="text-gray-400">No expiry</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($code->is_active && $code->isAvailable())
                                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            <i class="fas fa-check-circle mr-1"></i> Aktif
                                        </span>
                                    @elseif(!$code->is_active)
                                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                            <i class="fas fa-pause-circle mr-1"></i> Inactive
                                        </span>
                                    @elseif($code->used_count >= $code->usage_limit)
                                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                            <i class="fas fa-ban mr-1"></i> Limit Reached
                                        </span>
                                    @elseif($code->valid_until && $code->valid_until->isPast())
                                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-orange-100 text-orange-800">
                                            <i class="fas fa-clock mr-1"></i> Kadaluarsa
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    @if($code->used_count == 0)
                                        <form action="{{ route('admin.voucher-codes.destroy', $code) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this voucher?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-gray-400" title="Cannot delete used voucher">
                                            <i class="fas fa-lock"></i>
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-8 text-center text-gray-500">
                                    <i class="fas fa-inbox text-4xl mb-2"></i>
                                    <p>Tidak ada voucher diskon ditemukan</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($voucherCodes->hasPages())
                <div class="px-6 py-4 bg-gray-50">
                    {{ $voucherCodes->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
