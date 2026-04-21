<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductVariant extends Model
{
    protected $fillable = [
        'product_id', 'type', 'value', 'sku', 'image',
        'price_adjustment', 'stock', 'is_active', 'sort_order',
    ];

    protected $casts = [
        'price_adjustment' => 'decimal:4',
        'stock'            => 'integer',
        'is_active'        => 'boolean',
        'sort_order'       => 'integer',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function getPriceAttribute(): float
    {
        return round($this->product->price + $this->price_adjustment, 4);
    }

    public function getPriceWithVatAttribute(): float
    {
        return round($this->price * (1 + $this->product->vat_rate / 100), 4);
    }
}
