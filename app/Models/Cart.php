<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cart extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'session_id',
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

    public function items(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    public function getSubtotalAttribute(): float
    {
        return round(
            $this->items->sum(fn($item) => (float) $item->unit_price * $item->quantity),
            4
        );
    }

    public function getVatAmountAttribute(): float
    {
        return round(
            $this->items->sum(fn($item) => (float) $item->unit_price * $item->quantity * ($item->effective_vat_rate / 100)),
            4
        );
    }

    public function getTotalAttribute(): float
    {
        return round($this->subtotal + $this->vat_amount, 4);
    }

    public function getItemCountAttribute(): int
    {
        return $this->items->sum('quantity');
    }

    public function getIsEmptyAttribute(): bool
    {
        return $this->items->isEmpty();
    }
}
