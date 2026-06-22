@if ($errors->any())
<div class="bg-red-50 border border-red-200 text-red-600 rounded-lg px-4 py-3 text-sm">
    <ul class="list-disc list-inside">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
</div>
@endif

<div class="grid grid-cols-2 gap-4">
    <div>
        <label class="block text-sm font-semibold text-gray-700 mb-1">Mode *</label>
        <select name="mode" id="text-mode" onchange="toggleChallengeRequired()" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-blue-400">
            <option value="rehearsal" {{ old('mode', $typingText?->mode) === 'rehearsal' ? 'selected' : '' }}>Practice</option>
            <option value="challenge" {{ old('mode', $typingText?->mode) === 'challenge' ? 'selected' : '' }}>Challenge</option>
        </select>
    </div>
    <div>
        <label class="block text-sm font-semibold text-gray-700 mb-1">
            Challenge
            <span id="challenge-required-badge" class="{{ old('mode', $typingText?->mode) === 'challenge' ? '' : 'hidden' }} text-red-500">*</span>
        </label>
        <select name="challenge_id" id="challenge-select" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-blue-400">
            <option value="">— None (Practice only) —</option>
            @foreach($challenges as $c)
            <option value="{{ $c->id }}" {{ old('challenge_id', $typingText?->challenge_id) == $c->id ? 'selected' : '' }}>{{ $c->title }}</option>
            @endforeach
        </select>
        <p id="challenge-hint" class="{{ old('mode', $typingText?->mode) === 'challenge' ? '' : 'hidden' }} text-xs text-amber-600 mt-1">
            ⚠ Challenge-mode texts must be linked to a challenge or they will never be served.
        </p>
    </div>
</div>
<script>
function toggleChallengeRequired() {
    const isChallenge = document.getElementById('text-mode').value === 'challenge';
    document.getElementById('challenge-required-badge').classList.toggle('hidden', !isChallenge);
    document.getElementById('challenge-hint').classList.toggle('hidden', !isChallenge);
    document.getElementById('challenge-select').required = isChallenge;
}
toggleChallengeRequired();
</script>
<div>
    <label class="block text-sm font-semibold text-gray-700 mb-1">Title</label>
    <input type="text" name="title" value="{{ old('title', $typingText?->title) }}"
        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-blue-400">
</div>
<div>
    <label class="block text-sm font-semibold text-gray-700 mb-1">Content *</label>
    <textarea name="content" rows="5" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-blue-400 font-mono">{{ old('content', $typingText?->content) }}</textarea>
</div>
<div class="grid grid-cols-3 gap-4">
    <div>
        <label class="block text-sm font-semibold text-gray-700 mb-1">Language</label>
        <input type="text" name="language" value="{{ old('language', $typingText?->language ?? 'en') }}" maxlength="10"
            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-blue-400">
    </div>
    <div>
        <label class="block text-sm font-semibold text-gray-700 mb-1">Difficulty</label>
        <select name="difficulty" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-blue-400">
            @foreach(['easy', 'medium', 'hard'] as $d)
            <option value="{{ $d }}" {{ old('difficulty', $typingText?->difficulty) === $d ? 'selected' : '' }}>{{ ucfirst($d) }}</option>
            @endforeach
        </select>
    </div>
    <div class="flex items-end pb-2">
        <label class="flex items-center gap-2 text-sm cursor-pointer">
            <input type="checkbox" name="is_active" value="1" {{ old('is_active', $typingText === null || $typingText?->is_active) ? 'checked' : '' }} class="rounded">
            Active
        </label>
    </div>
</div>
