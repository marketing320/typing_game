<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChallengeAttempt;
use App\Models\TypingChallenge;
use Illuminate\Http\Request;

class AdminAttemptController extends Controller
{
    public function index(Request $request)
    {
        $query = ChallengeAttempt::with(['player', 'challenge']);

        if ($challengeId = $request->input('challenge_id')) {
            $query->where('challenge_id', $challengeId);
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        if ($playerId = $request->input('player_id')) {
            $query->where('player_id', $playerId);
        }

        if ($date = $request->input('date')) {
            $query->whereDate('created_at', $date);
        }

        if ($minWpm = $request->input('min_wpm')) {
            $query->where('wpm', '>=', $minWpm);
        }

        if ($maxWpm = $request->input('max_wpm')) {
            $query->where('wpm', '<=', $maxWpm);
        }

        $attempts = $query->latest()->paginate(20);
        $challenges = TypingChallenge::all();

        return view('admin.attempts.index', compact('attempts', 'challenges'));
    }
}
