<?php

namespace App\Services;

use App\Mail\OtpMail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class MailerService
{
    public function sendOtp(string $email, string $otp, string $username = 'Player'): bool
    {
        try {
            Mail::to($email)->send(new OtpMail($otp, $username));
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to send OTP email: ' . $e->getMessage(), [
                'email' => $email,
            ]);
            return false;
        }
    }
}
