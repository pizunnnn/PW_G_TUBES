@extends('layouts.app')

@section('title', 'Kelola Slider')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">
                    <i class="fas fa-images text-purple-600"></i> Kelola Slider
                </h1>
                <p class="text-gray-600 mt-2">Tambah, edit, atau hapus slider homepage</p>
            </div>
            <a href="{{ route('admin.sliders.create') }}" class="bg-gradient-to-r from-purple-600 to-indigo-600 text-white px-6 py-3 rounded-lg hover:from-purple-700 hover:to-indigo-700 transition font-semibold">
                <i class="fas fa-plus"></i> Tambah Slider Baru
            </a>
        </div>

        <!-- Success Message -->
        @if(session('success'))
            <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-6">
                <i class="fas fa-check-circle"></i> {{ session('success') }}
            </div>
        @endif

        <!-- Sliders Table -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Preview</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Judul</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Link Ke</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Urutan</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($sliders as $slider)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="w-32 h-20 bg-gray-200 rounded-lg overflow-hidden">
                                        <img src="{{ asset('storage/' . $slider->image) }}"
                                             alt="{{ $slider->title }}"
                                             class="w-full h-full object-cover">
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-semibold text-gray-900">
                                        {{ $slider->title ?? '-' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    @if($slider->link_type === 'product' && $slider->product)
                                        <div class="text-sm text-gray-900">
                                            <i class="fas fa-box text-purple-600"></i> {{ $slider->product->name }}
                                        </div>
                                    @elseif($slider->link_type === 'url')
                                        <div class="text-sm text-gray-900">
                                            <i class="fas fa-link text-blue-600"></i> {{ Str::limit($slider->link_value, 30) }}
                                        </div>
                                    @else
                                        <div class="text-sm text-gray-500">
                                            <i class="fas fa-ban"></i> Tidak ada link
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm font-semibold text-gray-900">{{ $slider->order }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <form action="{{ route('admin.sliders.toggle', $slider) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="px-3 py-1 rounded-full text-xs font-semibold {{ $slider->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ $slider->is_active ? 'Aktif' : 'Tidak Aktif' }}
                                        </button>
                                    </form>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <div class="flex space-x-2">
                                        <a href="{{ route('admin.sliders.edit', $slider) }}" class="text-blue-600 hover:text-blue-900">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.sliders.destroy', $slider) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus slider ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                    <i class="fas fa-inbox text-4xl mb-2"></i>
                                    <p>Belum ada slider</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($sliders->hasPages())
                <div class="px-6 py-4 bg-gray-50">
                    {{ $sliders->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
