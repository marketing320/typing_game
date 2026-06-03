<?php

namespace Database\Seeders;

use App\Models\ChallengeAttempt;
use App\Models\Player;
use App\Models\TypingChallenge;
use App\Models\TypingText;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class LeaderboardSeeder extends Seeder
{
    private array $prefixes = ['Fast', 'Quick', 'Turbo', 'Swift', 'Flash', 'Nitro', 'Rapid', 'Blazing', 'Neon', 'Cyber'];
    private array $suffixes = ['Monkey', 'Typer', 'Keys', 'Pro', 'Master', 'Wizard', 'Ninja', 'Speeder', 'Fingers', 'Hawk'];

    public function run(): void
    {
        $challenge = TypingChallenge::where('status', 'active')->first();

        if (!$challenge) {
            $this->command->warn('No active challenge found — run ChallengeSeeder first.');
            return;
        }

        $text = TypingText::where('challenge_id', $challenge->id)
            ->where('mode', 'challenge')
            ->first();

        if (!$text) {
            $this->command->warn('No challenge text found.');
            return;
        }

        $totalChars = mb_strlen($text->content);
        $totalWords = count(preg_split('/\s+/', trim($text->content)));

        // Four deliberate ties — two pairs — to demonstrate Olympic shared ranking
        $entries = [
            ['wpm' => 75.00, 'accuracy' => 97.50],  // } tied pair #1
            ['wpm' => 75.00, 'accuracy' => 97.50],  // }
            ['wpm' => 90.00, 'accuracy' => 100.00], // } tied pair #2
            ['wpm' => 90.00, 'accuracy' => 100.00], // }
        ];

        // 46 random entries spread across a realistic WPM range
        $wpmPool = array_merge(
            $this->range(20, 40, 10),   // slow
            $this->range(41, 70, 20),   // average
            $this->range(71, 100, 12),  // good
            $this->range(101, 130, 4)   // fast
        );

        foreach ($wpmPool as $wpm) {
            $entries[] = [
                'wpm'      => $wpm + (rand(0, 99) / 100),
                'accuracy' => min(100.0, 80 + rand(0, 20) + rand(0, 99) / 100),
            ];
        }

        foreach ($entries as $i => $entry) {
            $wpm          = round($entry['wpm'], 2);
            $accuracy     = round(min($entry['accuracy'], 100.0), 2);
            $correctChars = (int) round($totalChars * $accuracy / 100);
            $wrongChars   = $totalChars - $correctChars;

            // Derive duration from WPM formula: WPM = (correctChars/5) / (duration_mins)
            $durationSeconds = $wpm > 0
                ? round(($correctChars / 5) / ($wpm / 60), 3)
                : 60.000;

            $correctWords = (int) round($totalWords * $accuracy / 100);
            $wrongWords   = $totalWords - $correctWords;

            $completedAt = Carbon::now()->subDays(rand(0, 30))->subMinutes(rand(0, 1440));
            $startedAt   = (clone $completedAt)->subSeconds((int) $durationSeconds);

            $username = $this->username($i);
            $email    = 'seed.player' . ($i + 1) . '@leaderboard.local';

            $player = Player::firstOrCreate(
                ['email' => $email],
                [
                    'username'           => $username,
                    'email_verified_at'  => $completedAt,
                ]
            );

            ChallengeAttempt::updateOrCreate(
                ['player_id' => $player->id, 'challenge_id' => $challenge->id],
                [
                    'typing_text_id'    => $text->id,
                    'status'            => 'completed',
                    'started_at'        => $startedAt,
                    'completed_at'      => $completedAt,
                    'duration_seconds'  => $durationSeconds,
                    'total_words'       => $totalWords,
                    'correct_words'     => $correctWords,
                    'wrong_words'       => $wrongWords,
                    'total_characters'  => $totalChars,
                    'correct_characters' => $correctChars,
                    'wrong_characters'  => $wrongChars,
                    'wpm'               => $wpm,
                    'accuracy'          => $accuracy,
                    'mistake_count'     => (int) round($wrongChars * 0.6),
                    'remaining_lives'   => 1,
                    'is_within_geofence' => true,
                ]
            );
        }

        $this->command->info('LeaderboardSeeder: ' . count($entries) . ' entries seeded.');
    }

    private function username(int $i): string
    {
        $prefix = $this->prefixes[$i % count($this->prefixes)];
        $suffix = $this->suffixes[(int) ($i / count($this->prefixes)) % count($this->suffixes)];
        return $prefix . $suffix . ($i + 1);
    }

    /** Return $count random integers in [$min, $max]. */
    private function range(int $min, int $max, int $count): array
    {
        $out = [];
        for ($i = 0; $i < $count; $i++) {
            $out[] = rand($min, $max);
        }
        return $out;
    }
}
