<?php

namespace App\Http\Controllers;

use App\Models\TypingChallenge;
use App\Services\LeaderboardService;
use Illuminate\Http\JsonResponse;

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

        $entries = $activeChallenge
            ? $this->leaderboard->getForChallenge($activeChallenge)
            : collect();

        return view('leaderboard.index', compact('entries', 'challenges', 'activeChallenge'));
    }

    public function data(): JsonResponse
    {
        $challenges = TypingChallenge::whereIn('status', ['active', 'ended'])
            ->orderByDesc('created_at')
            ->get();

        $activeChallenge = $challenges->where('status', 'active')->first()
            ?? $challenges->first();

        $entries = $activeChallenge
            ? $this->leaderboard->getForChallenge($activeChallenge)
            : collect();

        return response()->json([
            'entries'   => $entries->values(),
            'challenge' => $activeChallenge ? [
                'title'  => $activeChallenge->title,
                'status' => $activeChallenge->status,
            ] : null,
        ]);
    }
}
