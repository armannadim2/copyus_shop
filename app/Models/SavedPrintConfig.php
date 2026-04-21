<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SavedPrintConfig extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'print_template_id', 'name',
        'configuration', 'quantity', 'artwork_notes',
    ];

    protected $casts = [
        'configuration' => 'array',
        'quantity'      => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(PrintTemplate::class, 'print_template_id');
    }
}
