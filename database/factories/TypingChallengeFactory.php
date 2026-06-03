<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class TypingChallengeFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(4),
            'description' => $this->faker->sentence(),
            'status' => 'active',
            'allow_retry_next_day' => false,
            'max_attempts_per_day' => 1,
            'require_geofence' => false,
        ];
    }
}
