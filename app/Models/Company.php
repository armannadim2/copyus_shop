<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Company extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'cif_vat', 'email', 'phone',
        'address', 'city', 'postal_code', 'country',
        'payment_terms', 'credit_limit', 'credit_used',
        'approval_threshold', 'is_active',
    ];

    protected $casts = [
        'credit_limit'       => 'decimal:2',
        'credit_used'        => 'decimal:2',
        'approval_threshold' => 'decimal:2',
        'is_active'          => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function members(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function invitations(): HasMany
    {
        return $this->hasMany(CompanyInvitation::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors & Helpers
    |--------------------------------------------------------------------------
    */

    public function getOwner(): ?User
    {
        return $this->members()->where('company_role', 'owner')->first();
    }

    public function getPaymentDaysAttribute(): int
    {
        return match ($this->payment_terms) {
            'net_15' => 15,
            'net_30' => 30,
            'net_60' => 60,
            'net_90' => 90,
            default  => 0,
        };
    }

    public function getPaymentTermsLabelAttribute(): string
    {
        return match ($this->payment_terms) {
            'net_15' => 'Net 15',
            'net_30' => 'Net 30',
            'net_60' => 'Net 60',
            'net_90' => 'Net 90',
            default  => 'Pagament immediat',
        };
    }

    public function getCreditAvailableAttribute(): float
    {
        return max(0, (float) $this->credit_limit - (float) $this->credit_used);
    }

    public function hasAvailableCredit(float $amount): bool
    {
        if ($this->credit_limit <= 0) {
            return true; // No credit limit set
        }

        return $this->credit_available >= $amount;
    }

    public function needsApproval(float $amount): bool
    {
        return $this->approval_threshold !== null
            && $amount > (float) $this->approval_threshold;
    }
}
