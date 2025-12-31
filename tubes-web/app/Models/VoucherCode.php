<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class VoucherCode extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'discount_type',
        'discount_value',
        'category_id',
        'min_purchase',
        'max_discount',
        'valid_from',
        'valid_until',
        'usage_limit',
        'used_count',
        'is_active',
    ];

    protected $casts = [
        'discount_value' => 'decimal:2',
        'min_purchase' => 'decimal:2',
        'max_discount' => 'decimal:2',
        'valid_from' => 'datetime',
        'valid_until' => 'datetime',
        'usage_limit' => 'integer',
        'used_count' => 'integer',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeValid($query)
    {
        return $query->where('is_active', true)
            ->where(function($q) {
                $q->whereNull('valid_from')
                  ->orWhere('valid_from', '<=', now());
            })
            ->where(function($q) {
                $q->whereNull('valid_until')
                  ->orWhere('valid_until', '>=', now());
            });
    }

    public function scopeAvailable($query)
    {
        return $query->valid()
            ->whereColumn('used_count', '<', 'usage_limit');
    }

    // Helper methods
    public function isValid()
    {
        if (!$this->is_active) return false;

        if ($this->valid_from && $this->valid_from->isFuture()) return false;
        if ($this->valid_until && $this->valid_until->isPast()) return false;

        return true;
    }

    public function isAvailable()
    {
        return $this->isValid() && $this->used_count < $this->usage_limit;
    }

    public function canBeUsedFor($product, $totalAmount)
    {
        if (!$this->isAvailable()) return false;

        // Check if voucher is for specific category
        if ($this->category_id && $this->category_id != $product->category_id) {
            return false;
        }

        // Check minimum purchase
        if ($this->min_purchase && $totalAmount < $this->min_purchase) {
            return false;
        }

        return true;
    }

    public function calculateDiscount($amount)
    {
        if ($this->discount_type === 'percentage') {
            $discount = ($amount * $this->discount_value) / 100;

            // Apply max discount if set
            if ($this->max_discount && $discount > $this->max_discount) {
                $discount = $this->max_discount;
            }

            return $discount;
        }

        // Fixed discount
        return min($this->discount_value, $amount); // Can't discount more than the amount
    }

    public function incrementUsage()
    {
        $this->increment('used_count');
    }

    // Generate voucher code
    public static function generateCode()
    {
        do {
            $code = strtoupper(Str::random(8));
        } while (self::where('code', $code)->exists());

        return $code;
    }
}