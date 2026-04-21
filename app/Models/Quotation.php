<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Quotation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'order_id',
        'converted_to_order_id',
        'quote_number',
        'status',
        'quoted_subtotal',
        'quoted_vat',
        'quoted_total',
        'admin_notes',
        'customer_notes',
        'valid_until',
        'locale',
    ];

    protected $casts = [
        'quoted_subtotal' => 'decimal:4',
        'quoted_vat'      => 'decimal:4',
        'quoted_total'    => 'decimal:4',
        'valid_until'     => 'date',
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
        return $this->hasMany(QuotationItem::class);
    }

    public function convertedOrder(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'converted_to_order_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Static Helpers
    |--------------------------------------------------------------------------
    */

    public static function generateQuoteNumber(): string
    {
        do {
            $number = 'QUO-' . strtoupper(substr(uniqid(), -6));
        } while (static::where('quote_number', $number)->exists());

        return $number;
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeQuoted($query)
    {
        return $query->where('status', 'quoted');
    }

    public function scopeActive($query)
    {
        return $query->whereIn('status', ['pending', 'reviewing', 'quoted']);
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    /** Alias so existing views using ->total_quoted still work */
    public function getTotalQuotedAttribute(): ?float
    {
        return $this->quoted_total ? (float) $this->quoted_total : null;
    }

    public function getIsExpiredAttribute(): bool
    {
        return $this->valid_until
            && $this->valid_until->isPast()
            && $this->status === 'quoted';
    }

    public function getIsAcceptableAttribute(): bool
    {
        return $this->status === 'quoted'
            && (!$this->valid_until || $this->valid_until->isFuture());
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'pending'   => 'bg-yellow-50 text-yellow-700',
            'reviewing' => 'bg-blue-50 text-blue-700',
            'quoted'    => 'bg-purple-50 text-purple-700',
            'accepted'  => 'bg-green-50 text-green-700',
            'rejected'  => 'bg-red-50 text-red-600',
            'expired'   => 'bg-gray-100 text-gray-500',
            'converted' => 'bg-indigo-50 text-indigo-700',
            default     => 'bg-gray-50 text-gray-500',
        };
    }

    public function getItemCountAttribute(): int
    {
        return $this->items->sum('quantity');
    }
}
