<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Slider extends Model
{
    protected $fillable = [
        'title',
        'image',
        'link_type',
        'link_value',
        'order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'order' => 'integer',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'link_value');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order', 'asc');
    }

    public function getLinkUrlAttribute()
    {
        return match($this->link_type) {
            'product' => $this->product ? route('products.show', $this->product->slug) : '#',
            'url' => $this->link_value,
            default => '#',
        };
    }
}
