<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuotationItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'quotation_id',
        'product_id',
        'product_snapshot',
        'quantity',
        'unit_price',
        'quoted_price',
        'vat_rate',
        'vat_amount',
        'total',
        'notes',
    ];

    protected $casts = [
        'quantity'         => 'integer',
        'unit_price'       => 'decimal:4',
        'quoted_price'     => 'decimal:4',
        'vat_rate'         => 'decimal:2',
        'vat_amount'       => 'decimal:4',
        'total'            => 'decimal:4',
        'product_snapshot' => 'array',
    ];

    public function quotation(): BelongsTo
    {
        return $this->belongsTo(Quotation::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class)->withDefault();
    }

    // ── Accessors ────────────────────────────────────────────────────

    public function getProductNameAttribute(): string
    {
        return $this->product?->getTranslation('name', app()->getLocale()) ?? '—';
    }

    public function getSkuAttribute(): string
    {
        return $this->product?->sku ?? '—';
    }

    /** Unit price including VAT (what admin quoted) */
    public function getEffectivePriceAttribute(): float
    {
        return (float) ($this->quoted_price ?? ($this->product?->price_with_vat ?? 0));
    }

    /** Line total (effective_price * quantity) */
    public function getLineTotalAttribute(): float
    {
        return round($this->effective_price * $this->quantity, 4);
    }

    /** Unit price excluding VAT, derived from quoted_price */
    public function getUnitPriceExVatAttribute(): float
    {
        if (! $this->quoted_price) return 0;
        $rate = (float) ($this->vat_rate ?? $this->product?->vat_rate ?? 21);
        return round($this->quoted_price / (1 + $rate / 100), 4);
    }

    public function getHasQuotedPriceAttribute(): bool
    {
        return ! is_null($this->quoted_price);
    }
}
