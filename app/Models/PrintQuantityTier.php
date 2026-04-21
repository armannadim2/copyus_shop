<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Translatable\HasTranslations;

class PrintQuantityTier extends Model
{
    use HasTranslations;

    public array $translatable = ['label'];

    protected $fillable = [
        'print_template_id', 'min_quantity', 'discount_percent', 'label', 'is_active',
    ];

    protected $casts = [
        'min_quantity'      => 'integer',
        'discount_percent'  => 'decimal:2',
        'is_active'         => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function template(): BelongsTo
    {
        return $this->belongsTo(PrintTemplate::class, 'print_template_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Static helpers
    |--------------------------------------------------------------------------
    */

    /**
     * Find the best applicable tier for the given template and quantity.
     */
    public static function resolve(PrintTemplate $template, int $quantity): ?self
    {
        return static::where('print_template_id', $template->id)
            ->where('min_quantity', '<=', $quantity)
            ->where('is_active', true)
            ->orderByDesc('min_quantity')
            ->first();
    }
}
