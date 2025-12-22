<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\VoucherCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function index()
    {
        $user = auth()->user();
        $purchaseHistory = Transaction::where('user_id', $user->id)
            ->with('product')
            ->latest()
            ->limit(5)
            ->get();
        
        return view('profile.index', compact('user', 'purchaseHistory'));
    }
    
    public function update(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . auth()->id(),
        ]);
        
        auth()->user()->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);
        
        return back()->with('success', 'Profil berhasil diperbarui!');
    }
    
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|min:8|confirmed',
        ]);
        
        if (!Hash::check($request->current_password, auth()->user()->password)) {
            return back()->with('error', 'Password lama tidak sesuai!');
        }
        
        auth()->user()->update([
            'password' => Hash::make($request->password),
        ]);
        
        return back()->with('success', 'Password berhasil diubah!');
    }
    
    public function vouchers()
    {
        $vouchers = VoucherCode::whereHas('transaction', function($query) {
                $query->where('user_id', auth()->id())
                      ->where('payment_status', 'paid');
            })
            ->with(['product', 'transaction'])
            ->latest()
            ->paginate(20);
        
        return view('profile.vouchers', compact('vouchers'));
    }
}
