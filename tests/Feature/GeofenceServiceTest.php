<?php

namespace Tests\Feature;

use App\Models\GeofenceRule;
use App\Models\TypingChallenge;
use App\Services\GeofenceService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GeofenceServiceTest extends TestCase
{
    use RefreshDatabase;

    private GeofenceService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new GeofenceService();
    }

    public function test_allows_when_geofence_not_required(): void
    {
        $challenge = TypingChallenge::factory()->create(['require_geofence' => false]);
        $result = $this->service->checkAccess(3.1390, 101.6869, $challenge);

        $this->assertTrue($result['allowed']);
    }

    public function test_blocks_when_outside_radius(): void
    {
        $rule = GeofenceRule::factory()->create([
            'latitude' => 3.1390,
            'longitude' => 101.6869,
            'radius_meters' => 100,
            'is_active' => true,
        ]);

        $challenge = TypingChallenge::factory()->create([
            'require_geofence' => true,
            'geofence_rule_id' => $rule->id,
        ]);

        // Far away (New York ~15000km)
        $result = $this->service->checkAccess(40.7128, -74.0060, $challenge);

        $this->assertFalse($result['allowed']);
    }

    public function test_allows_when_inside_radius(): void
    {
        $rule = GeofenceRule::factory()->create([
            'latitude' => 3.1390,
            'longitude' => 101.6869,
            'radius_meters' => 1000,
            'is_active' => true,
        ]);

        $challenge = TypingChallenge::factory()->create([
            'require_geofence' => true,
            'geofence_rule_id' => $rule->id,
        ]);

        // Very close (within 10 meters)
        $result = $this->service->checkAccess(3.1390, 101.6869, $challenge);

        $this->assertTrue($result['allowed']);
    }
}
