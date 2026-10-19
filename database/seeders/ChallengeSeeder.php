<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\TypingChallenge;
use Illuminate\Database\Seeder;

class ChallengeSeeder extends Seeder
{
    public function run(): void
    {
        $admin = Admin::where('email', 'admin@typingmonkey.local')->first();

        TypingChallenge::firstOrCreate(
            ['title' => 'WordSprint Grand Challenge'],
            [
                'description' => 'Welcome to the official WordSprint Grand Challenge! Show off your speed and accuracy.',
                'status' => 'active',
                'allow_retry_next_day' => false,
                'max_attempts_per_day' => 1,
                'require_geofence' => false,
                'created_by' => $admin?->id,
            ]
        );

        $this->call(TypingTextSeeder::class);
    }
}
