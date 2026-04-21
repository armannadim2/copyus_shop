<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Translatable\HasTranslations;

class PrintCompatibilityRule extends Model
{
    use HasTranslations;

    public array $translatable = ['message'];

    protected $fillable = [
        'print_template_id',
        'rule_type',
        'condition_option_key',
        'condition_value_key',
        'target_option_key',
        'target_value_key',
        'message',
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
}
