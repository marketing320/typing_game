<?php

namespace App\Services;

use App\Models\Player;
use App\Models\PlayerDevice;

class DeviceFingerprintService
{
    public function record(string $fingerprint, ?Player $player, string $ip = null, string $userAgent = null): PlayerDevice
    {
        $device = PlayerDevice::where('device_fingerprint', $fingerprint)->first();

        if ($device) {
            $device->update([
                'player_id' => $player?->id ?? $device->player_id,
                'last_seen_at' => now(),
                'ip_address' => $ip ?? $device->ip_address,
            ]);
            return $device;
        }

        return PlayerDevice::create([
            'player_id' => $player?->id,
            'device_fingerprint' => $fingerprint,
            'ip_address' => $ip,
            'user_agent' => $userAgent,
            'first_seen_at' => now(),
            'last_seen_at' => now(),
        ]);
    }
}
