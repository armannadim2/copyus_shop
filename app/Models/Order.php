<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'order_number',
        'status',
        'payment_status',
        'payment_terms',
        'payment_due_at',
        'payment_reference',
        'payment_confirmed_at',
        'subtotal',
        'vat_amount',
        'total',
        'shipping_address',
        'billing_address',
        'notes',
        'admin_notes',
        'tracking_number',
        'tracking_url',
        'cancelled_at',
        'placed_at',
        'promo_code',
        'discount_amount',
    ];

    protected $casts = [
        'shipping_address'     => 'array',
        'billing_address'      => 'array',
        'subtotal'             => 'decimal:4',
        'vat_amount'           => 'decimal:4',
        'total'                => 'decimal:4',
        'placed_at'            => 'datetime',
        'payment_confirmed_at' => 'datetime',
        'cancelled_at'         => 'datetime',
        'payment_due_at'       => 'date',
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
        return $this->hasMany(OrderItem::class);
    }

    public function invoice(): HasOne
    {
        return $this->hasOne(Invoice::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Static Helpers
    |--------------------------------------------------------------------------
    */

    public static function generateOrderNumber(): string
    {
        do {
            $number = 'ORD-' . strtoupper(substr(uniqid(), -6));
        } while (static::where('order_number', $number)->exists());

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

    public function scopeActive($query)
    {
        return $query->whereIn('status', [
            'confirmed',
            'processing',
            'shipped',
        ]);
    }

    public function scopeDelivered($query)
    {
        return $query->where('status', 'delivered');
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    public function getItemCountAttribute(): int
    {
        return $this->items->sum('quantity');
    }

    public function getIsEditableAttribute(): bool
    {
        return in_array($this->status, ['pending']);
    }

    public function getIsCancellableAttribute(): bool
    {
        return in_array($this->status, ['pending', 'confirmed']);
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'pending'    => 'bg-yellow-50 text-yellow-700',
            'confirmed'  => 'bg-blue-50 text-blue-700',
            'processing' => 'bg-purple-50 text-purple-700',
            'shipped'    => 'bg-indigo-50 text-indigo-700',
            'delivered'  => 'bg-green-50 text-green-700',
            'cancelled'  => 'bg-red-50 text-red-600',
            default      => 'bg-gray-50 text-gray-500',
        };
    }
}
