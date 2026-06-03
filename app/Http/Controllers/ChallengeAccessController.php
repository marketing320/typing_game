<?php

namespace App\Http\Controllers;

use App\Models\Player;
use App\Models\TypingChallenge;
use App\Services\GeofenceService;
use App\Services\OtpService;
use App\Services\MailerService;
use Illuminate\Http\Request;

class ChallengeAccessController extends Controller
{
    public function __construct(
        private GeofenceService $geofence,
        private OtpService $otp,
        private MailerService $mailer,
    ) {}

    public function access()
    {
        $challenge = TypingChallenge::where('status', 'active')->latest()->first();
        $requireGeofence = $challenge?->require_geofence ?? false;
        return view('challenge.access', compact('requireGeofence'));
    }

    public function checkLocation(Request $request)
    {
        $data = $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $challenge = TypingChallenge::where('status', 'active')->latest()->first();

        if (!$challenge) {
            return response()->json(['allowed' => false, 'message' => 'No active challenge found.']);
        }

        $result = $this->geofence->checkAccess($data['latitude'], $data['longitude'], $challenge);

        session(['geolocation' => [
            'latitude' => $data['latitude'],
            'longitude' => $data['longitude'],
            'is_within_geofence' => $result['allowed'],
            'distance' => $result['distance'] ?? null,
        ]]);

        return response()->json([
            'allowed' => $result['allowed'],
            'message' => $result['message'] ?? null,
        ]);
    }

    public function requestOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email|max:255',
            'username' => 'required|string|max:64',
        ]);

        $email = $request->input('email');
        $username = $request->input('username');

        if ($this->otp->isRecentlyRequested($email, 'challenge_access', 60)) {
            return response()->json(['success' => false, 'message' => 'Please wait 60 seconds before requesting another OTP.'], 429);
        }

        // Create or find player
        $player = Player::firstOrCreate(
            ['email' => $email],
            ['username' => $username]
        );

        if ($player->is_blocked) {
            return response()->json(['success' => false, 'message' => 'Your account is not eligible for this challenge.'], 403);
        }

        $otpRecord = $this->otp->generate($email, 'challenge_access', $request->ip(), $request->userAgent());

        // Dev mode: store OTP in session so the verify page can display it
        if (app()->environment('local')) {
            session(['dev_otp' => $otpRecord->otp_code]);
        }

        $sent = $this->mailer->sendOtp($email, $otpRecord->otp_code, $player->username);

        // In local env, continue even if mailer is not configured yet
        if (!$sent && !app()->environment('local')) {
            return response()->json(['success' => false, 'message' => 'Failed to send OTP. Please try again.'], 500);
        }

        session(['challenge_email' => $email, 'challenge_username' => $player->username]);

        return response()->json(['success' => true, 'message' => 'OTP sent to your email.']);
    }

    public function otpForm()
    {
        if (!session('challenge_email')) {
            return redirect()->route('challenge.access');
        }

        return view('challenge.otp');
    }
}
