@extends('layouts.app')

@section('title', 'Generate Voucher Codes')

@section('content')
<div class="max-w-4xl mx-auto px-4">
    <div class="mb-6">
        <a href="{{ route('admin.voucher-codes.index') }}" class="text-blue-600 hover:text-blue-800">
            <i class="fas fa-arrow-left"></i> Back to Voucher Codes
        </a>
    </div>

    <div class="bg-white rounded-lg shadow p-8">
        <h1 class="text-2xl font-bold mb-6 text-gray-800">
            <i class="fas fa-magic"></i> Generate Voucher Codes
        </h1>

        <div class="bg-blue-50 border-l-4 border-blue-600 p-4 mb-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-info-circle text-blue-600 text-xl"></i>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-semibold text-blue-800">Manual Code Generation</h3>
                    <p class="text-sm text-blue-700 mt-1">
                        Generate voucher codes manually without transaction. These codes can be used for promotions, giveaways, or manual sales.
                    </p>
                </div>
            </div>
        </div>

        <form action="{{ route('admin.voucher-codes.store') }}" method="POST">
            @csrf

            <!-- Product Selection -->
            <div class="mb-6">
                <label class="block text-gray-700 font-semibold mb-2">Select Product *</label>
                <select name="product_id" id="product_id" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-green-600 @error('product_id') border-red-500 @enderror" required>
                    <option value="">Choose a product...</option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}" 
                                data-format="{{ $product->code_format }}"
                                {{ old('product_id') == $product->id ? 'selected' : '' }}>
                            {{ $product->name }} - {{ $product->price_formatted }}
                        </option>
                    @endforeach
                </select>
                @error('product_id')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Code Format Preview -->
            <div class="mb-6" id="format-preview" style="display: none;">
                <label class="block text-gray-700 font-semibold mb-2">Code Format</label>
                <div class="bg-gray-50 p-4 rounded-lg border">
                    <p class="text-sm text-gray-600 mb-2">Codes will be generated in this format:</p>
                    <code class="text-lg font-mono font-bold text-green-600" id="format-text">XXXX-XXXX-XXXX</code>
                    <p class="text-xs text-gray-500 mt-2">Example: <span id="format-example" class="font-mono">ABCD-EFGH-IJKL</span></p>
                </div>
            </div>

            <!-- Quantity -->
            <div class="mb-6">
                <label class="block text-gray-700 font-semibold mb-2">Quantity *</label>
                <input 
                    type="number" 
                    name="quantity" 
                    value="{{ old('quantity', 10) }}"
                    min="1"
                    max="100"
                    placeholder="10"
                    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-green-600 @error('quantity') border-red-500 @enderror" 
                    required
                >
                <p class="text-sm text-gray-500 mt-1">How many codes to generate? (Max: 100)</p>
                @error('quantity')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Warning -->
            <div class="bg-yellow-50 border-l-4 border-yellow-600 p-4 mb-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-triangle text-yellow-600 text-xl"></i>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-semibold text-yellow-800">Important Notes:</h3>
                        <ul class="text-sm text-yellow-700 mt-1 list-disc list-inside">
                            <li>Generated codes are permanent and cannot be modified</li>
                            <li>Codes will be available immediately after generation</li>
                            <li>These codes are NOT automatically sent via email</li>
                            <li>You can export or copy codes from the list page</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Submit Buttons -->
            <div class="flex gap-4">
                <button type="submit" class="bg-green-600 text-white px-8 py-3 rounded-lg hover:bg-green-700 transition">
                    <i class="fas fa-magic"></i> Generate Codes
                </button>
                <a href="{{ route('admin.voucher-codes.index') }}" class="bg-gray-300 text-gray-700 px-8 py-3 rounded-lg hover:bg-gray-400 transition">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>

<script>
    // Show code format preview when product is selected
    document.getElementById('product_id').addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const format = selectedOption.getAttribute('data-format');
        
        if (format) {
            document.getElementById('format-preview').style.display = 'block';
            document.getElementById('format-text').textContent = format;
            
            // Generate example code
            const example = format.replace(/X/g, () => {
                const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
                return chars.charAt(Math.floor(Math.random() * chars.length));
            });
            document.getElementById('format-example').textContent = example;
        } else {
            document.getElementById('format-preview').style.display = 'none';
        }
    });
</script>
@endsection