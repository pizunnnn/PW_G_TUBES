<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MidtransController extends Controller
{
    public function callback(Request $request)
    {
        // Handle Midtrans callback
        return response()->json(['status' => 'success']);
    }

    public function finish(Request $request)
    {
        return redirect()->route('user.transactions.index')
            ->with('success', 'Payment completed successfully!');
    }

    public function unfinish(Request $request)
    {
        return redirect()->route('user.transactions.index')
            ->with('warning', 'Payment not completed.');
    }

    public function error(Request $request)
    {
        return redirect()->route('user.transactions.index')
            ->with('error', 'Payment failed. Please try again.');
    }
}
