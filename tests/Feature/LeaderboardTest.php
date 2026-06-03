<?php

namespace Tests\Feature;

use App\Models\ChallengeAttempt;
use App\Models\Player;
use App\Models\TypingChallenge;
use App\Models\TypingText;
use App\Services\LeaderboardService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LeaderboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_leaderboard_ranks_by_wpm_then_accuracy_then_duration(): void
    {
        $challenge = TypingChallenge::factory()->create(['status' => 'active']);
        $text = TypingText::factory()->create(['challenge_id' => $challenge->id, 'mode' => 'challenge']);

        // Player A: 80 WPM, 95% accuracy, 30s
        $pA = Player::factory()->create();
        ChallengeAttempt::factory()->create([
            'challenge_id' => $challenge->id, 'player_id' => $pA->id,
            'typing_text_id' => $text->id, 'status' => 'completed',
            'wpm' => 80, 'accuracy' => 95, 'duration_seconds' => 30,
        ]);

        // Player B: 80 WPM, 98% accuracy, 28s (same WPM, higher accuracy wins)
        $pB = Player::factory()->create();
        ChallengeAttempt::factory()->create([
            'challenge_id' => $challenge->id, 'player_id' => $pB->id,
            'typing_text_id' => $text->id, 'status' => 'completed',
            'wpm' => 80, 'accuracy' => 98, 'duration_seconds' => 28,
        ]);

        // Player C: 70 WPM (lower WPM, ranks last)
        $pC = Player::factory()->create();
        ChallengeAttempt::factory()->create([
            'challenge_id' => $challenge->id, 'player_id' => $pC->id,
            'typing_text_id' => $text->id, 'status' => 'completed',
            'wpm' => 70, 'accuracy' => 99, 'duration_seconds' => 25,
        ]);

        $service = new LeaderboardService();
        $entries = $service->getForChallenge($challenge);

        $this->assertEquals(1, $entries[0]['rank']);
        $this->assertEquals($pB->username, $entries[0]['username']); // B wins (same WPM, higher acc)
        $this->assertEquals(2, $entries[1]['rank']);
        $this->assertEquals($pA->username, $entries[1]['username']);
        $this->assertEquals(3, $entries[2]['rank']);
        $this->assertEquals($pC->username, $entries[2]['username']);
    }
}
