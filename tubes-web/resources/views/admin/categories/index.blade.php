@extends('layouts.app')

@section('title', 'Kelola Game')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">
                    <i class="fas fa-tags text-purple-600"></i> Kelola Game
                </h1>
                <p class="text-gray-600 mt-2">Kelola kategori game untuk produk Anda</p>
            </div>
            <a href="{{ route('admin.categories.create') }}" class="bg-gradient-to-r from-purple-600 to-indigo-600 text-white px-6 py-3 rounded-lg hover:from-purple-700 hover:to-indigo-700 transition font-semibold">
                <i class="fas fa-plus"></i> Tambah Game Baru
            </a>
        </div>

        <!-- Games Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($categories as $game)
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <h3 class="text-xl font-bold text-gray-900">{{ $game->name }}</h3>
                            <p class="text-sm text-gray-500 mt-1">{{ $game->slug }}</p>
                        </div>
                        <form action="{{ route('admin.categories.toggle', $game) }}" method="POST">
                            @csrf
                            <button type="submit" class="px-3 py-1 rounded-full text-xs font-semibold {{ $game->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $game->is_active ? 'Aktif' : 'Tidak Aktif' }}
                            </button>
                        </form>
                    </div>

                    <div class="mb-4">
                        <div class="flex items-center text-sm text-gray-600">
                            <i class="fas fa-box mr-2 text-purple-600"></i>
                            <span>{{ $game->products_count }} Produk</span>
                        </div>
                    </div>

                    <div class="flex space-x-2 pt-4 border-t">
                        <a href="{{ route('admin.categories.edit', $game) }}" class="flex-1 bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition text-center text-sm font-semibold">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        <form action="{{ route('admin.categories.destroy', $game) }}" method="POST" onsubmit="return confirm('Yakin? Ini akan mempengaruhi semua produk di game ini.')" class="flex-1">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition text-sm font-semibold">
                                <i class="fas fa-trash"></i> Hapus
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="col-span-3 bg-white rounded-xl shadow-lg p-12 text-center">
                    <i class="fas fa-inbox text-6xl text-gray-400 mb-4"></i>
                    <p class="text-gray-500">Tidak ada kategori ditemukan</p>
                </div>
            @endforelse
        </div>

        @if($categories->hasPages())
            <div class="mt-6">
                {{ $categories->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
