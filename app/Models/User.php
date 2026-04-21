<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Models\SavedAddress;
use App\Models\Wishlist;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    const ROLE_ADMIN    = 'admin';
    const ROLE_APPROVED = 'approved';
    const ROLE_PENDING  = 'pending';
    const ROLE_REJECTED = 'rejected';

    protected $fillable = [
        'name',
        'email',
        'password',
        'company_name',
        'cif',
        'phone',
        'address',
        'city',
        'postal_code',
        'country',
        'requires_invoice',
        'role',
        'locale',
        'is_active',
        'approved_at',
        'company_id',
        'company_role',
        'spending_limit',
        'cart_recovery_sent_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at'      => 'datetime',
        'approved_at'            => 'datetime',
        'cart_recovery_sent_at'  => 'datetime',
        'is_active'              => 'boolean',
        'requires_invoice'       => 'boolean',
        'spending_limit'         => 'decimal:2',
    ];

    // -------------------------------------------------------
    // Company Role Helpers
    // -------------------------------------------------------

    public function isCompanyOwner(): bool
    {
        return $this->company_role === 'owner';
    }

    public function isCompanyManager(): bool
    {
        return in_array($this->company_role, ['owner', 'manager']);
    }

    public function canManageCompany(): bool
    {
        return in_array($this->company_role, ['owner', 'manager']);
    }

    public function canPlaceOrders(): bool
    {
        return in_array($this->company_role, ['owner', 'manager', 'buyer'])
            || is_null($this->company_role);
    }

    // -------------------------------------------------------
    // Role Helpers
    // -------------------------------------------------------

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isApproved(): bool
    {
        return $this->role === self::ROLE_APPROVED;
    }

    public function isPending(): bool
    {
        return $this->role === self::ROLE_PENDING;
    }

    public function isRejected(): bool
    {
        return $this->role === self::ROLE_REJECTED;
    }

    public function isActive(): bool
    {
        return $this->is_active === true;
    }

    public function canSeePrices(): bool
    {
        return in_array($this->role, [self::ROLE_ADMIN, self::ROLE_APPROVED]);
    }

    // -------------------------------------------------------
    // Accessors
    // -------------------------------------------------------

    public function getIsAdminAttribute(): bool
    {
        return $this->isAdmin();
    }

    public function getIsApprovedAttribute(): bool
    {
        return $this->isApproved();
    }

    public function getIsPendingAttribute(): bool
    {
        return $this->isPending();
    }

    public function getIsRejectedAttribute(): bool
    {
        return $this->isRejected();
    }

    public function getFullAddressAttribute(): string
    {
        return trim(implode(', ', array_filter([
            $this->address,
            $this->postal_code,
            $this->city,
            $this->country,
        ])), ', ');
    }

    // -------------------------------------------------------
    // Relationships
    // -------------------------------------------------------

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function cartItems(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    public function cart(): HasOne
    {
        return $this->hasOne(Cart::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function quotations(): HasMany
    {
        return $this->hasMany(Quotation::class);
    }

    public function savedAddresses(): HasMany
    {
        return $this->hasMany(SavedAddress::class);
    }

    public function wishlist(): HasMany
    {
        return $this->hasMany(Wishlist::class);
    }

    // -------------------------------------------------------
    // Scopes
    // -------------------------------------------------------

    public function scopeAdmins($query)
    {
        return $query->where('role', self::ROLE_ADMIN);
    }

    public function scopeApproved($query)
    {
        return $query->where('role', self::ROLE_APPROVED);
    }

    public function scopePending($query)
    {
        return $query->where('role', self::ROLE_PENDING);
    }

    public function scopeRejected($query)
    {
        return $query->where('role', self::ROLE_REJECTED);
    }
}
