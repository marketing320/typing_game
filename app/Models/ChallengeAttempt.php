<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChallengeAttempt extends Model
{
    use HasFactory;
    protected $fillable = [
        'challenge_id', 'player_id', 'typing_text_id', 'status',
        'started_at', 'completed_at', 'duration_seconds',
        'total_words', 'correct_words', 'wrong_words',
        'total_characters', 'correct_characters', 'wrong_characters',
        'wpm', 'accuracy', 'mistake_count', 'remaining_lives',
        'user_input', 'ip_address', 'user_agent',
        'latitude', 'longitude', 'distance_from_allowed_meters', 'is_within_geofence',
        'device_fingerprint',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'duration_seconds' => 'float',
        'wpm' => 'float',
        'accuracy' => 'float',
        'is_within_geofence' => 'boolean',
        'latitude' => 'float',
        'longitude' => 'float',
    ];

    public function challenge(): BelongsTo
    {
        return $this->belongsTo(TypingChallenge::class, 'challenge_id');
    }

    public function player(): BelongsTo
    {
        return $this->belongsTo(Player::class);
    }

    public function typingText(): BelongsTo
    {
        return $this->belongsTo(TypingText::class, 'typing_text_id');
    }
}
