<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CartItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'cart_id', 'user_id', 'product_id', 'print_job_id',
        'quantity', 'type', 'unit_price', 'configuration_snapshot',
    ];

    protected $casts = [
        'quantity'               => 'integer',
        'unit_price'             => 'decimal:4',
        'configuration_snapshot' => 'array',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class)->withDefault();
    }

    public function printJob(): BelongsTo
    {
        return $this->belongsTo(PrintJob::class)->withDefault();
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    public function getIsPrintJobAttribute(): bool
    {
        return !is_null($this->print_job_id);
    }

    /**
     * Resolved unit price (excl. VAT):
     * - For print jobs: frozen price stored at cart-add time.
     * - For products: live price from the product model.
     */
    public function getEffectiveUnitPriceAttribute(): float
    {
        if ($this->is_print_job) {
            return (float) ($this->unit_price ?? 0);
        }

        return (float) ($this->product?->price ?? 0);
    }

    /**
     * Resolved VAT rate.
     */
    public function getEffectiveVatRateAttribute(): float
    {
        if ($this->is_print_job) {
            return (float) ($this->printJob?->template?->vat_rate ?? 21);
        }

        return (float) ($this->product?->vat_rate ?? 21);
    }

    /**
     * Line total including VAT.
     */
    public function getLineTotalAttribute(): float
    {
        return round(
            $this->effective_unit_price * (1 + $this->effective_vat_rate / 100) * $this->quantity,
            4
        );
    }

    /**
     * Display name for the cart item.
     */
    public function getDisplayNameAttribute(): string
    {
        if ($this->is_print_job) {
            $locale = app()->getLocale();
            return $this->printJob?->template?->getTranslation('name', $locale) ?? 'Treball d\'impressió';
        }

        return $this->product?->getTranslation('name', app()->getLocale()) ?? '';
    }
}
