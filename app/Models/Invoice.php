<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'user_id',
        'invoice_number',
        'status',
        'subtotal',
        'vat_amount',
        'total',
        'locale',
        'company_details',
        'billing_address',
        'issued_at',
        'due_at',
        'paid_at',
    ];

    protected $casts = [
        'company_details' => 'array',
        'billing_address' => 'array',
        'subtotal'        => 'decimal:4',
        'vat_amount'      => 'decimal:4',
        'total'           => 'decimal:4',
        'issued_at'       => 'datetime',
        'due_at'          => 'datetime',
        'paid_at'         => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | Static Helpers
    |--------------------------------------------------------------------------
    */

    public static function generateInvoiceNumber(): string
    {
        do {
            $number = 'INV-' . date('Y') . '-' . str_pad(
                (static::whereYear('created_at', date('Y'))->count() + 1),
                5, '0', STR_PAD_LEFT
            );
        } while (static::where('invoice_number', $number)->exists());

        return $number;
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    public function getIsOverdueAttribute(): bool
    {
        return $this->due_at
            && $this->due_at->isPast()
            && $this->status !== 'paid';
    }

    public function getIsPaidAttribute(): bool
    {
        return $this->status === 'paid';
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'issued'    => 'bg-blue-50 text-blue-700',
            'paid'      => 'bg-green-50 text-green-700',
            'overdue'   => 'bg-red-50 text-red-600',
            'cancelled' => 'bg-gray-100 text-gray-500',
            default     => 'bg-gray-50 text-gray-500',
        };
    }
}
