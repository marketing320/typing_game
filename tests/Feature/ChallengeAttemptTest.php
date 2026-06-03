<?php

namespace Tests\Feature;

use App\Models\ChallengeAttempt;
use App\Models\Player;
use App\Models\TypingChallenge;
use App\Models\TypingText;
use App\Services\ChallengeAttemptService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ChallengeAttemptTest extends TestCase
{
    use RefreshDatabase;

    private ChallengeAttemptService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new ChallengeAttemptService();
    }

    public function test_player_can_attempt_with_no_prior_attempts(): void
    {
        $player = Player::factory()->create();
        $challenge = TypingChallenge::factory()->create(['status' => 'active']);

        $result = $this->service->canAttempt($player, $challenge);
        $this->assertTrue($result['allowed']);
    }

    public function test_player_cannot_retry_when_retry_disabled(): void
    {
        $player = Player::factory()->create();
        $challenge = TypingChallenge::factory()->create([
            'status' => 'active',
            'allow_retry_next_day' => false,
        ]);
        $text = TypingText::factory()->create(['challenge_id' => $challenge->id, 'mode' => 'challenge']);

        ChallengeAttempt::factory()->create([
            'player_id' => $player->id,
            'challenge_id' => $challenge->id,
            'typing_text_id' => $text->id,
            'status' => 'started',
            'started_at' => now(),
        ]);

        $result = $this->service->canAttempt($player, $challenge);
        $this->assertFalse($result['allowed']);
    }

    public function test_player_can_retry_next_day_when_allowed(): void
    {
        $player = Player::factory()->create();
        $challenge = TypingChallenge::factory()->create([
            'status' => 'active',
            'allow_retry_next_day' => true,
        ]);
        $text = TypingText::factory()->create(['challenge_id' => $challenge->id, 'mode' => 'challenge']);

        // Yesterday's attempt
        ChallengeAttempt::factory()->create([
            'player_id' => $player->id,
            'challenge_id' => $challenge->id,
            'typing_text_id' => $text->id,
            'status' => 'completed',
            'started_at' => Carbon::yesterday(),
        ]);

        $result = $this->service->canAttempt($player, $challenge);
        $this->assertTrue($result['allowed']);
    }

    public function test_blocked_player_cannot_attempt(): void
    {
        $player = Player::factory()->create(['is_blocked' => true]);
        $challenge = TypingChallenge::factory()->create(['status' => 'active']);

        $result = $this->service->canAttempt($player, $challenge);
        $this->assertFalse($result['allowed']);
    }
}
