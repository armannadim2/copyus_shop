<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\HasTranslations;

class PrintOption extends Model
{
    use HasTranslations;

    public array $translatable = ['label'];

    protected $fillable = [
        'print_template_id', 'key', 'label',
        'input_type', 'is_required', 'sort_order',
    ];

    protected $casts = [
        'is_required' => 'boolean',
        'sort_order'  => 'integer',
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

    public function values(): HasMany
    {
        return $this->hasMany(PrintOptionValue::class)->orderBy('sort_order');
    }

    public function activeValues(): HasMany
    {
        return $this->hasMany(PrintOptionValue::class)
            ->where('is_active', true)
            ->orderBy('sort_order');
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    public function getDefaultValueAttribute(): ?PrintOptionValue
    {
        return $this->values->firstWhere('is_default', true)
            ?? $this->values->first();
    }
}
