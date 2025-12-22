<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
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
        $product = Product::active()->inStock()->findOrFail($productId);
        
        return view('transactions.create', compact('product'));
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'payment_method' => 'required|in:midtrans,bank_transfer,qris',
        ]);
        
        $product = Product::findOrFail($request->product_id);
        
        // Check stock
        if ($product->stock < $request->quantity) {
            return back()->with('error', 'Stok tidak mencukupi!');
        }
        
        $totalPrice = $product->price * $request->quantity;
        
        DB::beginTransaction();
        try {
            // Create transaction
            $transaction = Transaction::create([
                'user_id' => auth()->id(),
                'product_id' => $product->id,
                'transaction_code' => Transaction::generateTransactionCode(),
                'quantity' => $request->quantity,
                'total_price' => $totalPrice,
                'payment_status' => 'pending',
                'payment_method' => $request->payment_method,
            ]);
            
            // Decrease stock
            $product->decreaseStock($request->quantity);
            
            DB::commit();
            
            // TODO: Integrate with Midtrans payment gateway
            
            return redirect()->route('transactions.show', $transaction->transaction_code)
                ->with('success', 'Transaksi berhasil dibuat! Silakan lakukan pembayaran.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
    
    public function show($transactionCode)
    {
        $transaction = Transaction::where('transaction_code', $transactionCode)
            ->where('user_id', auth()->id())
            ->with(['product', 'voucherCodes'])
            ->firstOrFail();
        
        return view('transactions.show', compact('transaction'));
    }
}
