<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'description',
        'account_fields',
        'price',
        'discount_type',
        'discount_value',
        'stock',
        'image',
        'code_format',
        'is_active',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'discount_value' => 'decimal:2',
        'stock' => 'integer',
        'is_active' => 'boolean',
        'account_fields' => 'array',
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

    // Accessors & Mutators
    public function setNameAttribute($value)
    {
        $this->attributes['name'] = $value;
        $this->attributes['slug'] = Str::slug($value);
    }

    public function getPriceFormattedAttribute()
    {
        return 'Rp ' . number_format($this->price, 0, ',', '.');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeInStock($query)
    {
        return $query->where('stock', '>', 0);
    }

    // Helper methods
    public function isInStock()
    {
        return $this->stock > 0;
    }

    public function decreaseStock($quantity = 1)
    {
        if ($this->stock >= $quantity) {
            $this->decrement('stock', $quantity);
            return true;
        }
        return false;
    }

    public function increaseStock($quantity = 1)
    {
        $this->increment('stock', $quantity);
    }

    // Discount methods
    public function hasDiscount()
    {
        return $this->discount_type && $this->discount_value > 0;
    }

    public function getDiscountAmount()
    {
        if (!$this->hasDiscount()) {
            return 0;
        }

        if ($this->discount_type === 'percentage') {
            return ($this->price * $this->discount_value) / 100;
        }

        return $this->discount_value; // fixed amount
    }

    public function getFinalPrice()
    {
        return $this->price - $this->getDiscountAmount();
    }

    public function getFinalPriceFormattedAttribute()
    {
        return 'Rp ' . number_format($this->getFinalPrice(), 0, ',', '.');
    }

    public function getDiscountPercentage()
    {
        if (!$this->hasDiscount()) {
            return 0;
        }

        if ($this->discount_type === 'percentage') {
            return $this->discount_value;
        }

        // Calculate percentage for fixed discount
        return ($this->discount_value / $this->price) * 100;
    }
}