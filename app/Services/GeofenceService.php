<?php

namespace App\Services;

use App\Models\GeofenceRule;
use App\Models\TypingChallenge;

class GeofenceService
{
    public function checkAccess(float $userLat, float $userLon, TypingChallenge $challenge): array
    {
        if (!$challenge->require_geofence) {
            return ['allowed' => true, 'distance' => null];
        }

        $rule = $challenge->geofenceRule;

        if (!$rule || !$rule->is_active) {
            return ['allowed' => true, 'distance' => null];
        }

        $distance = $this->haversine($userLat, $userLon, $rule->latitude, $rule->longitude);
        $within = $distance <= $rule->radius_meters;

        return [
            'allowed' => $within,
            'distance' => round($distance, 2),
            'rule' => $rule,
            'message' => !$within ? ($rule->warning_message ?: 'You are outside the allowed event area.') : null,
        ];
    }

    private function haversine(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $earthRadius = 6371000;

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) ** 2
            + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon / 2) ** 2;

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }
}
