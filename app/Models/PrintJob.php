<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class PrintJob extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'print_template_id', 'status',
        'configuration', 'quantity',
        'unit_price', 'total_price', 'production_days',
        'artwork_path', 'artwork_notes', 'admin_notes',
        'expected_delivery_at', 'produced_at', 'received_at',
    ];

    protected $casts = [
        'configuration'       => 'array',
        'quantity'            => 'integer',
        'unit_price'          => 'decimal:4',
        'total_price'         => 'decimal:4',
        'production_days'     => 'integer',
        'expected_delivery_at'=> 'date',
        'produced_at'         => 'datetime',
        'received_at'         => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(PrintTemplate::class, 'print_template_id')->withDefault();
    }

    public function cartItem(): HasOne
    {
        return $this->hasOne(CartItem::class);
    }

    public function productionLog(): HasMany
    {
        return $this->hasMany(PrintProductionLog::class)->latest();
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    public function getIsEditableAttribute(): bool
    {
        return in_array($this->status, ['draft', 'in_cart']);
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'draft'         => 'gray',
            'in_cart'       => 'blue',
            'ordered'       => 'yellow',
            'in_production' => 'orange',
            'completed'     => 'green',
            'cancelled'     => 'red',
            default         => 'gray',
        };
    }

    /**
     * Build a human-readable summary of the configuration using loaded template options.
     * Template must be loaded with options.values for this to work efficiently.
     */
    public function getConfigurationLabelsAttribute(): array
    {
        $labels = [];
        $template = $this->template;
        if (!$template || !$this->configuration) return $labels;

        $locale = app()->getLocale();

        foreach ($this->configuration as $optionKey => $valueKey) {
            $option = $template->options->firstWhere('key', $optionKey);
            if (!$option) continue;
            $value = $option->values->firstWhere('value_key', $valueKey);
            if (!$value) continue;

            $labels[$option->getTranslation('label', $locale)] =
                $value->getTranslation('label', $locale);
        }

        return $labels;
    }

    /**
     * Snapshot data for storing in order_items.product_snapshot.
     */
    public function toOrderSnapshot(): array
    {
        $locale = app()->getLocale();

        return [
            'type'                 => 'print_job',
            'name'                 => $this->template->getTranslation('name', $locale),
            'sku'                  => 'PRINT-' . strtoupper($this->template->slug) . '-' . $this->id,
            'unit'                 => 'unitats',
            'print_job_id'         => $this->id,
            'template_slug'        => $this->template->slug,
            'configuration'        => $this->configuration,
            'configuration_labels' => $this->configuration_labels,
            'quantity'             => $this->quantity,
            'unit_price'           => (float) $this->unit_price,
            'production_days'      => $this->production_days,
            'artwork_path'         => $this->artwork_path,
        ];
    }
}
