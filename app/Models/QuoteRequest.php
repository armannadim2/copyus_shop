<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuoteRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'reference', 'user_id',
        'name', 'email', 'phone', 'company_name', 'cif',
        'service_type', 'quantity', 'deadline', 'budget_range',
        'description', 'attachment_path',
        'status', 'admin_notes',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function generateReference(): string
    {
        do {
            $ref = 'QR-' . strtoupper(substr(uniqid(), -6));
        } while (static::where('reference', $ref)->exists());

        return $ref;
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'new'       => 'bg-blue-50 text-blue-700',
            'in_review' => 'bg-amber-50 text-amber-700',
            'quoted'    => 'bg-green-50 text-green-700',
            'closed'    => 'bg-gray-100 text-gray-500',
            default     => 'bg-gray-100 text-gray-500',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'new'       => 'Nova',
            'in_review' => 'En revisió',
            'quoted'    => 'Pressupostada',
            'closed'    => 'Tancada',
            default     => $this->status,
        };
    }
}
