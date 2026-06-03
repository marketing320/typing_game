<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\TypingChallenge;
use App\Models\TypingText;
use Illuminate\Database\Seeder;

class ChallengeSeeder extends Seeder
{
    public function run(): void
    {
        $admin = Admin::where('email', 'admin@typingmonkey.local')->first();

        $challenge = TypingChallenge::firstOrCreate(
            ['title' => 'Typing Monkey Grand Challenge'],
            [
                'description' => 'Welcome to the official Typing Monkey Grand Challenge! Show off your speed and accuracy.',
                'status' => 'active',
                'allow_retry_next_day' => false,
                'max_attempts_per_day' => 1,
                'require_geofence' => false,
                'created_by' => $admin?->id,
            ]
        );

        // Challenge text
        TypingText::firstOrCreate(
            [
                'challenge_id' => $challenge->id,
                'mode' => 'challenge',
            ],
            [
                'title' => 'Grand Challenge Text',
                'content' => 'Speed and focus are the keys to winning this typing challenge. Keep your eyes on the words and type with confidence.',
                'language' => 'en',
                'difficulty' => 'medium',
                'is_active' => true,
                'created_by' => $admin?->id,
            ]
        );

        // Rehearsal text
        TypingText::firstOrCreate(
            ['mode' => 'rehearsal', 'challenge_id' => null],
            [
                'title' => 'Warm-Up Practice',
                'content' => 'The little monkey jumps from branch to branch while collecting bananas in the bright morning sun.',
                'language' => 'en',
                'difficulty' => 'easy',
                'is_active' => true,
                'created_by' => $admin?->id,
            ]
        );
    }
}
