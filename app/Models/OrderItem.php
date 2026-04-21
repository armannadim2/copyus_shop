<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'product_id',
        'product_snapshot',
        'quantity',
        'unit_price',
        'vat_rate',
        'vat_amount',
        'subtotal',
        'total',
    ];

    protected $casts = [
        'product_snapshot' => 'array',
        'quantity'         => 'integer',
        'unit_price'       => 'decimal:4',
        'vat_rate'         => 'decimal:2',
        'vat_amount'       => 'decimal:4',
        'subtotal'         => 'decimal:4',
        'total'            => 'decimal:4',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class)->withDefault();
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    /**
     * Get product name from snapshot (safe fallback chain)
     */
    public function getProductNameAttribute(): string
    {
        return $this->product_snapshot['name']
            ?? $this->product?->getTranslation('name', app()->getLocale())
            ?? '—';
    }

    /**
     * Get product SKU from snapshot
     */
    public function getSkuAttribute(): string
    {
        return $this->product_snapshot['sku']
            ?? $this->product?->sku
            ?? '—';
    }

    /**
     * Get unit from snapshot
     */
    public function getUnitAttribute(): string
    {
        return $this->product_snapshot['unit']
            ?? $this->product?->unit
            ?? '';
    }

}
