<?php

namespace App\Http\Controllers;

use App\Models\ChallengeAttempt;
use App\Models\Player;
use App\Models\SystemSetting;
use App\Models\TypingChallenge;
use App\Services\ChallengeAttemptService;
use App\Services\GeofenceService;
use App\Services\ScoringService;
use Illuminate\Http\Request;

class ChallengeGameController extends Controller
{
    public function __construct(
        private ChallengeAttemptService $attemptService,
        private ScoringService $scoring,
        private GeofenceService $geofence,
    ) {}

    public function play()
    {
        $playerId = session('verified_player_id');

        if (!$playerId) {
            return redirect()->route('challenge.access');
        }

        $player = Player::findOrFail($playerId);
        $challenge = TypingChallenge::where('status', 'active')->latest()->first();

        if (!$challenge) {
            return redirect()->route('home')->with('error', 'No active challenge found.');
        }

        $canAttempt = $this->attemptService->canAttempt($player, $challenge);

        if (!$canAttempt['allowed']) {
            return redirect()->route('home')->with('error', $canAttempt['reason']);
        }

        $text = $challenge->activeText();

        if (!$text) {
            return redirect()->route('home')->with('error', 'Challenge text not available.');
        }

        $referralOptions = array_map('trim', explode(',', SystemSetting::get('referral_source_options', 'Social media,Friend / Family,Event poster,Other')));

        return view('challenge.play', compact('player', 'challenge', 'text', 'referralOptions'));
    }

    public function saveProfile(Request $request)
    {
        $playerId = session('verified_player_id');

        if (!$playerId) {
            return response()->json(['success' => false, 'message' => 'Session expired, please refresh.'], 401);
        }

        $data = $request->validate([
            'full_name'       => 'required|string|max:100',
            'phone'           => 'required|string|max:30',
            'referral_source' => 'required|string|max:100',
        ]);

        Player::where('id', $playerId)->update($data);

        return response()->json(['success' => true]);
    }

    public function start(Request $request)
    {
        $playerId = session('verified_player_id');

        if (!$playerId) {
            return response()->json(['success' => false, 'message' => 'Unauthorized.'], 401);
        }

        $player = Player::findOrFail($playerId);
        $challenge = TypingChallenge::where('status', 'active')->latest()->first();

        if (!$challenge) {
            return response()->json(['success' => false, 'message' => 'No active challenge.'], 404);
        }

        $canAttempt = $this->attemptService->canAttempt($player, $challenge);

        if (!$canAttempt['allowed']) {
            return response()->json(['success' => false, 'message' => $canAttempt['reason']], 403);
        }

        $text = $challenge->activeText();

        if (!$text) {
            return response()->json(['success' => false, 'message' => 'No challenge text available.'], 404);
        }

        $geo = session('geolocation', []);

        // Server-side geofence re-check — cannot be bypassed by editing the session
        if ($challenge->require_geofence) {
            if (empty($geo['latitude']) || empty($geo['longitude'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Location verification is required for this challenge.',
                ], 403);
            }

            $geoCheck = $this->geofence->checkAccess(
                (float) $geo['latitude'],
                (float) $geo['longitude'],
                $challenge
            );

            if (!$geoCheck['allowed']) {
                return response()->json([
                    'success' => false,
                    'message' => $geoCheck['message'] ?? 'You are outside the allowed area.',
                ], 403);
            }
        }

        $attempt = $this->attemptService->start($player, $challenge, $text, array_merge($geo, [
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'device_fingerprint' => $request->input('device_fingerprint'),
        ]));

        session(['current_attempt_id' => $attempt->id]);

        return response()->json([
            'success' => true,
            'attempt_id' => $attempt->id,
            'text' => $text->content,
            'text_id' => $text->id,
        ]);
    }

    public function submit(Request $request)
    {
        $data = $request->validate([
            'attempt_id' => 'required|integer',
            'user_input' => 'required|string',
            'duration_seconds' => 'required|numeric|min:0',
        ]);

        $playerId = session('verified_player_id');

        if (!$playerId) {
            return response()->json(['success' => false, 'message' => 'Unauthorized.'], 401);
        }

        $attempt = ChallengeAttempt::where('id', $data['attempt_id'])
            ->where('player_id', $playerId)
            ->where('status', 'started')
            ->firstOrFail();

        $text = $attempt->typingText;
        $scores = $this->scoring->analyze($text->content, $data['user_input'], (float) $data['duration_seconds']);

        $this->attemptService->submit($attempt, $data, $scores);

        session()->forget(['current_attempt_id', 'verified_player_id', 'challenge_email', 'geolocation']);

        return response()->json([
            'success' => true,
            'wpm' => $scores['wpm'],
            'accuracy' => $scores['accuracy'],
            'correct_words' => $scores['correct_words'],
            'wrong_words' => $scores['wrong_words'],
            'duration_seconds' => $data['duration_seconds'],
            'attempt_id' => $attempt->id,
            'redirect' => route('challenge.result', ['attempt' => $attempt->id]),
        ]);
    }

    public function result(ChallengeAttempt $attempt)
    {
        $attempt->load(['player', 'challenge', 'typingText']);
        return view('challenge.result', compact('attempt'));
    }
}
