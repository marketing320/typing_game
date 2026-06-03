<?php

namespace App\Http\Controllers;

use App\Models\Player;
use App\Models\TypingChallenge;
use App\Services\OtpService;
use App\Services\ChallengeAttemptService;
use Illuminate\Http\Request;

class OtpController extends Controller
{
    public function __construct(
        private OtpService $otp,
        private ChallengeAttemptService $challengeService,
    ) {}

    public function verify(Request $request)
    {
        $request->validate([
            'otp_code' => 'required|string|size:6',
        ]);

        $email = session('challenge_email');

        if (!$email) {
            return response()->json(['success' => false, 'message' => 'Session expired. Please start over.'], 400);
        }

        $result = $this->otp->verify($email, $request->input('otp_code'), 'challenge_access');

        if (!$result['success']) {
            return response()->json(['success' => false, 'message' => $result['message']], 422);
        }

        // Mark player as verified
        $player = Player::where('email', $email)->first();
        if ($player && !$player->email_verified_at) {
            $player->update(['email_verified_at' => now(), 'last_login_at' => now()]);
        } elseif ($player) {
            $player->update(['last_login_at' => now()]);
        }

        // Check if player can attempt
        $challenge = TypingChallenge::where('status', 'active')->latest()->first();

        if (!$challenge) {
            return response()->json(['success' => false, 'message' => 'No active challenge found.'], 404);
        }

        $canAttempt = $this->challengeService->canAttempt($player, $challenge);

        if (!$canAttempt['allowed']) {
            return response()->json(['success' => false, 'message' => $canAttempt['reason']], 403);
        }

        session(['verified_player_id' => $player->id]);

        return response()->json(['success' => true, 'redirect' => route('challenge.play')]);
    }
}
