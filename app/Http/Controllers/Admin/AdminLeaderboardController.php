<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TypingChallenge;
use App\Services\LeaderboardService;
use Illuminate\Http\Request;

class AdminLeaderboardController extends Controller
{
    public function __construct(private LeaderboardService $leaderboard) {}

    public function index(Request $request)
    {
        $challenges = TypingChallenge::whereIn('status', ['active', 'ended'])->latest()->get();

        $challengeId = $request->input('challenge_id');
        $selectedChallenge = $challengeId
            ? TypingChallenge::find($challengeId)
            : $challenges->first();

        $entries = $selectedChallenge
            ? $this->leaderboard->getForChallenge($selectedChallenge, 100)
            : collect();

        return view('admin.leaderboard.index', compact('entries', 'challenges', 'selectedChallenge'));
    }
}
