<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class CompanyInvitation extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id', 'email', 'role', 'token', 'expires_at', 'accepted_at',
    ];

    protected $casts = [
        'expires_at'  => 'datetime',
        'accepted_at' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    public static function generate(Company $company, string $email, string $role): self
    {
        // Revoke any existing pending invitation for this email
        static::where('company_id', $company->id)
            ->where('email', $email)
            ->whereNull('accepted_at')
            ->delete();

        return static::create([
            'company_id' => $company->id,
            'email'      => strtolower(trim($email)),
            'role'       => $role,
            'token'      => Str::random(64),
            'expires_at' => now()->addDays(7),
        ]);
    }

    public function isValid(): bool
    {
        return is_null($this->accepted_at)
            && $this->expires_at->isFuture();
    }
}
