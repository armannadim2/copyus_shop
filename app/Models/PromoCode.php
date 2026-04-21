<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PromoCode extends Model
{
    protected $fillable = [
        'code', 'description', 'type', 'value',
        'min_order_total', 'max_uses', 'used_count',
        'max_uses_per_user', 'valid_from', 'valid_until', 'is_active',
    ];

    protected $casts = [
        'value'           => 'decimal:2',
        'min_order_total' => 'decimal:2',
        'is_active'       => 'boolean',
        'valid_from'      => 'date',
        'valid_until'     => 'date',
    ];

    /** Check if code is usable right now for a given subtotal and user. */
    public function isValid(float $subtotal, ?User $user = null): bool|string
    {
        if (!$this->is_active) return 'Codi de descompte inactiu.';

        $today = now()->toDateString();
        if ($this->valid_from && $this->valid_from->gt(now())) return 'El codi encara no és vàlid.';
        if ($this->valid_until && $this->valid_until->lt(now())) return 'El codi ha caducat.';

        if ($this->max_uses && $this->used_count >= $this->max_uses) return 'El codi ha esgotat els usos.';

        if ($this->min_order_total && $subtotal < $this->min_order_total) {
            return "Comanda mínima de {$this->min_order_total} € per aplicar aquest codi.";
        }

        if ($this->max_uses_per_user && $user) {
            $usedByUser = Order::where('user_id', $user->id)
                ->where('promo_code', $this->code)
                ->count();
            if ($usedByUser >= $this->max_uses_per_user) return 'Ja has usat aquest codi el màxim de vegades.';
        }

        return true;
    }

    /** Calculate the discount amount for a given subtotal. */
    public function calculateDiscount(float $subtotal): float
    {
        if ($this->type === 'percent') {
            return round($subtotal * ($this->value / 100), 4);
        }
        return min($this->value, $subtotal);
    }
}
