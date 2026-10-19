<?php

namespace Tests\Feature;

use App\Mail\OtpMail;
use App\Models\ChallengeAttempt;
use App\Models\Player;
use App\Models\TypingChallenge;
use App\Models\TypingText;
use App\Services\OtpService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class ChallengeAccessTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Mail::fake();
    }

    public function test_new_player_can_request_an_otp(): void
    {
        TypingChallenge::factory()->create();

        $this->postJson(route('challenge.request-otp'), [
            'email' => 'player@example.com', 'username' => 'Player',
        ])->assertOk()->assertJsonPath('success', true)
            ->assertSessionHas('challenge_email', 'player@example.com');

        Mail::assertSent(OtpMail::class);
        $this->assertDatabaseCount('players', 1);
        $this->assertDatabaseCount('email_otps', 1);
    }

    public function test_existing_player_keeps_their_identity_when_requesting_an_otp(): void
    {
        $player = Player::factory()->create();
        TypingChallenge::factory()->create();

        $this->requestOtp($player)->assertOk()->assertJsonPath('success', true)
            ->assertSessionHas('challenge_username', $player->username);

        $this->assertDatabaseCount('players', 1);
        Mail::assertSent(OtpMail::class);
    }

    public function test_deleted_player_can_request_an_otp_without_a_duplicate_email_error(): void
    {
        $player = Player::factory()->create();
        $player->delete();
        TypingChallenge::factory()->create();

        $this->requestOtp($player)->assertOk()->assertJsonPath('success', true);

        $this->assertDatabaseCount('players', 1);
        $this->assertSoftDeleted($player);
        Mail::assertSent(OtpMail::class);
    }

    public function test_deleted_player_is_restored_only_after_verifying_the_otp(): void
    {
        $player = Player::factory()->create();
        $player->delete();
        TypingChallenge::factory()->create();
        $otp = app(OtpService::class)->generate($player->email, 'challenge_access');

        $this->withSession(['challenge_email' => $player->email])
            ->postJson(route('challenge.verify-otp'), ['otp_code' => '000000'])
            ->assertUnprocessable();
        $this->assertSoftDeleted($player);

        $this->postJson(route('challenge.verify-otp'), ['otp_code' => $otp->otp_code])
            ->assertOk()->assertJsonPath('success', true)
            ->assertSessionHas('verified_player_id', $player->id);

        $this->assertFalse($player->fresh()->trashed());
        $this->assertDatabaseCount('players', 1);
    }

    public function test_deleted_blocked_player_cannot_request_an_otp(): void
    {
        $player = Player::factory()->create(['is_blocked' => true]);
        $player->delete();

        $this->requestOtp($player)->assertForbidden()
            ->assertJsonPath('message', 'Your account is not eligible for this challenge.');

        Mail::assertNothingSent();
        $this->assertSoftDeleted($player);
        $this->assertDatabaseCount('email_otps', 0);
    }

    public function test_archiving_a_player_outside_admin_preserves_their_daily_entry_limit(): void
    {
        $player = Player::factory()->create();
        $challenge = TypingChallenge::factory()->create(['require_unique_email' => true]);
        $text = TypingText::factory()->create(['challenge_id' => $challenge->id]);
        ChallengeAttempt::factory()->create([
            'player_id' => $player->id,
            'challenge_id' => $challenge->id,
            'typing_text_id' => $text->id,
            'status' => 'completed',
            'completed_at' => now(),
        ]);
        $player->delete();

        $this->requestOtp($player)->assertForbidden()
            ->assertJsonPath('code', 'email_used_today');

        Mail::assertNothingSent();
        $this->assertSoftDeleted($player);
        $this->assertDatabaseCount('email_otps', 0);
    }

    public function test_fetch_validation_errors_are_returned_as_json(): void
    {
        $this->postJson(route('challenge.request-otp'), ['email' => 'invalid'])
            ->assertUnprocessable()->assertJsonValidationErrors(['email', 'username']);
    }

    public function test_deleted_player_with_a_used_lifetime_attempt_gets_a_clear_rejection(): void
    {
        $player = Player::factory()->create();
        $challenge = TypingChallenge::factory()->create([
            'require_unique_email' => false,
            'max_attempts_per_day' => 1,
        ]);
        $text = TypingText::factory()->create(['challenge_id' => $challenge->id]);
        ChallengeAttempt::factory()->create([
            'player_id' => $player->id,
            'challenge_id' => $challenge->id,
            'typing_text_id' => $text->id,
            'status' => 'completed',
            'completed_at' => now()->subDays(3),
        ]);
        $player->delete();

        $this->requestOtp($player)->assertForbidden()->assertJsonPath(
            'message', 'You have used all your allowed attempts for this challenge.'
        );

        Mail::assertNothingSent();
        $this->assertSoftDeleted($player);
    }

    public function test_player_blocked_after_requesting_an_otp_cannot_restore_their_account(): void
    {
        $player = Player::factory()->create(['is_blocked' => true]);
        $player->delete();
        TypingChallenge::factory()->create();
        $otp = app(OtpService::class)->generate($player->email, 'challenge_access');

        $this->withSession(['challenge_email' => $player->email])
            ->postJson(route('challenge.verify-otp'), ['otp_code' => $otp->otp_code])
            ->assertForbidden()->assertSessionMissing('verified_player_id');

        $this->assertSoftDeleted($player);
    }

    public function test_mobile_rejection_is_returned_as_json_for_fetch_requests(): void
    {
        $this->withHeader('User-Agent', 'Mozilla/5.0 (iPhone) Mobile')
            ->postJson(route('challenge.request-otp'), [
                'email' => 'player@example.com', 'username' => 'Player',
            ])->assertForbidden()->assertJsonPath('code', 'mobile_blocked');

        Mail::assertNothingSent();
    }

    private function requestOtp(Player $player): TestResponse
    {
        return $this->postJson(route('challenge.request-otp'), [
            'email' => $player->email, 'username' => 'New name',
        ]);
    }
}
