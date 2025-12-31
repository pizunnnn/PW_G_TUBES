<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'product_id',
        'transaction_code',
        'quantity',
        'total_price',
        'voucher_code_id',
        'discount_amount',
        'payment_status',
        'topup_status',
        'payment_method',
        'bank',
        'game_user_id',
        'game_server',
        'midtrans_order_id',
        'midtrans_transaction_id',
        'paid_at',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'total_price' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function voucherCode()
    {
        return $this->belongsTo(VoucherCode::class);
    }

    // Accessors
    public function getTotalPriceFormattedAttribute()
    {
        return 'Rp ' . number_format($this->total_price, 0, ',', '.');
    }

    public function getStatusBadgeAttribute()
    {
        return match($this->payment_status) {
            'pending' => '<span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Pending</span>',
            'paid' => '<span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Dibayar</span>',
            'failed' => '<span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Gagal</span>',
            'cancelled' => '<span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">Dibatalkan</span>',
            'expired' => '<span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-orange-100 text-orange-800">Kedaluwarsa</span>',
            default => '<span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-600">Tidak Diketahui</span>',
        };
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('payment_status', 'pending');
    }

    public function scopePaid($query)
    {
        return $query->where('payment_status', 'paid');
    }

    public function scopeFailed($query)
    {
        return $query->where('payment_status', 'failed');
    }

    public function scopeTopupPending($query)
    {
        return $query->where('topup_status', 'pending');
    }

    public function scopeTopupCompleted($query)
    {
        return $query->where('topup_status', 'completed');
    }

    public function scopeTopupFailed($query)
    {
        return $query->where('topup_status', 'failed');
    }

    // Helper methods
    public function isPaid()
    {
        return $this->payment_status === 'paid';
    }

    public function isPending()
    {
        return $this->payment_status === 'pending';
    }

    public function markAsPaid()
    {
        $this->update([
            'payment_status' => 'paid',
            'paid_at' => now(),
        ]);
    }

    public function markAsFailed()
    {
        $this->update([
            'payment_status' => 'failed',
        ]);
    }

    public function markAsExpired()
    {
        $this->update([
            'payment_status' => 'expired',
        ]);
    }

    // Topup status helpers
    public function isTopupPending()
    {
        return $this->topup_status === 'pending';
    }

    public function isTopupCompleted()
    {
        return $this->topup_status === 'completed';
    }

    public function markTopupAsCompleted()
    {
        $this->update([
            'topup_status' => 'completed',
        ]);
    }

    public function markTopupAsFailed()
    {
        $this->update([
            'topup_status' => 'failed',
        ]);
    }

    // Generate transaction code
    public static function generateTransactionCode()
    {
        do {
            $code = 'TRX-' . strtoupper(Str::random(10));
        } while (self::where('transaction_code', $code)->exists());
        
        return $code;
    }
}