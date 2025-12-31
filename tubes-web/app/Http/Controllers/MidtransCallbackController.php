<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Midtrans\Notification;

class MidtransCallbackController extends Controller
{
    public function handle(Request $request)
    {
        $notification = new Notification();

        $orderId = $notification->order_id;
        $transactionStatus = $notification->transaction_status;
        $paymentType = $notification->payment_type;
        $fraudStatus = $notification->fraud_status;

        $transaction = Transaction::where('transaction_code', $orderId)->first();

        if (!$transaction) {
            return response()->json(['message' => 'Transaction not found'], 404);
        }

        // STATUS BERHASIL
        if (
            $transactionStatus === 'capture' ||
            $transactionStatus === 'settlement'
        ) {
            $transaction->update([
                'payment_status' => 'paid',
                'payment_type' => $paymentType,
            ]);
        }

        // STATUS GAGAL / DIBATALKAN
        elseif (
            in_array($transactionStatus, ['deny', 'cancel', 'expire'])
        ) {
            $transaction->update([
                'payment_status' => 'failed',
            ]);
        }

        return response()->json(['message' => 'Callback processed']);
    }
}
