<?php

namespace Database\Seeders;

use App\Models\GeofenceRule;
use Illuminate\Database\Seeder;

class GeofenceSeeder extends Seeder
{
    public function run(): void
    {
        GeofenceRule::firstOrCreate(
            ['name' => 'Default Event Area'],
            [
                'latitude' => 0.0,
                'longitude' => 0.0,
                'radius_meters' => 500,
                'warning_message' => 'You are outside the allowed event area. Please move closer to participate in the Challenge Mode.',
                'is_active' => true,
            ]
        );
    }
}
