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

    @php
        $mobileEnabled = old('settings.mobile_block_enabled', $settings['mobile_block_enabled']?->setting_value ?? '1');
        $mobileMessage = old('settings.mobile_block_message', $settings['mobile_block_message']?->setting_value
            ?? 'This challenge can only be played on our official Brightstar Computer display PC setup. Please come to our booth at Plaza Lowyat Ground Floor and use the computer provided to take part. Please ask our staff for assistance if you need some.  ^w^');
    @endphp

    <div class="border-t border-gray-100 pt-4">
        <label class="flex items-center gap-2.5 cursor-pointer mb-1">
            {{-- hidden 0 ensures an unchecked box still submits a value --}}
            <input type="hidden" name="settings[mobile_block_enabled]" value="0">
            <input type="checkbox" name="settings[mobile_block_enabled]" value="1"
                {{ $mobileEnabled === '1' ? 'checked' : '' }} class="rounded">
            <span class="text-sm font-semibold text-gray-700">Block Mobile Devices</span>
        </label>
        <p class="text-xs text-gray-400 mb-3">When on, phones &amp; tablets are blocked from the challenge flow and shown the message below. Practice &amp; leaderboard stay open on mobile.</p>

        <label class="block text-sm font-semibold text-gray-700 mb-1">Mobile Block Message</label>
        <textarea name="settings[mobile_block_message]" rows="4"
            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-blue-400 resize-y">{{ $mobileMessage }}</textarea>
        <p class="text-xs text-gray-400 mt-1">Shown on the blocked page. Line breaks are preserved.</p>
    </div>

    <button type="submit" class="bg-gray-900 text-white font-bold px-6 py-2.5 rounded-lg hover:bg-gray-700 transition">Save Settings</button>
</form>
</div>
@endsection
