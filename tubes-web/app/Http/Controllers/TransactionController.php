<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Transaction;
use App\Models\VoucherCode;
use App\Services\MidtransService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class TransactionController extends Controller
{
    public function index()
    {
        $transactions = Transaction::where('user_id', auth()->id())
            ->with('product')
            ->latest()
            ->paginate(10);
        
        return view('transactions.index', compact('transactions'));
    }
    
    public function create($productId)
    {
        $product = Product::with('category')->active()->inStock()->findOrFail($productId);

        // Use product's account_fields, or fallback to category's account_fields
        if (!$product->account_fields && $product->category->account_fields) {
            $product->account_fields = $product->category->account_fields;
        }

        return view('transactions.create', compact('product'));
    }
    
    public function store(Request $request)
    {
        // Basic validation
        $rules = [
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'payment_method' => 'required|in:midtrans,bank_transfer,qris',
            'bank' => 'required_if:payment_method,midtrans|in:bca,bni,bri,mandiri,permata',
            'voucher_code' => 'nullable|string',
        ];

        // Get product to check account fields
        $product = Product::with('category')->findOrFail($request->product_id);

        // Add dynamic validation for account fields if they exist
        $accountFields = $product->account_fields ?? $product->category->account_fields ?? null;
        if ($accountFields && isset($accountFields['fields'])) {
            foreach ($accountFields['fields'] as $field) {
                $fieldName = $field['name'];
                $isRequired = $field['required'] ?? true;

                if ($isRequired) {
                    $rules[$fieldName] = 'required';
                    if ($field['type'] === 'number') {
                        $rules[$fieldName] .= '|numeric';
                    }
                }
            }
        }

        $request->validate($rules);

        // Check stock
        if ($product->stock < $request->quantity) {
            return back()->with('error', 'Stok tidak mencukupi!');
        }

        // Calculate subtotal
        $subtotal = $product->price * $request->quantity;

        // Calculate product discount
        $productDiscount = $product->hasDiscount() ? ($product->getDiscountAmount() * $request->quantity) : 0;

        // Calculate price after product discount
        $priceAfterProductDiscount = $subtotal - $productDiscount;

        // Apply voucher if provided
        $voucherDiscount = 0;
        $voucherCodeId = null;

        if ($request->voucher_code) {
            $voucher = VoucherCode::where('code', strtoupper($request->voucher_code))->first();

            if ($voucher && $voucher->canBeUsedFor($product, $priceAfterProductDiscount)) {
                $voucherDiscount = $voucher->calculateDiscount($priceAfterProductDiscount);
                $voucherCodeId = $voucher->id;
            }
        }

        // Calculate total discount and final price
        $totalDiscount = $productDiscount + $voucherDiscount;
        $finalPrice = $subtotal - $totalDiscount;

        DB::beginTransaction();
        try {
            // Create transaction
            $transaction = Transaction::create([
                'user_id' => auth()->id(),
                'product_id' => $product->id,
                'transaction_code' => Transaction::generateTransactionCode(),
                'quantity' => $request->quantity,
                'total_price' => $finalPrice,
                'voucher_code_id' => $voucherCodeId,
                'discount_amount' => $totalDiscount,
                'payment_status' => 'pending',
                'payment_method' => $request->payment_method,
                'bank' => $request->bank ?? null,
                'game_user_id' => $request->game_user_id ?? null,
                'game_server' => $request->game_server ?? null,
            ]);

            // Increment voucher usage if applied
            if ($voucherCodeId) {
                $voucher->incrementUsage();
            }

            // Decrease stock
            $product->decreaseStock($request->quantity);

            DB::commit();

            return redirect()->route('transactions.detail', $transaction->transaction_code)
                ->with('success', 'Transaksi berhasil dibuat! Silakan lakukan pembayaran.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function applyVoucher(Request $request)
    {
        $request->validate([
            'voucher_code' => 'required|string',
            'product_id' => 'required|exists:products,id',
            'amount' => 'required|numeric|min:0',
        ]);

        $voucher = VoucherCode::where('code', strtoupper($request->voucher_code))->first();

        if (!$voucher) {
            return response()->json([
                'success' => false,
                'message' => 'Kode voucher tidak valid'
            ]);
        }

        $product = Product::findOrFail($request->product_id);

        if (!$voucher->canBeUsedFor($product, $request->amount)) {
            $message = 'Voucher tidak bisa digunakan';

            if (!$voucher->isAvailable()) {
                $message = 'Voucher sudah tidak berlaku atau habis digunakan';
            } elseif ($voucher->category_id && $voucher->category_id != $product->category_id) {
                $message = 'Voucher hanya berlaku untuk game ' . $voucher->category->name;
            } elseif ($voucher->min_purchase && $request->amount < $voucher->min_purchase) {
                $message = 'Minimal pembelian Rp ' . number_format($voucher->min_purchase, 0, ',', '.') . ' untuk menggunakan voucher ini';
            }

            return response()->json([
                'success' => false,
                'message' => $message
            ]);
        }

        $discount = $voucher->calculateDiscount($request->amount);

        return response()->json([
            'success' => true,
            'discount' => $discount,
            'message' => 'Voucher berhasil digunakan! Hemat Rp ' . number_format($discount, 0, ',', '.')
        ]);
    }
    
    public function detail($transactionCode)
    {
        $transaction = Transaction::where('transaction_code', $transactionCode)
            ->where('user_id', auth()->id())
            ->with(['product', 'voucherCode'])
            ->firstOrFail();

        $paymentData = null;

        // Create Midtrans charge for pending payments
        if ($transaction->payment_status === 'pending' && in_array($transaction->payment_method, ['midtrans', 'qris'])) {
            try {
                $midtransService = new MidtransService();

                // Calculate effective price per item (after discount)
                $effectivePricePerItem = (int) ($transaction->total_price / $transaction->quantity);

                $result = $midtransService->createTransaction([
                    'transaction_id' => $transaction->transaction_code,
                    'gross_amount' => $transaction->total_price,
                    'customer_details' => [
                        'name' => auth()->user()->name,
                        'email' => auth()->user()->email,
                        'phone' => auth()->user()->phone ?? '',
                    ],
                    'item_details' => [[
                        'id' => $transaction->product->id,
                        'price' => $effectivePricePerItem,
                        'quantity' => $transaction->quantity,
                        'name' => $transaction->product->name . ($transaction->discount_amount > 0 ? ' (Discounted)' : ''),
                    ]],
                    'payment_method' => $transaction->payment_method,
                    'channel' => $transaction->bank ?? 'bca',
                ]);

                $paymentData = $result['payment_instructions'];
            } catch (\Exception $e) {
                \Log::error('Midtrans Charge Error: ' . $e->getMessage());
            }
        }

        return view('transactions.show', compact('transaction', 'paymentData'));
    }

    public function cancel($transactionCode)
    {
        $transaction = Transaction::where('transaction_code', $transactionCode)
            ->where('user_id', auth()->id())
            ->with(['product', 'voucherCode'])
            ->firstOrFail();

        // Only allow canceling pending transactions
        if ($transaction->payment_status !== 'pending') {
            return back()->with('error', 'Hanya transaksi pending yang bisa dicancel!');
        }

        DB::beginTransaction();
        try {
            // Restore product stock
            $transaction->product->increment('stock', $transaction->quantity);

            // Restore voucher usage if voucher was used
            if ($transaction->voucher_code_id && $transaction->voucherCode) {
                $transaction->voucherCode->decrement('used_count');
            }

            // Update transaction status
            $transaction->update([
                'payment_status' => 'cancelled',
            ]);

            DB::commit();

            return redirect()->route('transactions.list')
                ->with('success', 'Transaksi berhasil dicancel. Stock dan voucher sudah dikembalikan.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal cancel transaksi: ' . $e->getMessage());
        }
    }

    public function midtransCallback(Request $request)
    {
        // Set Midtrans configuration
        \Midtrans\Config::$serverKey = config('midtrans.server_key');
        \Midtrans\Config::$isProduction = config('midtrans.is_production');

        try {
            $notification = new \Midtrans\Notification();

            $transactionStatus = $notification->transaction_status;
            $orderID = $notification->order_id;
            $fraudStatus = $notification->fraud_status;

            // Find transaction
            $transaction = Transaction::where('transaction_code', $orderID)->first();

            if (!$transaction) {
                return response()->json(['message' => 'Transaction not found'], 404);
            }

            // Update transaction status based on Midtrans notification
            if ($transactionStatus == 'capture') {
                if ($fraudStatus == 'accept') {
                    $transaction->payment_status = 'paid';
                    $transaction->paid_at = now();
                }
            } else if ($transactionStatus == 'settlement') {
                $transaction->payment_status = 'paid';
                $transaction->paid_at = now();
            } else if ($transactionStatus == 'pending') {
                $transaction->payment_status = 'pending';
            } else if ($transactionStatus == 'deny' || $transactionStatus == 'expire' || $transactionStatus == 'cancel') {
                $transaction->payment_status = 'failed';
            }

            $transaction->save();

            return response()->json(['message' => 'Callback processed successfully']);

        } catch (\Exception $e) {
            \Log::error('Midtrans Callback Error: ' . $e->getMessage());
            return response()->json(['message' => 'Internal server error'], 500);
        }
    }

    public function downloadInvoice($transactionCode)
    {
        $transaction = Transaction::where('transaction_code', $transactionCode)
            ->where('user_id', auth()->id())
            ->with(['user', 'product.category', 'voucherCode'])
            ->firstOrFail();

        // Only allow downloading invoice for paid transactions
        if ($transaction->payment_status !== 'paid') {
            return back()->with('error', 'Invoice hanya tersedia untuk transaksi yang sudah dibayar!');
        }

        $pdf = Pdf::loadView('invoices.transaction', ['transaction' => $transaction]);

        return $pdf->download('invoice-' . $transaction->transaction_code . '.pdf');
    }
}
