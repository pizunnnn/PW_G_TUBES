<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\Product;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $status = $request->get('status');
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');
        
        $transactions = Transaction::query()
            ->with(['user', 'product'])
            ->when($search, function($query, $search) {
                return $query->where('transaction_code', 'like', "%{$search}%")
                           ->orWhereHas('user', function($q) use ($search) {
                               $q->where('name', 'like', "%{$search}%")
                                 ->orWhere('email', 'like', "%{$search}%");
                           })
                           ->orWhereHas('product', function($q) use ($search) {
                               $q->where('name', 'like', "%{$search}%");
                           });
            })
            ->when($status, function($query, $status) {
                return $query->where('payment_status', $status);
            })
            ->when($dateFrom, function($query, $dateFrom) {
                return $query->whereDate('created_at', '>=', $dateFrom);
            })
            ->when($dateTo, function($query, $dateTo) {
                return $query->whereDate('created_at', '<=', $dateTo);
            })
            ->latest()
            ->paginate(15);
        
        return view('admin.transactions.index', compact('transactions', 'search', 'status', 'dateFrom', 'dateTo'));
    }

    public function show(Transaction $transaction)
    {
        $transaction->load(['user', 'product', 'voucherCodes']);
        return view('admin.transactions.show', compact('transaction'));
    }

    public function updateStatus(Request $request, Transaction $transaction)
    {
        $validated = $request->validate([
            'payment_status' => 'required|in:pending,paid,failed,expired',
        ]);

        $transaction->update($validated);

        // If marked as paid, generate voucher codes if not exists
        if ($validated['payment_status'] === 'paid' && $transaction->voucherCodes()->count() === 0) {
            app(\App\Services\VoucherCodeService::class)->generateForTransaction($transaction);
        }

        return redirect()
            ->route('admin.transactions.show', $transaction)
            ->with('success', 'Transaction status updated successfully!');
    }

    public function exportPDF(Request $request)
    {
        $status = $request->get('status');
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');
        
        $transactions = Transaction::query()
            ->with(['user', 'product'])
            ->when($status, function($query, $status) {
                return $query->where('payment_status', $status);
            })
            ->when($dateFrom, function($query, $dateFrom) {
                return $query->whereDate('created_at', '>=', $dateFrom);
            })
            ->when($dateTo, function($query, $dateTo) {
                return $query->whereDate('created_at', '<=', $dateTo);
            })
            ->latest()
            ->get();

        $totalRevenue = $transactions->where('payment_status', 'paid')->sum('total_price');
        
        $pdf = Pdf::loadView('admin.transactions.pdf', compact('transactions', 'totalRevenue', 'status', 'dateFrom', 'dateTo'));
        
        return $pdf->download('transactions-report-' . date('Y-m-d') . '.pdf');
    }
}