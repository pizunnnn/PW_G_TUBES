<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\Product;
use App\Services\MidtransService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserTransactionController extends Controller
{
    protected $midtransService;

    public function __construct(MidtransService $midtransService)
    {
        $this->midtransService = $midtransService;
    }

    public function index()
    {
        $transactions = Auth::user()
            ->transactions()
            ->with('product')
            ->latest()
            ->paginate(10);

        return view('user.transactions.index', compact('transactions'));
    }

    public function show(Transaction $transaction)
    {
        // Ensure user can only see their own transactions
        if ($transaction->user_id !== Auth::id()) {
            abort(403);
        }

        $transaction->load(['product', 'voucherCodes']);
        return view('user.transactions.show', compact('transaction'));
    }

    public function checkout(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1|max:10',
        ]);

        $product = Product::findOrFail($validated['product_id']);
        $quantity = (int) $validated['quantity'];

        if (!$product->is_active) {
            return back()->with('error', 'Produk tidak tersedia');
        }

        if ($product->stock < $quantity) {
            return back()->with('error', 'Stok produk tidak mencukupi');
        }

        // 🔒 harga resmi dari server
        $totalPrice = $product->getPriceForPayment() * $quantity;

        $transaction = Transaction::create([
            'user_id' => Auth::id(),
            'product_id' => $product->id,
            'transaction_code' => Transaction::generateTransactionCode(),
            'quantity' => $quantity,
            'total_price' => $totalPrice,
            'payment_status' => 'pending',
        ]);

        try {
            $snapToken = $this->midtransService->createTransaction($transaction);

            $transaction->update([
                'midtrans_order_id' => $transaction->transaction_code,
            ]);

            return view('user.transactions.payment', compact('transaction', 'snapToken'));

        } catch (\Exception $e) {
            \Log::error('Midtrans payment creation failed', [
                'message' => $e->getMessage(),
                'transaction_id' => $transaction->id
            ]);

            return back()->with('error', 'Payment creation failed.');
        }
    }
}