<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RehearsalAttempt extends Model
{
    protected $fillable = [
        'typing_text_id', 'anonymous_id',
        'started_at', 'completed_at', 'duration_seconds',
        'total_words', 'correct_words', 'wrong_words',
        'total_characters', 'correct_characters', 'wrong_characters',
        'wpm', 'accuracy', 'user_input', 'ip_address', 'user_agent', 'device_fingerprint',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'duration_seconds' => 'float',
        'wpm' => 'float',
        'accuracy' => 'float',
    ];

    public function typingText(): BelongsTo
    {
        return $this->belongsTo(TypingText::class, 'typing_text_id');
    }
}
