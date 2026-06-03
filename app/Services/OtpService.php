<?php

namespace App\Services;

use App\Models\EmailOtp;
use Carbon\Carbon;
use Illuminate\Support\Str;

class OtpService
{
    public function generate(string $email, string $purpose, string $ip = null, string $userAgent = null): EmailOtp
    {
        // Invalidate old OTPs for this email+purpose
        EmailOtp::where('email', $email)
            ->where('purpose', $purpose)
            ->whereNull('verified_at')
            ->update(['expires_at' => now()->subSecond()]);

        $otp = str_pad((string) random_int(100000, 999999), 6, '0', STR_PAD_LEFT);
        $expiry = (int) config('app.otp_expiry_minutes', 10);

        return EmailOtp::create([
            'email' => $email,
            'otp_code' => $otp,
            'purpose' => $purpose,
            'expires_at' => Carbon::now()->addMinutes($expiry),
            'max_attempts' => (int) config('app.otp_max_attempts', 5),
            'ip_address' => $ip,
            'user_agent' => $userAgent,
        ]);
    }

    public function verify(string $email, string $code, string $purpose): array
    {
        $otp = EmailOtp::where('email', $email)
            ->where('purpose', $purpose)
            ->whereNull('verified_at')
            ->latest()
            ->first();

        if (!$otp) {
            return ['success' => false, 'message' => 'Invalid or expired code.'];
        }

        if ($otp->isExpired()) {
            return ['success' => false, 'message' => 'OTP has expired. Please request a new one.'];
        }

        if ($otp->hasExceededAttempts()) {
            return ['success' => false, 'message' => 'Too many failed attempts. Please request a new OTP.'];
        }

        $otp->increment('attempts');

        if ($otp->otp_code !== $code) {
            $remaining = $otp->max_attempts - $otp->attempts;
            return ['success' => false, 'message' => "Incorrect code. {$remaining} attempt(s) remaining."];
        }

        $otp->update(['verified_at' => now()]);

        return ['success' => true, 'message' => 'OTP verified successfully.'];
    }

    public function isRecentlyRequested(string $email, string $purpose, int $cooldownSeconds = 60): bool
    {
        return EmailOtp::where('email', $email)
            ->where('purpose', $purpose)
            ->where('created_at', '>=', now()->subSeconds($cooldownSeconds))
            ->exists();
    }
}
