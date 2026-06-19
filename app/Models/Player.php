<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Player extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = ['username', 'email', 'full_name', 'phone', 'referral_source', 'email_verified_at', 'last_login_at', 'is_blocked'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login_at' => 'datetime',
        'is_blocked' => 'boolean',
    ];

    public function challengeAttempts(): HasMany
    {
        return $this->hasMany(ChallengeAttempt::class);
    }

    public function devices(): HasMany
    {
        return $this->hasMany(PlayerDevice::class);
    }

    public function isVerified(): bool
    {
        return $this->email_verified_at !== null;
    }

    public function bestWpm(): float
    {
        return (float) $this->challengeAttempts()
            ->where('status', 'completed')
            ->max('wpm') ?? 0;
    }

    public function bestAccuracy(): float
    {
        return (float) $this->challengeAttempts()
            ->where('status', 'completed')
            ->max('accuracy') ?? 0;
    }
}
