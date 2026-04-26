<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContactMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'name', 'email', 'phone',
        'subject', 'message',
        'status', 'admin_notes', 'read_at',
    ];

    protected $casts = [
        'read_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'new'      => 'bg-blue-50 text-blue-700',
            'read'     => 'bg-amber-50 text-amber-700',
            'replied'  => 'bg-green-50 text-green-700',
            'archived' => 'bg-gray-100 text-gray-500',
            default    => 'bg-gray-100 text-gray-500',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'new'      => 'Nou',
            'read'     => 'Llegit',
            'replied'  => 'Respost',
            'archived' => 'Arxivat',
            default    => $this->status,
        };
    }
}
