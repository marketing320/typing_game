<?php

namespace Tests\Feature;

use App\Models\ChallengeAttempt;
use App\Models\Player;
use App\Models\TypingChallenge;
use App\Models\TypingText;
use Database\Seeders\ChallengeSeeder;
use Database\Seeders\TypingTextSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TypingPassagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_fresh_seed_and_repeat_seed_provide_a_passage_library_without_duplicates(): void
    {
        $this->seed(ChallengeSeeder::class);
        $this->seed(ChallengeSeeder::class);

        $this->assertDatabaseCount('typing_challenges', 1);
        $this->assertSame(15, TypingText::where('mode', 'rehearsal')->where('is_active', true)->count());
        $this->assertSame(1, TypingText::where('mode', 'challenge')->where('is_active', true)->count());
        $this->assertDatabaseCount('typing_texts', 16);
    }

    public function test_upgrade_keeps_the_original_text_and_scores_for_previous_attempts(): void
    {
        $challenge = TypingChallenge::factory()->create(['title' => 'WordSprint Grand Challenge']);
        $original = 'Speed and focus are the keys to winning this typing challenge. Keep your eyes on the words and type with confidence.';
        $legacy = TypingText::factory()->create([
            'challenge_id' => $challenge->id, 'content' => $original,
        ]);
        $attempt = ChallengeAttempt::factory()->create([
            'player_id' => Player::factory()->create()->id,
            'challenge_id' => $challenge->id,
            'typing_text_id' => $legacy->id,
        ]);
        $practice = TypingText::factory()->create([
            'mode' => 'rehearsal', 'title' => 'Warm-Up Practice',
            'content' => 'The little monkey jumps from branch to branch while collecting bananas in the bright morning sun.',
        ]);

        $this->seed(TypingTextSeeder::class);
        $this->seed(TypingTextSeeder::class);

        $this->assertFalse($legacy->fresh()->is_active);
        $this->assertFalse($practice->fresh()->is_active);
        $this->assertSame($original, $attempt->fresh()->typingText->content);
        $this->assertSame(40.0, $attempt->fresh()->wpm);
        $this->assertNotSame($legacy->id, $challenge->activeText()->id);
        $this->assertSame(1, $challenge->typingTexts()->where('is_active', true)->count());
        $this->assertDatabaseCount('typing_texts', 18);
    }

    public function test_repeat_seed_preserves_admin_edits_and_disabled_passages(): void
    {
        $this->seed(ChallengeSeeder::class);
        $practice = TypingText::where('mode', 'rehearsal')->firstOrFail();
        $practice->update(['content' => 'A custom passage written by the admin.', 'is_active' => false]);
        $challenge = TypingChallenge::firstOrFail();
        $text = $challenge->activeText();
        $text->update(['content' => 'The custom official challenge passage.']);

        $this->seed(TypingTextSeeder::class);

        $this->assertSame('A custom passage written by the admin.', $practice->fresh()->content);
        $this->assertFalse($practice->fresh()->is_active);
        $this->assertSame('The custom official challenge passage.', $challenge->activeText()->content);
        $this->assertDatabaseCount('typing_texts', 16);
    }

    public function test_new_passage_excludes_the_previous_text_and_inactive_texts(): void
    {
        $previous = TypingText::factory()->create(['mode' => 'rehearsal']);
        $next = TypingText::factory()->create(['mode' => 'rehearsal']);
        TypingText::factory()->create(['mode' => 'rehearsal', 'is_active' => false]);
        TypingText::factory()->create(['mode' => 'challenge']);

        $this->get(route('rehearsal.index', ['previous' => $previous->id]))
            ->assertOk()->assertViewHas('text', fn ($text) => $text->id === $next->id)
            ->assertSee('New passage')
            ->assertSee(route('rehearsal.index', ['previous' => $next->id]), false);
    }

    public function test_new_passage_still_works_with_only_one_active_practice_text(): void
    {
        $only = TypingText::factory()->create(['mode' => 'rehearsal']);

        $this->get(route('rehearsal.index', ['previous' => $only->id]))
            ->assertOk()->assertViewHas('text', fn ($text) => $text->id === $only->id);
    }

    public function test_practice_page_handles_an_empty_library(): void
    {
        $this->get(route('rehearsal.index'))->assertOk()->assertViewHas('text', null);
    }

    public function test_players_share_the_challenge_passage_and_it_scores_correctly(): void
    {
        $this->seed(ChallengeSeeder::class);
        $challenge = TypingChallenge::firstOrFail();
        $text = $challenge->activeText();
        $players = Player::factory()->count(2)->create();
        foreach ($players as $player) {
            $this->withSession(['verified_player_id' => $player->id])
                ->get(route('challenge.play'))->assertOk()
                ->assertViewHas('text', fn ($shared) => $shared->id === $text->id);
        }

        $this->postJson(route('challenge.start'))->assertOk();
        $attempt = ChallengeAttempt::where('player_id', $players[1]->id)->firstOrFail();
        $this->assertSame($text->id, $attempt->typing_text_id);
        $this->postJson(route('challenge.submit'), [
            'attempt_id' => $attempt->id,
            'user_input' => $text->content,
            'duration_seconds' => 120,
        ])->assertOk()->assertJsonPath('accuracy', 100);
        $this->assertSame(40, $attempt->fresh()->total_words);
        $this->assertSame('completed', $attempt->fresh()->status);
    }
}
