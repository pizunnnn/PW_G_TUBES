<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VoucherCode;
use App\Models\Category;
use Illuminate\Http\Request;

class VoucherCodeController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $category = $request->get('category');
        $status = $request->get('status');

        $voucherCodes = VoucherCode::query()
            ->with('category')
            ->when($search, function($query, $search) {
                return $query->where('code', 'like', "%{$search}%");
            })
            ->when($category, function($query, $category) {
                return $query->where('category_id', $category);
            })
            ->when($status === 'active', function($query) {
                return $query->active();
            })
            ->when($status === 'inactive', function($query) {
                return $query->where('is_active', false);
            })
            ->when($status === 'expired', function($query) {
                return $query->where('valid_until', '<', now());
            })
            ->latest()
            ->paginate(20);

        $categories = Category::all();

        return view('admin.voucher-codes.index', compact('voucherCodes', 'search', 'category', 'status', 'categories'));
    }

    public function create()
    {
        $categories = Category::active()->get();
        return view('admin.voucher-codes.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'nullable|string|max:255|unique:voucher_codes,code',
            'discount_type' => 'required|in:percentage,fixed',
            'discount_value' => 'required|numeric|min:0',
            'category_id' => 'nullable|exists:categories,id',
            'min_purchase' => 'nullable|numeric|min:0',
            'max_discount' => 'nullable|numeric|min:0',
            'valid_from' => 'nullable|date',
            'valid_until' => 'nullable|date|after:valid_from',
            'usage_limit' => 'required|integer|min:1',
            'is_active' => 'boolean',
        ]);

        // Generate code if not provided
        if (empty($validated['code'])) {
            $validated['code'] = VoucherCode::generateCode();
        } else {
            $validated['code'] = strtoupper($validated['code']);
        }

        // Validate percentage discount
        if ($validated['discount_type'] === 'percentage' && $validated['discount_value'] > 100) {
            return back()->withInput()->with('error', 'Percentage discount cannot exceed 100%');
        }

        // Set default values
        $validated['is_active'] = $request->has('is_active') ? 1 : 0;
        $validated['used_count'] = 0;

        VoucherCode::create($validated);

        return redirect()
            ->route('admin.voucher-codes.index')
            ->with('success', 'Discount voucher created successfully!');
    }

    public function destroy(VoucherCode $voucherCode)
    {
        // Prevent deletion if voucher has been used
        if ($voucherCode->used_count > 0) {
            return redirect()
                ->route('admin.voucher-codes.index')
                ->with('error', 'Cannot delete voucher that has been used!');
        }

        $voucherCode->delete();

        return redirect()
            ->route('admin.voucher-codes.index')
            ->with('success', 'Voucher code deleted successfully!');
    }
}