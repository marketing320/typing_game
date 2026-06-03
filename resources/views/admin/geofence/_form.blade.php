@if ($errors->any())
<div class="bg-red-50 border border-red-200 text-red-600 rounded-lg px-4 py-3 text-sm">
    <ul class="list-disc list-inside">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
</div>
@endif
<div>
    <label class="block text-sm font-semibold text-gray-700 mb-1">Name *</label>
    <input type="text" name="name" value="{{ old('name', $geofence?->name) }}" required
        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-blue-400">
</div>
<div class="grid grid-cols-2 gap-4">
    <div>
        <label class="block text-sm font-semibold text-gray-700 mb-1">Latitude *</label>
        <input type="number" step="0.0000001" name="latitude" value="{{ old('latitude', $geofence?->latitude) }}" required
            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-blue-400">
    </div>
    <div>
        <label class="block text-sm font-semibold text-gray-700 mb-1">Longitude *</label>
        <input type="number" step="0.0000001" name="longitude" value="{{ old('longitude', $geofence?->longitude) }}" required
            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-blue-400">
    </div>
</div>
<div>
    <label class="block text-sm font-semibold text-gray-700 mb-1">Radius (meters) *</label>
    <input type="number" name="radius_meters" value="{{ old('radius_meters', $geofence?->radius_meters ?? 500) }}" min="1" required
        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-blue-400">
</div>
<div>
    <label class="block text-sm font-semibold text-gray-700 mb-1">Warning Message</label>
    <textarea name="warning_message" rows="2" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-blue-400">{{ old('warning_message', $geofence?->warning_message) }}</textarea>
</div>
<label class="flex items-center gap-2 text-sm cursor-pointer">
    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $geofence === null || $geofence?->is_active) ? 'checked' : '' }} class="rounded">
    Active
</label>
