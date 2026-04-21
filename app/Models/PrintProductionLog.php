<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PrintProductionLog extends Model
{
    protected $fillable = [
        'print_job_id', 'admin_id', 'event',
        'previous_status', 'new_status', 'note',
    ];

    public function printJob(): BelongsTo
    {
        return $this->belongsTo(PrintJob::class);
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id')->withDefault([
            'name' => 'Sistema',
        ]);
    }
}
