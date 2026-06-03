<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ChallengeAttemptFactory extends Factory
{
    public function definition(): array
    {
        return [
            'status' => 'completed',
            'started_at' => now()->subSeconds(60),
            'completed_at' => now(),
            'duration_seconds' => 60,
            'total_words' => 10,
            'correct_words' => 9,
            'wrong_words' => 1,
            'total_characters' => 50,
            'correct_characters' => 48,
            'wrong_characters' => 2,
            'wpm' => 40,
            'accuracy' => 96.0,
            'mistake_count' => 2,
            'remaining_lives' => 1,
        ];
    }
}
