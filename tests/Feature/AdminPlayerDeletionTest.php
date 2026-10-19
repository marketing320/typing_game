<?php

namespace Tests\Feature;

use App\Models\ChallengeAttempt;
use App\Models\EmailOtp;
use App\Models\Player;
use App\Models\TypingChallenge;
use App\Models\TypingText;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class AdminPlayerDeletionTest extends TestCase
{
    use RefreshDatabase;

    public function test_single_deletion_removes_all_attempts_and_refreshes_the_leaderboard(): void
    {
        $player = Player::factory()->create();
        $other = Player::factory()->create();
        $challenge = TypingChallenge::factory()->create();
        $pastChallenge = TypingChallenge::factory()->create(['status' => 'ended']);
        foreach (['started', 'completed', 'failed', 'disqualified'] as $status) {
            $this->attempt($player, $challenge, $status);
        }
        $this->attempt($player, $pastChallenge);
        $untouched = $this->attempt($other, $challenge);

        $this->getJson(route('leaderboard.data'))->assertJsonCount(2, 'entries');

        $this->withSession(['admin_id' => 1])
            ->delete(route('admin.players.destroy', $player))
            ->assertRedirect(route('admin.players.index'));

        $this->assertDatabaseMissing('players', ['id' => $player->id]);
        $this->assertDatabaseMissing('challenge_attempts', ['player_id' => $player->id]);
        $this->assertDatabaseHas('challenge_attempts', ['id' => $untouched->id]);
        $this->assertNotSoftDeleted($other);
        $this->getJson(route('leaderboard.data'))->assertJsonCount(1, 'entries')
            ->assertJsonPath('entries.0.username', $other->username);
    }

    public function test_bulk_deletion_clears_only_the_selected_players_attempts(): void
    {
        $players = Player::factory()->count(3)->create();
        $challenge = TypingChallenge::factory()->create();
        foreach ($players as $player) {
            $this->attempt($player, $challenge);
        }

        $this->withSession(['admin_id' => 1])
            ->post(route('admin.players.bulk-destroy'), [
                'ids' => [$players[0]->id, $players[1]->id, $players[0]->id, 999999],
            ])->assertRedirect(route('admin.players.index'))
            ->assertSessionHas('success', '2 player(s) permanently deleted and their attempts reset.');

        foreach ($players->take(2) as $player) {
            $this->assertDatabaseMissing('players', ['id' => $player->id]);
            $this->assertDatabaseMissing('challenge_attempts', ['player_id' => $player->id]);
        }
        $this->assertNotSoftDeleted($players[2]);
        $this->assertDatabaseCount('challenge_attempts', 1);
    }

    #[DataProvider('entryModes')]
    public function test_deleted_player_can_verify_and_start_a_fresh_attempt(bool $uniqueEmail): void
    {
        Mail::fake();
        $player = Player::factory()->create();
        $challenge = TypingChallenge::factory()->create([
            'require_unique_email' => $uniqueEmail,
            'max_attempts_per_day' => 1,
        ]);
        $oldAttempt = $this->attempt($player, $challenge);
        $payload = ['email' => $player->email, 'username' => $player->username];

        $this->postJson(route('challenge.request-otp'), $payload)->assertForbidden();
        $this->withSession(['admin_id' => 1])
            ->delete(route('admin.players.destroy', $player))->assertRedirect();
        $this->postJson(route('challenge.request-otp'), $payload)
            ->assertOk()->assertJsonPath('success', true);
        $otp = EmailOtp::where('email', $player->email)->latest()->firstOrFail();
        $this->postJson(route('challenge.verify-otp'), ['otp_code' => $otp->otp_code])
            ->assertOk()->assertJsonPath('success', true);
        $this->postJson(route('challenge.start'))->assertOk()->assertJsonPath('success', true);

        $this->assertDatabaseCount('players', 1);
        $this->assertDatabaseMissing('players', ['id' => $player->id]);
        $newPlayer = Player::where('email', $player->email)->firstOrFail();
        $this->assertNotSame($player->id, $newPlayer->id);
        $this->assertDatabaseCount('challenge_attempts', 1);
        $this->assertDatabaseMissing('challenge_attempts', ['id' => $oldAttempt->id]);
        $this->assertDatabaseHas('challenge_attempts', [
            'player_id' => $newPlayer->id, 'challenge_id' => $challenge->id, 'status' => 'started',
        ]);
        $this->getJson(route('leaderboard.data'))->assertJsonCount(0, 'entries');
    }

    public static function entryModes(): array
    {
        return ['daily email limit' => [true], 'lifetime limit' => [false]];
    }

    public function test_unauthenticated_deletion_keeps_players_and_attempts(): void
    {
        $player = Player::factory()->create();
        $this->attempt($player, TypingChallenge::factory()->create());

        $this->delete(route('admin.players.destroy', $player))
            ->assertRedirect(route('admin.login'));
        $this->post(route('admin.players.bulk-destroy'), ['ids' => [$player->id]])
            ->assertRedirect(route('admin.login'));

        $this->assertNotSoftDeleted($player);
        $this->assertDatabaseCount('challenge_attempts', 1);
    }

    public function test_empty_bulk_selection_keeps_all_attempts(): void
    {
        $player = Player::factory()->create();
        $this->attempt($player, TypingChallenge::factory()->create());

        $this->withSession(['admin_id' => 1])
            ->post(route('admin.players.bulk-destroy'), ['ids' => []])
            ->assertSessionHas('error', 'No players selected.');

        $this->assertNotSoftDeleted($player);
        $this->assertDatabaseCount('challenge_attempts', 1);
    }

    private function attempt(Player $player, TypingChallenge $challenge, string $status = 'completed'): ChallengeAttempt
    {
        $text = TypingText::factory()->create([
            'challenge_id' => $challenge->id, 'mode' => 'challenge', 'is_active' => true,
        ]);

        return ChallengeAttempt::factory()->create([
            'player_id' => $player->id,
            'challenge_id' => $challenge->id,
            'typing_text_id' => $text->id,
            'status' => $status,
        ]);
    }
}
