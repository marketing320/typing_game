<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChallengeAttempt;
use App\Models\Player;
use App\Models\TypingChallenge;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_players' => Player::count(),
            'total_attempts' => ChallengeAttempt::count(),
            'avg_wpm' => round(ChallengeAttempt::where('status', 'completed')->avg('wpm') ?? 0, 1),
            'highest_wpm' => ChallengeAttempt::where('status', 'completed')->max('wpm') ?? 0,
            'today_attempts' => ChallengeAttempt::whereDate('created_at', today())->count(),
            'active_challenge' => TypingChallenge::where('status', 'active')->latest()->first(),
        ];

        $recentAttempts = ChallengeAttempt::with(['player', 'challenge'])
            ->latest()
            ->limit(10)
            ->get();

        return view('admin.dashboard', compact('stats', 'recentAttempts'));
    }
}
