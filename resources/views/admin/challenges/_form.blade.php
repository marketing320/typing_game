@if ($errors->any())
<div class="bg-red-50 border border-red-200 text-red-600 rounded-lg px-4 py-3 text-sm">
    <ul class="list-disc list-inside">
        @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
    </ul>
</div>
@endif

<div>
    <label class="block text-sm font-semibold text-gray-700 mb-1">Title *</label>
    <input type="text" name="title" value="{{ old('title', $challenge?->title) }}" required
        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-blue-400">
</div>
<div>
    <label class="block text-sm font-semibold text-gray-700 mb-1">Description</label>
    <textarea name="description" rows="3" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-blue-400">{{ old('description', $challenge?->description) }}</textarea>
</div>
<div class="grid grid-cols-2 gap-4">
    <div>
        <label class="block text-sm font-semibold text-gray-700 mb-1">Status *</label>
        <select name="status" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-blue-400">
            @foreach(['draft', 'active', 'ended'] as $s)
            <option value="{{ $s }}" {{ old('status', $challenge?->status) === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
            @endforeach
        </select>
        <p class="text-xs text-amber-600 mt-1">Setting to Active will automatically end any currently active challenge.</p>
    </div>
    <div>
        <label class="block text-sm font-semibold text-gray-700 mb-1">Max Attempts (lifetime)</label>
        <input type="number" name="max_attempts_per_day" value="{{ old('max_attempts_per_day', $challenge?->max_attempts_per_day ?? 1) }}" min="1"
            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-blue-400">
        <p class="text-xs text-gray-400 mt-1">Total attempts a player may ever make. Reached = blocked permanently.</p>
    </div>
</div>
<div class="grid grid-cols-2 gap-4">
    <div>
        <label class="block text-sm font-semibold text-gray-700 mb-1">Start Date</label>
        <input type="datetime-local" name="start_at" value="{{ old('start_at', $challenge?->start_at?->format('Y-m-d\TH:i')) }}"
            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-blue-400">
    </div>
    <div>
        <label class="block text-sm font-semibold text-gray-700 mb-1">End Date</label>
        <input type="datetime-local" name="end_at" value="{{ old('end_at', $challenge?->end_at?->format('Y-m-d\TH:i')) }}"
            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-blue-400">
    </div>
</div>
<div>
    <label class="flex items-center gap-2 text-sm cursor-pointer">
        <input type="checkbox" name="allow_retry_next_day" value="1" {{ old('allow_retry_next_day', $challenge?->allow_retry_next_day) ? 'checked' : '' }} class="rounded">
        Allow Retry Next Day
    </label>
</div>

{{-- Geofence section — both steps (checkbox + rule) must be completed for enforcement to work --}}
<div class="border-2 border-dashed border-gray-300 rounded-lg p-4 space-y-3" id="geofence-box">
    <div class="flex items-start justify-between">
        <label class="flex items-center gap-2 text-sm font-semibold cursor-pointer">
            <input type="checkbox" name="require_geofence" value="1" id="cb-geofence"
                {{ old('require_geofence', $challenge?->require_geofence) ? 'checked' : '' }}
                class="rounded" onchange="toggleGeofence()">
            Require Geofence
        </label>
        <span id="geo-badge-off" class="text-xs bg-gray-100 text-gray-500 px-2 py-0.5 rounded {{ old('require_geofence', $challenge?->require_geofence) ? 'hidden' : '' }}">
            OFF — anyone can play
        </span>
        <span id="geo-badge-on" class="text-xs bg-green-100 text-green-700 px-2 py-0.5 rounded {{ old('require_geofence', $challenge?->require_geofence) ? '' : 'hidden' }}">
            ON — location enforced
        </span>
    </div>

    <div id="geofence-select" class="{{ old('require_geofence', $challenge?->require_geofence) ? '' : 'hidden' }} space-y-2">
        <p class="text-xs text-amber-700 bg-amber-50 border border-amber-200 rounded px-3 py-2">
            ⚠ You must select a rule below. Without a rule selected, the geofence checkbox alone does nothing.
        </p>
        <label class="block text-sm font-semibold text-gray-700">Geofence Rule *</label>
        @if($geofenceRules->isEmpty())
            <p class="text-sm text-red-600 bg-red-50 border border-red-200 rounded px-3 py-2">
                No active geofence rules exist yet.
                <a href="{{ route('admin.geofence.create') }}" class="underline font-semibold">Create one first →</a>
            </p>
        @else
        <select name="geofence_rule_id" id="geofence-rule-select"
            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-blue-400">
            <option value="">— Select a rule —</option>
            @foreach($geofenceRules as $rule)
            <option value="{{ $rule->id }}" {{ old('geofence_rule_id', $challenge?->geofence_rule_id) == $rule->id ? 'selected' : '' }}>
                {{ $rule->name }} — {{ $rule->radius_meters }}m radius
                ({{ $rule->latitude }}, {{ $rule->longitude }})
            </option>
            @endforeach
        </select>
        @endif
    </div>
</div>

<script>
function toggleGeofence() {
    const checked = document.getElementById('cb-geofence').checked;
    document.getElementById('geofence-select').classList.toggle('hidden', !checked);
    document.getElementById('geo-badge-off').classList.toggle('hidden', checked);
    document.getElementById('geo-badge-on').classList.toggle('hidden', !checked);
    const sel = document.getElementById('geofence-rule-select');
    if (sel) sel.required = checked;
}
// Enforce required on page load if already checked
(function () {
    const sel = document.getElementById('geofence-rule-select');
    if (sel && document.getElementById('cb-geofence').checked) sel.required = true;
})();
</script>
