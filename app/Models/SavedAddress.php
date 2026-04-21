<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SavedAddress extends Model
{
    protected $fillable = [
        'user_id', 'label', 'contact_name', 'phone',
        'address', 'city', 'postal_code', 'country', 'is_default',
    ];

    protected $casts = [
        'is_default' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function toShippingArray(): array
    {
        return [
            'address'      => $this->address,
            'city'         => $this->city,
            'postal_code'  => $this->postal_code,
            'country'      => $this->country,
            'contact_name' => $this->contact_name,
            'phone'        => $this->phone,
        ];
    }
}
