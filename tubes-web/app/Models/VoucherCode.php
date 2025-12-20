<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class VoucherCode extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_id',
        'product_id',
        'code',
        'is_used',
        'used_at',
    ];

    protected $casts = [
        'is_used' => 'boolean',
        'used_at' => 'datetime',
    ];

    // Relationships
    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // Scopes
    public function scopeUnused($query)
    {
        return $query->where('is_used', false);
    }

    public function scopeUsed($query)
    {
        return $query->where('is_used', true);
    }

    // Helper methods
    public function markAsUsed()
    {
        $this->update([
            'is_used' => true,
            'used_at' => now(),
        ]);
    }

    // Generate voucher code
    public static function generateCode($format = 'XXXX-XXXX-XXXX')
    {
        do {
            $code = '';
            $parts = explode('-', $format);
            
            foreach ($parts as $part) {
                $length = strlen($part);
                $code .= strtoupper(Str::random($length)) . '-';
            }
            
            $code = rtrim($code, '-');
        } while (self::where('code', $code)->exists());
        
        return $code;
    }
}