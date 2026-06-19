@extends('layouts.admin')
@section('title', 'Settings')
@section('page-title', 'System Settings')

@section('content')
<div class="max-w-lg">
<form method="POST" action="{{ route('admin.settings.update') }}" class="bg-white rounded-xl shadow border border-gray-100 p-6 space-y-4">
    @csrf
    @foreach([
        'site_name' => 'Site Name',
        'otp_expiry_minutes' => 'OTP Expiry (minutes)',
        'otp_max_attempts' => 'OTP Max Attempts',
        'maintenance_mode' => 'Maintenance Mode (1=on, 0=off)',
    ] as $key => $label)
    <div>
        <label class="block text-sm font-semibold text-gray-700 mb-1">{{ $label }}</label>
        <input type="text" name="settings[{{ $key }}]" value="{{ old('settings.'.$key, $settings[$key]?->setting_value ?? '') }}"
            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-blue-400">
    </div>
    @endforeach

    <div>
        <label class="block text-sm font-semibold text-gray-700 mb-1">Referral Source Options</label>
        <textarea name="settings[referral_source_options]" rows="4"
            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-blue-400 resize-y"
            placeholder="Social media,Friend / Family,Event poster,Other">{{ old('settings.referral_source_options', $settings['referral_source_options']?->setting_value ?? 'Social media,Friend / Family,Event poster,Other') }}</textarea>
        <p class="text-xs text-gray-400 mt-1">Comma-separated. These appear as radio options in the pre-game form on /challenge/play.</p>
    </div>

    <button type="submit" class="bg-gray-900 text-white font-bold px-6 py-2.5 rounded-lg hover:bg-gray-700 transition">Save Settings</button>
</form>
</div>
@endsection
