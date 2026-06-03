<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MailerService
{
    private string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = rtrim(config('app.mailer_service_url', 'http://localhost:4001'), '/');
    }

    public function sendOtp(string $email, string $otp, string $username = 'Player'): bool
    {
        try {
            $response = Http::timeout(10)->post("{$this->baseUrl}/send-otp", [
                'email' => $email,
                'otp' => $otp,
                'username' => $username,
            ]);

            if ($response->successful()) {
                return true;
            }

            Log::warning('Mailer service returned error', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return false;
        } catch (\Exception $e) {
            Log::error('Mailer service unreachable: ' . $e->getMessage());
            return false;
        }
    }
}
