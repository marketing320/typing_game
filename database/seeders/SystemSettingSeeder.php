<?php

namespace Database\Seeders;

use App\Models\SystemSetting;
use Illuminate\Database\Seeder;

class SystemSettingSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = [
            'site_name' => 'Typing Monkey',
            'otp_expiry_minutes' => '10',
            'otp_max_attempts' => '5',
            'maintenance_mode' => '0',
        ];

        foreach ($defaults as $key => $value) {
            SystemSetting::firstOrCreate(['setting_key' => $key], ['setting_value' => $value]);
        }
    }
}
