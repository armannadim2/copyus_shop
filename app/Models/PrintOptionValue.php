<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Translatable\HasTranslations;

class PrintOptionValue extends Model
{
    use HasTranslations;

    public array $translatable = ['label'];

    protected $fillable = [
        'print_option_id', 'value_key', 'label',
        'price_modifier', 'price_modifier_type',
        'production_days_modifier',
        'is_default', 'is_active', 'sort_order',
    ];

    protected $casts = [
        'price_modifier'           => 'decimal:4',
        'production_days_modifier' => 'integer',
        'is_default'               => 'boolean',
        'is_active'                => 'boolean',
        'sort_order'               => 'integer',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function option(): BelongsTo
    {
        return $this->belongsTo(PrintOption::class, 'print_option_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    /** Signed price modifier for display (e.g. +0.05 or -0.02). */
    public function getPriceModifierSignedAttribute(): float
    {
        return (float) $this->price_modifier;
    }
}
