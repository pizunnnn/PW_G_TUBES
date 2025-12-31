<?php

namespace App\Http\Controllers;

use Midtrans\Snap;
use Midtrans\Config;

class PaymentController extends Controller
{
    public function pay()
    {
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = false;
        Config::$isSanitized = true;
        Config::$is3ds = true;

        $params = [
            'transaction_details' => [
                'order_id' => 'TEST-' . time(),
                'gross_amount' => 10000,
            ],
        ];

        $snapToken = Snap::getSnapToken($params);

        return view('payment', compact('snapToken'));
    }
}
