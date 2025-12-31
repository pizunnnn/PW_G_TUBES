@extends('layouts.app')

@section('title', 'Edit Game')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">
                <i class="fas fa-edit text-purple-600"></i> Edit Game
            </h1>
            <p class="text-gray-600 mt-2">Update game information</p>
        </div>

        <!-- Form -->
        <div class="bg-white rounded-xl shadow-lg p-8">
            <form action="{{ route('admin.categories.update', $game) }}" method="POST">
                @csrf
                @method('PUT')

                <!-- Game Name -->
                <div class="mb-6">
                    <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">
                        Game Name <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="text"
                        name="name"
                        id="name"
                        value="{{ old('name', $game->name) }}"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-600 focus:border-transparent @error('name') border-red-500 @enderror"
                        required
                    >
                    @error('name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Slug -->
                <div class="mb-6">
                    <label for="slug" class="block text-sm font-semibold text-gray-700 mb-2">
                        Slug <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="text"
                        name="slug"
                        id="slug"
                        value="{{ old('slug', $game->slug) }}"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-600 focus:border-transparent @error('slug') border-red-500 @enderror"
                        required
                    >
                    <p class="text-sm text-gray-500 mt-1">URL-friendly version of the name (lowercase, no spaces)</p>
                    @error('slug')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Account Fields Configuration -->
                <div class="mb-6" x-data="accountFieldsManager()">
                    <div class="flex items-center justify-between mb-3">
                        <label class="block text-sm font-semibold text-gray-700">
                            <i class="fas fa-gamepad text-purple-600"></i> Account Fields Configuration
                            <span class="text-gray-500 font-normal text-xs">(Optional - untuk game yang butuh User ID/Server)</span>
                        </label>
                        <label class="flex items-center">
                            <input
                                type="checkbox"
                                x-model="enabled"
                                class="w-4 h-4 text-purple-600 border-gray-300 rounded focus:ring-purple-500"
                            >
                            <span class="ml-2 text-sm font-medium text-gray-700">Enable Account Fields</span>
                        </label>
                    </div>

                    <div x-show="enabled" x-transition class="space-y-4 p-4 bg-purple-50 rounded-lg border-2 border-purple-200">
                        <!-- Form Title -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Form Title</label>
                            <input
                                type="text"
                                x-model="config.title"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-600"
                                placeholder="e.g., MASUKKAN ID DAN SERVER ANDA"
                            >
                        </div>

                        <!-- Fields List -->
                        <div>
                            <div class="flex items-center justify-between mb-3">
                                <label class="block text-sm font-semibold text-gray-700">Fields</label>
                                <button
                                    type="button"
                                    @click="addField()"
                                    class="px-4 py-2 bg-green-600 text-white text-sm rounded-lg hover:bg-green-700 transition"
                                >
                                    <i class="fas fa-plus"></i> Tambah Field
                                </button>
                            </div>

                            <template x-for="(field, index) in config.fields" :key="index">
                                <div class="bg-white p-4 rounded-lg border-2 border-gray-200 mb-3">
                                    <div class="flex items-center justify-between mb-3">
                                        <span class="text-sm font-semibold text-gray-700">Field <span x-text="index + 1"></span></span>
                                        <button
                                            type="button"
                                            @click="removeField(index)"
                                            class="text-red-600 hover:text-red-800"
                                        >
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>

                                    <div class="grid grid-cols-2 gap-3">
                                        <div>
                                            <label class="block text-xs font-medium text-gray-700 mb-1">Field Name (internal)</label>
                                            <input
                                                type="text"
                                                x-model="field.name"
                                                class="w-full px-3 py-2 text-sm border border-gray-300 rounded focus:ring-2 focus:ring-purple-500"
                                                placeholder="e.g., game_user_id"
                                            >
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-700 mb-1">Label (ditampilkan ke user)</label>
                                            <input
                                                type="text"
                                                x-model="field.label"
                                                class="w-full px-3 py-2 text-sm border border-gray-300 rounded focus:ring-2 focus:ring-purple-500"
                                                placeholder="e.g., User ID"
                                            >
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-700 mb-1">Type</label>
                                            <select
                                                x-model="field.type"
                                                class="w-full px-3 py-2 text-sm border border-gray-300 rounded focus:ring-2 focus:ring-purple-500"
                                            >
                                                <option value="text">Text</option>
                                                <option value="number">Number</option>
                                                <option value="select">Select (Dropdown)</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-700 mb-1">Placeholder</label>
                                            <input
                                                type="text"
                                                x-model="field.placeholder"
                                                class="w-full px-3 py-2 text-sm border border-gray-300 rounded focus:ring-2 focus:ring-purple-500"
                                                placeholder="e.g., Contoh: 123456"
                                            >
                                        </div>
                                        <div class="col-span-2">
                                            <label class="block text-xs font-medium text-gray-700 mb-1">Hint (petunjuk)</label>
                                            <input
                                                type="text"
                                                x-model="field.hint"
                                                class="w-full px-3 py-2 text-sm border border-gray-300 rounded focus:ring-2 focus:ring-purple-500"
                                                placeholder="e.g., Masukkan User ID kamu"
                                            >
                                        </div>
                                        <div class="col-span-2">
                                            <label class="flex items-center">
                                                <input
                                                    type="checkbox"
                                                    x-model="field.required"
                                                    class="w-4 h-4 text-purple-600 border-gray-300 rounded focus:ring-purple-500"
                                                >
                                                <span class="ml-2 text-xs font-medium text-gray-700">Required (wajib diisi)</span>
                                            </label>
                                        </div>

                                        <!-- Options for Select Type -->
                                        <div x-show="field.type === 'select'" class="col-span-2 p-3 bg-gray-50 rounded">
                                            <div class="flex items-center justify-between mb-2">
                                                <label class="block text-xs font-semibold text-gray-700">Dropdown Options</label>
                                                <button
                                                    type="button"
                                                    @click="addOption(field, index)"
                                                    class="px-2 py-1 bg-blue-600 text-white text-xs rounded hover:bg-blue-700"
                                                >
                                                    + Add Option
                                                </button>
                                            </div>
                                            <template x-if="!field.options || field.options.length === 0">
                                                <p class="text-xs text-gray-500 italic">No options yet. Click "Add Option" to add.</p>
                                            </template>
                                            <template x-for="(option, optIndex) in field.options" :key="optIndex">
                                                <div class="flex gap-2 mb-2">
                                                    <input
                                                        type="text"
                                                        x-model="option.value"
                                                        class="flex-1 px-2 py-1 text-xs border border-gray-300 rounded"
                                                        placeholder="Value (e.g., asia)"
                                                    >
                                                    <input
                                                        type="text"
                                                        x-model="option.label"
                                                        class="flex-1 px-2 py-1 text-xs border border-gray-300 rounded"
                                                        placeholder="Label (e.g., Asia)"
                                                    >
                                                    <button
                                                        type="button"
                                                        @click="removeOption(field, optIndex)"
                                                        class="text-red-600 hover:text-red-800"
                                                    >
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                            </template>

                            <template x-if="config.fields.length === 0">
                                <p class="text-sm text-gray-500 italic text-center py-4">Belum ada field. Klik "Tambah Field" untuk menambahkan.</p>
                            </template>
                        </div>

                        <!-- Hidden input to submit the JSON -->
                        <input type="hidden" name="account_fields" :value="enabled ? JSON.stringify(config) : ''">
                    </div>
                </div>

                <!-- Active Status -->
                <div class="mb-6">
                    <label class="flex items-center">
                        <input
                            type="checkbox"
                            name="is_active"
                            value="1"
                            {{ old('is_active', $game->is_active) ? 'checked' : '' }}
                            class="w-4 h-4 text-purple-600 border-gray-300 rounded focus:ring-purple-500"
                        >
                        <span class="ml-2 text-sm font-semibold text-gray-700">Active (visible to customers)</span>
                    </label>
                </div>

                <!-- Product Count Info -->
                <div class="mb-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <p class="text-sm text-blue-800">
                        <i class="fas fa-info-circle"></i>
                        This game currently has <strong>{{ $game->products_count }} products</strong>.
                    </p>
                </div>

                <!-- Buttons -->
                <div class="flex space-x-4">
                    <button
                        type="submit"
                        class="flex-1 bg-gradient-to-r from-purple-600 to-indigo-600 text-white font-semibold py-3 px-6 rounded-lg hover:from-purple-700 hover:to-indigo-700 transition"
                    >
                        <i class="fas fa-save"></i> Perbarui Game
                    </button>
                    <a
                        href="{{ route('admin.categories.index') }}"
                        class="flex-1 bg-gray-600 text-white font-semibold py-3 px-6 rounded-lg hover:bg-gray-700 transition text-center"
                    >
                        <i class="fas fa-times"></i> Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Auto-generate slug from name
    document.getElementById('name').addEventListener('input', function(e) {
        const slug = e.target.value
            .toLowerCase()
            .replace(/[^a-z0-9]+/g, '-')
            .replace(/(^-|-$)/g, '');
        document.getElementById('slug').value = slug;
    });

    // Account Fields Manager
    function accountFieldsManager() {
        const existingData = @json($game->account_fields ?? null);

        return {
            enabled: !!existingData,
            config: existingData || {
                title: '',
                fields: []
            },

            addField() {
                this.config.fields.push({
                    name: '',
                    label: '',
                    type: 'text',
                    placeholder: '',
                    required: true,
                    hint: '',
                    options: []
                });
            },

            removeField(index) {
                this.config.fields.splice(index, 1);
            },

            addOption(field, fieldIndex) {
                if (!field.options) {
                    field.options = [];
                }
                field.options.push({
                    value: '',
                    label: ''
                });
            },

            removeOption(field, optionIndex) {
                field.options.splice(optionIndex, 1);
            }
        }
    }
</script>
@endsection
