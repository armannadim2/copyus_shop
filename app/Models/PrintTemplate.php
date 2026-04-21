<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\HasTranslations;

class PrintTemplate extends Model
{
    use HasFactory, HasTranslations;

    public array $translatable = ['name', 'description'];

    protected $fillable = [
        'slug', 'name', 'description', 'icon',
        'base_price', 'vat_rate', 'base_production_days',
        'sort_order', 'is_active',
        'specifications_path', 'specifications_label',
    ];

    protected $casts = [
        'base_price'           => 'decimal:4',
        'vat_rate'             => 'decimal:2',
        'base_production_days' => 'integer',
        'sort_order'           => 'integer',
        'is_active'            => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function options(): HasMany
    {
        return $this->hasMany(PrintOption::class)->orderBy('sort_order');
    }

    public function quantityTiers(): HasMany
    {
        return $this->hasMany(PrintQuantityTier::class)->orderBy('min_quantity');
    }

    public function compatibilityRules(): HasMany
    {
        return $this->hasMany(PrintCompatibilityRule::class);
    }

    public function jobs(): HasMany
    {
        return $this->hasMany(PrintJob::class);
    }

    public function artworks(): HasMany
    {
        return $this->hasMany(PrintTemplateArtwork::class)->orderBy('sort_order');
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    public function getPriceWithVatAttribute(): float
    {
        return round($this->base_price * (1 + $this->vat_rate / 100), 4);
    }

    /**
     * Serialize compatibility rules to a flat array for Alpine.js.
     */
    public function getCompatibilityRulesForFrontendAttribute(): array
    {
        return $this->compatibilityRules->map(fn($r) => [
            'rule_type'            => $r->rule_type,
            'condition_option_key' => $r->condition_option_key,
            'condition_value_key'  => $r->condition_value_key,
            'target_option_key'    => $r->target_option_key,
            'target_value_key'     => $r->target_value_key,
            'message'              => $r->getTranslation('message', app()->getLocale()),
        ])->toArray();
    }
}
