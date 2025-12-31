<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Services\MidtransService;
use App\Services\VoucherCodeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MidtransController extends Controller
{
    protected $midtransService;
    protected $voucherService;

    public function __construct(MidtransService $midtransService, VoucherCodeService $voucherService)
    {
        $this->midtransService = $midtransService;
        $this->voucherService = $voucherService;
    }

    public function callback(Request $request)
    {
        try {
            $notification = $this->midtransService->handleNotification($request->all());
            
            $orderId = $notification->order_id;
            $transactionStatus = $notification->transaction_status;
            $fraudStatus = $notification->fraud_status ?? 'accept';
            
            Log::info('Midtrans callback received', [
                'order_id' => $orderId,
                'status' => $transactionStatus,
                'fraud' => $fraudStatus
            ]);

            $transaction = Transaction::where('midtrans_order_id', $orderId)->first();

            if (!$transaction) {
                Log::error('Transaction not found', ['order_id' => $orderId]);
                return response()->json(['message' => 'Transaction not found'], 404);
            }

            // Handle transaction status
            if ($transactionStatus == 'capture') {
                if ($fraudStatus == 'accept') {
                    $this->processSuccessfulPayment($transaction, $notification);
                }
            } elseif ($transactionStatus == 'settlement') {
                $this->processSuccessfulPayment($transaction, $notification);
            } elseif ($transactionStatus == 'pending') {
                $transaction->update([
                    'payment_status' => 'pending',
                    'midtrans_transaction_id' => $notification->transaction_id,
                ]);
            } elseif (in_array($transactionStatus, ['deny', 'expire', 'cancel'])) {
                $transaction->markAsFailed();
            }

            return response()->json(['message' => 'Callback processed successfully']);

        } catch (\Exception $e) {
            Log::error('Midtrans callback error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json(['message' => 'Callback processing failed'], 500);
        }
    }

    protected function processSuccessfulPayment($transaction, $notification)
    {
        $transaction->update([
            'payment_status' => 'paid',
            'paid_at' => now(),
            'midtrans_transaction_id' => $notification->transaction_id,
            'payment_method' => $notification->payment_type ?? 'unknown',
        ]);

        // Generate voucher codes
        $this->voucherService->generateForTransaction($transaction);

        Log::info('Payment processed successfully', [
            'transaction_id' => $transaction->id,
            'order_id' => $transaction->midtrans_order_id
        ]);
    }

    public function finish(Request $request)
    {
        $orderId = $request->get('order_id');
        $transaction = Transaction::where('midtrans_order_id', $orderId)->first();

        if (!$transaction) {
            return redirect()->route('home')->with('error', 'Transaction not found');
        }

        return redirect()
            ->route('user.transactions.show', $transaction)
            ->with('success', 'Payment completed! Check your email for voucher codes.');
    }

    public function unfinish(Request $request)
    {
        return redirect()->route('home')->with('warning', 'Payment not completed yet.');
    }

    public function error(Request $request)
    {
        return redirect()->route('home')->with('error', 'Payment failed. Please try again.');
    }
}