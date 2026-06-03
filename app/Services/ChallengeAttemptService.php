<?php

namespace App\Services;

use App\Models\ChallengeAttempt;
use App\Models\Player;
use App\Models\TypingChallenge;
use App\Models\TypingText;
use Carbon\Carbon;

class ChallengeAttemptService
{
    public function canAttempt(Player $player, TypingChallenge $challenge): array
    {
        if ($player->is_blocked) {
            return ['allowed' => false, 'reason' => 'Your account has been blocked.'];
        }

        if (!$challenge->isActive()) {
            return ['allowed' => false, 'reason' => 'This challenge is not currently active.'];
        }

        $totalAttempts = ChallengeAttempt::where('player_id', $player->id)
            ->where('challenge_id', $challenge->id)
            ->count();

        if ($totalAttempts === 0) {
            return ['allowed' => true];
        }

        // Lifetime cap — checked first so allow_retry_next_day cannot bypass it
        if ($totalAttempts >= $challenge->max_attempts_per_day) {
            return ['allowed' => false, 'reason' => 'You have used all your allowed attempts for this challenge.'];
        }

        // Under the lifetime cap but has prior attempts — retry requires allow_retry_next_day
        if (!$challenge->allow_retry_next_day) {
            return ['allowed' => false, 'reason' => 'You have already used your attempt for this challenge.'];
        }

        // Retry is allowed — block if they already attempted today
        $todayAttempts = ChallengeAttempt::where('player_id', $player->id)
            ->where('challenge_id', $challenge->id)
            ->whereDate('started_at', today())
            ->count();

        if ($todayAttempts > 0) {
            return ['allowed' => false, 'reason' => 'You have already attempted this challenge today. Try again tomorrow.'];
        }

        return ['allowed' => true];
    }

    public function start(Player $player, TypingChallenge $challenge, TypingText $text, array $geo = []): ChallengeAttempt
    {
        return ChallengeAttempt::create([
            'challenge_id' => $challenge->id,
            'player_id' => $player->id,
            'typing_text_id' => $text->id,
            'status' => 'started',
            'started_at' => now(),
            'remaining_lives' => 1,
            'ip_address' => $geo['ip_address'] ?? null,
            'user_agent' => $geo['user_agent'] ?? null,
            'latitude' => $geo['latitude'] ?? null,
            'longitude' => $geo['longitude'] ?? null,
            'distance_from_allowed_meters' => $geo['distance'] ?? null,
            'is_within_geofence' => $geo['is_within_geofence'] ?? null,
            'device_fingerprint' => $geo['device_fingerprint'] ?? null,
        ]);
    }

    public function submit(ChallengeAttempt $attempt, array $data, array $scores): ChallengeAttempt
    {
        $attempt->update(array_merge($scores, [
            'status' => 'completed',
            'user_input' => $data['user_input'] ?? null,
            'completed_at' => now(),
            'duration_seconds' => $data['duration_seconds'] ?? null,
        ]));

        return $attempt->fresh();
    }

    public function markFailed(ChallengeAttempt $attempt): void
    {
        if (in_array($attempt->status, ['started'])) {
            $attempt->update(['status' => 'failed']);
        }
    }
}
