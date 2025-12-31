@extends('layouts.app')

@section('title', 'Tambah Game Baru')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">
                <i class="fas fa-plus-circle text-purple-600"></i> Add New Game
            </h1>
            <p class="text-gray-600 mt-2">Create a new game category</p>
        </div>

        <!-- Form -->
        <div class="bg-white rounded-xl shadow-lg p-8">
            <form action="{{ route('admin.categories.store') }}" method="POST">
                @csrf

                <!-- Nama Game -->
                <div class="mb-6">
                    <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">
                        Nama Game <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="text"
                        name="name"
                        id="name"
                        value="{{ old('name') }}"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-600 focus:border-transparent @error('name') border-red-500 @enderror"
                        placeholder="e.g., Mobile Legends"
                        required
                    >
                    @error('name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Slug (Optional - Auto-generated) -->
                <div class="mb-6">
                    <label for="slug" class="block text-sm font-semibold text-gray-700 mb-2">
                        Slug <span class="text-gray-400 text-xs">(optional - auto-generated from name)</span>
                    </label>
                    <input
                        type="text"
                        name="slug"
                        id="slug"
                        value="{{ old('slug') }}"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-600 focus:border-transparent @error('slug') border-red-500 @enderror"
                        placeholder="mobile-legends"
                    >
                    <p class="text-sm text-gray-500 mt-1">URL-friendly version of the name (lowercase, no spaces)</p>
                    @error('slug')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Account Fields Configuration -->
                <div class="mb-6" x-data="accountFieldsBuilder()">
                    <label class="block text-sm font-semibold text-gray-900 mb-3">
                        <i class="fas fa-gamepad"></i> Account Fields Configuration
                        <span class="text-gray-400 text-xs font-normal">(optional - untuk game yang butuh User ID/Server)</span>
                    </label>

                    <div class="bg-gray-50 rounded-lg p-4 border-2 border-gray-200 mb-4">
                        <!-- Title -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Form Title</label>
                            <input type="text" x-model="config.title"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-purple-500 focus:border-purple-500"
                                   placeholder="MASUKKAN ID DAN SERVER ANDA">
                        </div>

                        <!-- Fields -->
                        <div class="space-y-3 mb-4">
                            <template x-for="(field, index) in config.fields" :key="index">
                                <div class="bg-white p-4 rounded-md border border-gray-300">
                                    <div class="flex justify-between items-start mb-3">
                                        <h4 class="font-semibold text-sm">Field #<span x-text="index + 1"></span></h4>
                                        <button type="button" @click="removeField(index)"
                                                class="text-red-600 hover:text-red-800 text-sm">
                                            <i class="fas fa-trash"></i> Hapus
                                        </button>
                                    </div>

                                    <div class="grid grid-cols-2 gap-3 mb-3">
                                        <div>
                                            <label class="block text-xs font-medium text-gray-700 mb-1">Field Name</label>
                                            <input type="text" x-model="field.name"
                                                   class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm"
                                                   placeholder="game_user_id">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-700 mb-1">Field Type</label>
                                            <select x-model="field.type"
                                                    class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm">
                                                <option value="text">Text</option>
                                                <option value="select">Select (Dropdown)</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-2 gap-3 mb-3">
                                        <div>
                                            <label class="block text-xs font-medium text-gray-700 mb-1">Label</label>
                                            <input type="text" x-model="field.label"
                                                   class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm"
                                                   placeholder="User ID">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-700 mb-1">Placeholder</label>
                                            <input type="text" x-model="field.placeholder"
                                                   class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm"
                                                   placeholder="Contoh: 123456789">
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="block text-xs font-medium text-gray-700 mb-1">Hint Text</label>
                                        <input type="text" x-model="field.hint"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm"
                                               placeholder="Untuk menemukan User ID...">
                                    </div>

                                    <div class="mb-3" x-show="field.type === 'select'">
                                        <label class="block text-xs font-medium text-gray-700 mb-2">Options (satu per baris)</label>
                                        <textarea x-model="field.optionsText" @input="updateOptions(index)"
                                                  rows="3"
                                                  class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm font-mono"
                                                  placeholder="America&#10;Asia&#10;Europe"></textarea>
                                        <p class="text-xs text-gray-500 mt-1">Format: value|label (atau cukup value)</p>
                                    </div>

                                    <label class="flex items-center text-sm">
                                        <input type="checkbox" x-model="field.required"
                                               class="rounded border-gray-300 text-purple-600 focus:ring-purple-500">
                                        <span class="ml-2">Required field</span>
                                    </label>
                                </div>
                            </template>
                        </div>

                        <!-- Add Field Button -->
                        <button type="button" @click="addField()"
                                class="w-full px-4 py-2 bg-purple-100 text-purple-700 rounded-md hover:bg-purple-200 transition text-sm font-semibold">
                            <i class="fas fa-plus"></i> Tambah Field
                        </button>
                    </div>

                    <!-- Hidden input to store JSON -->
                    <input type="hidden" name="account_fields" :value="JSON.stringify(config.fields.length > 0 ? config : null)">

                    <!-- Preview -->
                    <div x-show="config.fields.length > 0" class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <p class="text-sm font-semibold text-blue-900 mb-2">Preview:</p>
                        <pre class="text-xs bg-white p-2 rounded border overflow-auto" x-text="JSON.stringify(config, null, 2)"></pre>
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
                            class="w-4 h-4 text-purple-600 border-gray-300 rounded focus:ring-purple-500"
                        >
                        <span class="ml-2 text-sm font-semibold text-gray-700">Active (visible to customers)</span>
                    </label>
                </div>

                <!-- Buttons -->
                <div class="flex space-x-4">
                    <button
                        type="submit"
                        class="flex-1 bg-gradient-to-r from-purple-600 to-indigo-600 text-white font-semibold py-3 px-6 rounded-lg hover:from-purple-700 hover:to-indigo-700 transition"
                    >
                        <i class="fas fa-save"></i> Create Game
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

    // Account Fields Builder Component
    function accountFieldsBuilder() {
        return {
            config: {
                title: '',
                fields: []
            },

            addField() {
                this.config.fields.push({
                    name: '',
                    label: '',
                    type: 'text',
                    placeholder: '',
                    hint: '',
                    required: true,
                    options: [],
                    optionsText: ''
                });
            },

            removeField(index) {
                this.config.fields.splice(index, 1);
            },

            updateOptions(index) {
                const field = this.config.fields[index];
                if (field.type === 'select' && field.optionsText) {
                    field.options = field.optionsText.split('\n')
                        .map(line => line.trim())
                        .filter(line => line)
                        .map(line => {
                            const parts = line.split('|');
                            return {
                                value: parts[0].trim(),
                                label: parts[1] ? parts[1].trim() : parts[0].trim()
                            };
                        });
                }
            }
        }
    }
</script>
@endsection
