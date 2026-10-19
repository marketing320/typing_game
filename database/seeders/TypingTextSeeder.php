<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\TypingChallenge;
use App\Models\TypingText;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TypingTextSeeder extends Seeder
{
    public function run(): void
    {
        $passages = require database_path('data/typing-passages.php');
        $adminId = Admin::query()->value('id');

        DB::transaction(function () use ($passages, $adminId) {
            // Keep the original row for any recorded practice results.
            TypingText::where('mode', 'rehearsal')
                ->whereNull('challenge_id')
                ->where('title', 'Warm-Up Practice')
                ->where('content', 'The little monkey jumps from branch to branch while collecting bananas in the bright morning sun.')
                ->update(['is_active' => false]);

            foreach ($passages['practice'] as $passage) {
                TypingText::firstOrCreate([
                    'mode' => 'rehearsal',
                    'challenge_id' => null,
                    'title' => $passage['title'],
                ], $passage + [
                    'language' => 'en',
                    'is_active' => true,
                    'created_by' => $adminId,
                ]);
            }

            foreach (TypingChallenge::where('title', 'WordSprint Grand Challenge')->get() as $challenge) {
                $activeText = $challenge->activeText();

                // Preserve custom passages and any text deliberately disabled by an admin.
                if ($activeText && $activeText->content !== 'Speed and focus are the keys to winning this typing challenge. Keep your eyes on the words and type with confidence.') {
                    continue;
                }
                if (! $activeText && $challenge->typingTexts()->where('mode', 'challenge')->exists()) {
                    continue;
                }

                TypingText::firstOrCreate([
                    'challenge_id' => $challenge->id,
                    'mode' => 'challenge',
                    'title' => $passages['challenge']['title'],
                ], $passages['challenge'] + [
                    'language' => 'en',
                    'is_active' => true,
                    'created_by' => $adminId,
                ]);

                // New attempts use the current passage; previous attempts keep their text.
                $activeText?->update(['is_active' => false]);
            }
        });
    }
}
