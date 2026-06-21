<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TypingChallenge extends Model
{
    use HasFactory;
    protected $fillable = [
        'title', 'description', 'status', 'start_at', 'end_at',
        'allow_retry_next_day', 'max_attempts_per_day', 'require_unique_email',
        'require_geofence', 'geofence_rule_id', 'created_by',
    ];

    protected $casts = [
        'start_at' => 'datetime',
        'end_at' => 'datetime',
        'allow_retry_next_day' => 'boolean',
        'require_unique_email' => 'boolean',
        'require_geofence' => 'boolean',
    ];

    public function geofenceRule(): BelongsTo
    {
        return $this->belongsTo(GeofenceRule::class, 'geofence_rule_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'created_by');
    }

    public function typingTexts(): HasMany
    {
        return $this->hasMany(TypingText::class, 'challenge_id');
    }

    public function attempts(): HasMany
    {
        return $this->hasMany(ChallengeAttempt::class, 'challenge_id');
    }

    public function activeText(): ?TypingText
    {
        return $this->typingTexts()
            ->where('mode', 'challenge')
            ->where('is_active', true)
            ->first();
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }
}
