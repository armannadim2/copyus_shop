<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class ProductPriceTier extends Model
{
    protected $fillable = [
        'product_id', 'user_id', 'min_quantity',
        'price', 'label', 'valid_from', 'valid_until', 'is_active',
    ];

    protected $casts = [
        'price'        => 'decimal:4',
        'min_quantity' => 'integer',
        'is_active'    => 'boolean',
        'valid_from'   => 'date',
        'valid_until'  => 'date',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where(fn($q) => $q->whereNull('valid_from')->orWhere('valid_from', '<=', now()))
            ->where(fn($q) => $q->whereNull('valid_until')->orWhere('valid_until', '>=', now()));
    }

    /**
     * Resolve the best price for a given user and quantity.
     * Precedence: user-specific > quantity-based > base price.
     */
    public static function resolvePrice(Product $product, int $quantity, ?User $user = null): ?float
    {
        $query = static::active()->where('product_id', $product->id)
            ->where('min_quantity', '<=', $quantity)
            ->orderByDesc('min_quantity')
            ->orderByDesc('user_id'); // user-specific rows first

        if ($user) {
            $tier = (clone $query)->where('user_id', $user->id)->first()
                ?? (clone $query)->whereNull('user_id')->first();
        } else {
            $tier = (clone $query)->whereNull('user_id')->first();
        }

        return $tier?->price;
    }
}
