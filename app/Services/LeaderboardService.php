<?php

namespace App\Services;

use App\Models\ChallengeAttempt;
use App\Models\TypingChallenge;
use Illuminate\Support\Collection;

class LeaderboardService
{
    public function getForChallenge(TypingChallenge $challenge, int $limit = 50): Collection
    {
        $attempts = ChallengeAttempt::with('player')
            ->where('challenge_id', $challenge->id)
            ->where('status', 'completed')
            ->whereHas('player')
            ->orderByRaw('(wpm * accuracy) DESC')
            ->orderByDesc('accuracy')
            ->orderBy('duration_seconds')
            ->get()
            // One row per email — keep each player's best attempt (first in score order).
            ->unique(fn ($attempt) => $attempt->player->email)
            ->take($limit)
            ->values();

        return $this->rankAttempts($attempts, fn($attempt) => [
            'username'         => $attempt->player->username,
            'wpm'              => $attempt->wpm,
            'accuracy'         => $attempt->accuracy,
            'score'            => round($attempt->wpm * $attempt->accuracy / 100, 2),
            'duration_seconds' => $attempt->duration_seconds,
            'completed_at'     => $attempt->completed_at,
        ]);
    }

    public function getGlobal(int $limit = 50): Collection
    {
        $attempts = ChallengeAttempt::with(['player', 'challenge'])
            ->where('status', 'completed')
            ->whereHas('player')
            ->orderByRaw('(wpm * accuracy) DESC')
            ->orderByDesc('accuracy')
            ->orderBy('duration_seconds')
            ->get()
            // One row per email — keep each player's best attempt (first in score order).
            ->unique(fn ($attempt) => $attempt->player->email)
            ->take($limit)
            ->values();

        return $this->rankAttempts($attempts, fn($attempt) => [
            'username'         => $attempt->player->username,
            'challenge'        => $attempt->challenge->title,
            'wpm'              => $attempt->wpm,
            'accuracy'         => $attempt->accuracy,
            'score'            => round($attempt->wpm * $attempt->accuracy / 100, 2),
            'duration_seconds' => $attempt->duration_seconds,
            'completed_at'     => $attempt->completed_at,
        ]);
    }

    /**
     * Assign Olympic-style ranks (1-2-2-4) to a sorted collection of attempts.
     * Players with identical score, accuracy, and duration share the same rank.
     * The next distinct player's rank equals their 1-based position in the list.
     */
    private function rankAttempts(Collection $attempts, callable $mapper): Collection
    {
        $rank   = 1;
        $prev   = null;
        $result = collect();

        foreach ($attempts as $index => $attempt) {
            if ($prev !== null
                && round($attempt->wpm * $attempt->accuracy / 100, 2) == round($prev->wpm * $prev->accuracy / 100, 2)
                && $attempt->accuracy         == $prev->accuracy
                && $attempt->duration_seconds == $prev->duration_seconds
            ) {
                // Perfect tie — keep the same rank
            } else {
                $rank = $index + 1;
            }

            $result->push(array_merge(['rank' => $rank], $mapper($attempt)));
            $prev = $attempt;
        }

        return $result;
    }
}
