<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class TypingTextFactory extends Factory
{
    public function definition(): array
    {
        return [
            'mode' => 'challenge',
            'title' => $this->faker->sentence(3),
            'content' => $this->faker->sentences(3, true),
            'language' => 'en',
            'difficulty' => 'medium',
            'is_active' => true,
        ];
    }
}
