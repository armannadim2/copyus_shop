<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_number', 'user_id', 'order_id', 'print_job_id',
        'subject', 'body', 'status', 'priority', 'resolved_at',
    ];

    protected $casts = [
        'resolved_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function printJob(): BelongsTo
    {
        return $this->belongsTo(PrintJob::class);
    }

    public function replies(): HasMany
    {
        return $this->hasMany(TicketReply::class)->orderBy('created_at');
    }

    public static function generateTicketNumber(): string
    {
        do {
            $number = 'TKT-' . strtoupper(substr(uniqid(), -6));
        } while (static::where('ticket_number', $number)->exists());

        return $number;
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'open'        => 'bg-blue-50 text-blue-700',
            'in_progress' => 'bg-amber-50 text-amber-700',
            'resolved'    => 'bg-green-50 text-green-700',
            'closed'      => 'bg-gray-100 text-gray-500',
            default       => 'bg-gray-100 text-gray-500',
        };
    }

    public function getPriorityColorAttribute(): string
    {
        return match ($this->priority) {
            'urgent' => 'bg-red-50 text-red-600',
            'high'   => 'bg-orange-50 text-orange-600',
            'medium' => 'bg-yellow-50 text-yellow-600',
            default  => 'bg-gray-100 text-gray-500',
        };
    }
}
