@extends('layouts.app')

@section('title', 'Add Category')

@section('content')
<div class="max-w-3xl mx-auto px-4">
    <div class="mb-6">
        <a href="{{ route('admin.categories.index') }}" class="text-purple-600 hover:text-purple-800">
            <i class="fas fa-arrow-left"></i> Back to Categories
        </a>
    </div>

    <div class="bg-white rounded-lg shadow p-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-6">
            <i class="fas fa-plus-circle text-purple-600"></i> Add New Category
        </h1>

        <form action="{{ route('admin.categories.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <!-- Name -->
            <div class="mb-6">
                <label for="name" class="block text-gray-700 font-semibold mb-2">
                    <i class="fas fa-tag"></i> Category Name *
                </label>
                <input 
                    type="text" 
                    name="name" 
                    id="name" 
                    value="{{ old('name') }}"
                    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-600 @error('name') border-red-500 @enderror"
                    required
                >
                @error('name')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Description -->
            <div class="mb-6">
                <label for="description" class="block text-gray-700 font-semibold mb-2">
                    <i class="fas fa-align-left"></i> Description
                </label>
                <textarea 
                    name="description" 
                    id="description" 
                    rows="4"
                    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-600 @error('description') border-red-500 @enderror"
                >{{ old('description') }}</textarea>
                @error('description')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Icon Upload -->
            <div class="mb-6">
                <label for="icon" class="block text-gray-700 font-semibold mb-2">
                    <i class="fas fa-image"></i> Icon (jpg, png, max 2MB)
                </label>
                <input 
                    type="file" 
                    name="icon" 
                    id="icon" 
                    accept="image/jpeg,image/png,image/jpg"
                    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-600 @error('icon') border-red-500 @enderror"
                    onchange="previewImage(event)"
                >
                @error('icon')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
                
                <!-- Image Preview -->
                <div id="preview" class="mt-4 hidden">
                    <img id="preview-image" src="" alt="Preview" class="w-32 h-32 object-cover rounded">
                </div>
            </div>

            <!-- Active Status -->
            <div class="mb-6">
                <label class="flex items-center">
                    <input 
                        type="checkbox" 
                        name="is_active" 
                        value="1"
                        {{ old('is_active', true) ? 'checked' : '' }}
                        class="mr-2"
                    >
                    <span class="text-gray-700 font-semibold">Active</span>
                </label>
            </div>

            <!-- Buttons -->
            <div class="flex gap-4">
                <button 
                    type="submit" 
                    class="bg-gradient-to-r from-purple-600 to-indigo-600 text-white px-6 py-3 rounded-lg hover:from-purple-700 hover:to-indigo-700 transition"
                >
                    <i class="fas fa-save"></i> Create Category
                </button>
                <a 
                    href="{{ route('admin.categories.index') }}" 
                    class="bg-gray-300 text-gray-700 px-6 py-3 rounded-lg hover:bg-gray-400 transition"
                >
                    <i class="fas fa-times"></i> Cancel
                </a>
            </div>
        </form>
    </div>
</div>

<script>
function previewImage(event) {
    const preview = document.getElementById('preview');
    const previewImage = document.getElementById('preview-image');
    const file = event.target.files[0];
    
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            previewImage.src = e.target.result;
            preview.classList.remove('hidden');
        }
        reader.readAsDataURL(file);
    }
}
</script>
@endsection