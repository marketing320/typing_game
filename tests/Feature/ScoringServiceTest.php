<?php

namespace Tests\Feature;

use App\Services\ScoringService;
use Tests\TestCase;

class ScoringServiceTest extends TestCase
{
    private ScoringService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new ScoringService();
    }

    public function test_perfect_input_gives_100_accuracy(): void
    {
        $text = 'Hello world test';
        $result = $this->service->analyze($text, $text, 30);

        $this->assertEquals(100.0, $result['accuracy']);
        $this->assertEquals($result['total_characters'], $result['correct_characters']);
        $this->assertEquals(0, $result['wrong_characters']);
    }

    public function test_wpm_calculated_correctly(): void
    {
        // 30 correct chars over 30 seconds = (30/5) / (30/60) = 6 / 0.5 = 12 WPM
        $text = str_repeat('a', 30);
        $result = $this->service->analyze($text, $text, 30);

        $this->assertEquals(12, $result['wpm']);
    }

    public function test_wrong_characters_counted(): void
    {
        $original = 'hello';
        $typed = 'hellx';
        $result = $this->service->analyze($original, $typed, 10);

        $this->assertEquals(4, $result['correct_characters']);
        $this->assertEquals(1, $result['wrong_characters']);
    }

    public function test_zero_duration_returns_zero_wpm(): void
    {
        $result = $this->service->analyze('hello world', 'hello world', 0);
        $this->assertEquals(0, $result['wpm']);
    }
}
