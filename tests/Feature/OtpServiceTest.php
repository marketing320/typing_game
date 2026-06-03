<?php

namespace Tests\Feature;

use App\Models\EmailOtp;
use App\Services\OtpService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OtpServiceTest extends TestCase
{
    use RefreshDatabase;

    private OtpService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new OtpService();
    }

    public function test_generates_otp_for_email(): void
    {
        $otp = $this->service->generate('test@example.com', 'challenge_access');

        $this->assertNotNull($otp);
        $this->assertEquals('test@example.com', $otp->email);
        $this->assertEquals(6, strlen($otp->otp_code));
        $this->assertTrue(is_numeric($otp->otp_code));
    }

    public function test_otp_expires_after_configured_time(): void
    {
        $otp = $this->service->generate('test@example.com', 'challenge_access');
        $this->assertFalse($otp->isExpired());

        // Travel past expiry
        Carbon::setTestNow(now()->addMinutes(11));
        $otp->refresh();
        $this->assertTrue($otp->isExpired());

        Carbon::setTestNow(null);
    }

    public function test_verify_returns_success_for_correct_code(): void
    {
        $otp = $this->service->generate('test@example.com', 'challenge_access');
        $result = $this->service->verify('test@example.com', $otp->otp_code, 'challenge_access');

        $this->assertTrue($result['success']);
    }

    public function test_verify_fails_for_wrong_code(): void
    {
        $this->service->generate('test@example.com', 'challenge_access');
        $result = $this->service->verify('test@example.com', '000000', 'challenge_access');

        $this->assertFalse($result['success']);
    }

    public function test_verify_fails_after_max_attempts(): void
    {
        $otp = $this->service->generate('test@example.com', 'challenge_access');
        $otp->update(['max_attempts' => 2]);

        $this->service->verify('test@example.com', '000000', 'challenge_access');
        $this->service->verify('test@example.com', '000000', 'challenge_access');
        $result = $this->service->verify('test@example.com', $otp->otp_code, 'challenge_access');

        $this->assertFalse($result['success']);
    }
}
