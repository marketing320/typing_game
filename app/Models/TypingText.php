<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TypingText extends Model
{
    use HasFactory;
    protected $fillable = [
        'challenge_id', 'mode', 'title', 'content', 'language', 'difficulty', 'is_active', 'created_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function challenge(): BelongsTo
    {
        return $this->belongsTo(TypingChallenge::class, 'challenge_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'created_by');
    }

    public function challengeAttempts(): HasMany
    {
        return $this->hasMany(ChallengeAttempt::class, 'typing_text_id');
    }

    public function rehearsalAttempts(): HasMany
    {
        return $this->hasMany(RehearsalAttempt::class, 'typing_text_id');
    }
}
