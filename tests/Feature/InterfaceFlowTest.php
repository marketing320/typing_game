<?php

namespace Tests\Feature;

use App\Models\ChallengeAttempt;
use App\Models\Player;
use App\Models\SystemSetting;
use App\Models\TypingChallenge;
use App\Models\TypingText;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InterfaceFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_pages_keep_the_existing_mode_links_and_controls(): void
    {
        $challenge = TypingChallenge::factory()->create();
        TypingText::factory()->create(['mode' => 'rehearsal']);

        $this->get(route('home'))->assertOk()
            ->assertSee(route('rehearsal.index'), false)
            ->assertSee(route('challenge.access'), false)
            ->assertSee($challenge->title);
        $this->get(route('rehearsal.index'))->assertOk()
            ->assertSee('id="hidden-input"', false)
            ->assertSee('id="result-panel"', false)
            ->assertSee(route('rehearsal.submit'), false);
        $this->get(route('challenge.access'))->assertOk()
            ->assertSee('id="access-form"', false)
            ->assertSee('id="input-email"', false)
            ->assertSee(route('challenge.request-otp'), false);
        $this->get(route('leaderboard.index'))->assertOk()
            ->assertSee('id="leaderboard-body"', false)
            ->assertSee('id="score-info-modal"', false)
            ->assertSee(route('leaderboard.data'), false);
    }

    public function test_verification_game_and_results_render_with_the_existing_session(): void
    {
        $player = Player::factory()->create();
        $challenge = TypingChallenge::factory()->create();
        $text = TypingText::factory()->create(['challenge_id' => $challenge->id]);

        $this->withSession(['challenge_email' => $player->email, 'verified_player_id' => $player->id]);
        $otp = $this->get(route('challenge.otp'))->assertOk()
            ->assertSee($player->email)
            ->assertSee(route('challenge.verify-otp'), false);
        $this->assertSame(6, substr_count($otp->getContent(), 'class="otp-digit '));

        $this->get(route('challenge.play'))->assertOk()
            ->assertSee('id="profile-modal"', false)
            ->assertSee('id="start-btn"', false)
            ->assertSee('id="hidden-input"', false)
            ->assertSee(route('challenge.save-profile'), false)
            ->assertSee(route('challenge.start'), false)
            ->assertSee(route('challenge.submit'), false);

        $attempt = ChallengeAttempt::factory()->create([
            'player_id' => $player->id,
            'challenge_id' => $challenge->id,
            'typing_text_id' => $text->id,
        ]);
        $this->get(route('challenge.result', $attempt))->assertOk()
            ->assertSee($player->username)
            ->assertSee(route('leaderboard.index'), false);
    }

    public function test_admin_workspace_pages_and_forms_still_render(): void
    {
        $this->get(route('admin.login'))->assertOk()
            ->assertSee('name="email"', false)
            ->assertSee('name="password"', false)
            ->assertSee(route('admin.login.post'), false);

        foreach (['site_name', 'otp_expiry_minutes', 'otp_max_attempts', 'maintenance_mode', 'referral_source_options', 'mobile_block_enabled', 'mobile_block_message'] as $key) {
            SystemSetting::create(['setting_key' => $key, 'setting_value' => '']);
        }

        $this->withSession(['admin_id' => 1, 'admin_name' => 'Admin']);
        foreach (['dashboard', 'challenges.index', 'challenges.create', 'typing-texts.index', 'typing-texts.create', 'geofence.index', 'geofence.create', 'players.index', 'attempts.index', 'leaderboard.index', 'settings.index'] as $page) {
            $this->get(route('admin.'.$page))->assertOk()
                ->assertSee(route('admin.logout'), false);
        }
    }

    public function test_mobile_challenge_restriction_and_open_practice_are_preserved(): void
    {
        $this->withHeader('User-Agent', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_0 like Mac OS X) Mobile');
        $this->get(route('challenge.access'))->assertForbidden()
            ->assertSee(route('rehearsal.index'), false);
        $this->get(route('rehearsal.index'))->assertOk();
    }
}
