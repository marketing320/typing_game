<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class GeofenceRuleFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->city() . ' Area',
            'latitude' => $this->faker->latitude(),
            'longitude' => $this->faker->longitude(),
            'radius_meters' => 500,
            'warning_message' => 'You are outside the allowed event area.',
            'is_active' => true,
        ];
    }
}
