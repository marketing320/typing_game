<?php

namespace App\Http\Controllers;

use App\Models\TypingChallenge;
use App\Services\LeaderboardService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class LeaderboardController extends Controller
{
    public function __construct(private LeaderboardService $leaderboard) {}

    public function index()
    {
        $challenges = TypingChallenge::whereIn('status', ['active', 'ended'])
            ->orderByDesc('created_at')
            ->get();

        $activeChallenge = $challenges->where('status', 'active')->first()
            ?? $challenges->first();

        $entries = $this->cachedEntries($activeChallenge);

        return view('leaderboard.index', compact('entries', 'challenges', 'activeChallenge'));
    }

    public function data(): JsonResponse
    {
        $challenges = TypingChallenge::whereIn('status', ['active', 'ended'])
            ->orderByDesc('created_at')
            ->get();

        $activeChallenge = $challenges->where('status', 'active')->first()
            ?? $challenges->first();

        $entries = $this->cachedEntries($activeChallenge);

        return response()->json([
            'entries'   => $entries->values(),
            'challenge' => $activeChallenge ? [
                'title'  => $activeChallenge->title,
                'status' => $activeChallenge->status,
            ] : null,
        ]);
    }

    /**
     * Ranked leaderboard entries, cached for a few seconds so the high-frequency
     * poll endpoint doesn't recompute the JOIN + ranking on every request.
     */
    private function cachedEntries(?TypingChallenge $challenge)
    {
        if (!$challenge) {
            return collect();
        }

        return Cache::remember(
            "leaderboard:challenge:{$challenge->id}",
            3,
            fn () => $this->leaderboard->getForChallenge($challenge)->values()
        );
    }
}
