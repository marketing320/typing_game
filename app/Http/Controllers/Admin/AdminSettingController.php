<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SystemSetting;
use Illuminate\Http\Request;

class AdminSettingController extends Controller
{
    private array $knownSettings = [
        'site_name' => 'Site Name',
        'otp_expiry_minutes' => 'OTP Expiry (minutes)',
        'otp_max_attempts' => 'OTP Max Attempts',
        'maintenance_mode' => 'Maintenance Mode',
        'referral_source_options' => 'Referral Source Options',
    ];

    public function index()
    {
        $settings = SystemSetting::whereIn('setting_key', array_keys($this->knownSettings))->get()
            ->keyBy('setting_key');

        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'settings' => 'required|array',
            'settings.*' => 'nullable|string|max:500',
        ]);

        foreach ($request->input('settings', []) as $key => $value) {
            if (array_key_exists($key, $this->knownSettings)) {
                SystemSetting::set($key, $value);
            }
        }

        return back()->with('success', 'Settings updated.');
    }
}
