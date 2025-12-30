<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VoucherCode;
use App\Models\Product;
use Illuminate\Http\Request;

class VoucherCodeController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $product = $request->get('product');
        $status = $request->get('status');
        
        $voucherCodes = VoucherCode::query()
            ->with(['product', 'transaction.user'])
            ->when($search, function($query, $search) {
                return $query->where('code', 'like', "%{$search}%");
            })
            ->when($product, function($query, $product) {
                return $query->where('product_id', $product);
            })
            ->when($status === 'used', function($query) {
                return $query->used();
            })
            ->when($status === 'unused', function($query) {
                return $query->unused();
            })
            ->latest()
            ->paginate(20);
        
        $products = Product::all();
        
        return view('admin.voucher-codes.index', compact('voucherCodes', 'search', 'product', 'status', 'products'));
    }

    public function create()
    {
        $products = Product::active()->get();
        return view('admin.voucher-codes.create', compact('products'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1|max:100',
        ]);

        $product = Product::findOrFail($validated['product_id']);
        
        // Generate voucher codes
        for ($i = 0; $i < $validated['quantity']; $i++) {
            VoucherCode::create([
                'product_id' => $product->id,
                'code' => VoucherCode::generateCode($product->code_format),
                'is_used' => false,
            ]);
        }

        return redirect()
            ->route('admin.voucher-codes.index')
            ->with('success', $validated['quantity'] . ' voucher codes generated successfully!');
    }

    public function destroy(VoucherCode $voucherCode)
    {
        // Prevent deletion of used voucher codes
        if ($voucherCode->is_used) {
            return redirect()
                ->route('admin.voucher-codes.index')
                ->with('error', 'Cannot delete used voucher code!');
        }

        // Prevent deletion if attached to transaction
        if ($voucherCode->transaction_id) {
            return redirect()
                ->route('admin.voucher-codes.index')
                ->with('error', 'Cannot delete voucher code attached to transaction!');
        }

        $voucherCode->delete();

        return redirect()
            ->route('admin.voucher-codes.index')
            ->with('success', 'Voucher code deleted successfully!');
    }
}