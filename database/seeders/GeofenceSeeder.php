<?php

namespace Database\Seeders;

use App\Models\GeofenceRule;
use Illuminate\Database\Seeder;

class GeofenceSeeder extends Seeder
{
    public function run(): void
    {
        GeofenceRule::firstOrCreate(
            ['name' => 'Plaza Low Yat'],
            [
                'latitude' => 3.1442749,
                'longitude' => 101.7114161,
                'radius_meters' => 5000,
                'warning_message' => 'You are outside the allowed event area. Please move closer to participate in the Challenge Mode.',
                'is_active' => true,
            ]
        );
    }
}
