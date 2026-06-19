@extends('layouts.admin')
@section('title', 'Players')
@section('page-title', 'Players')

@section('content')

{{-- Search --}}
<div class="mb-4 flex items-center gap-3">
    <form method="GET" class="flex gap-2 flex-1">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search username or email..."
            class="border border-gray-300 rounded-lg px-3 py-2 text-sm flex-1 max-w-xs focus:outline-none focus:border-blue-400">
        <button type="submit" class="bg-gray-800 text-white text-sm px-4 py-2 rounded-lg">Search</button>
    </form>
</div>

{{-- Bulk action bar (hidden until rows are selected) --}}
<div id="bulk-bar" class="hidden mb-3 flex items-center gap-2 bg-gray-900 text-white rounded-lg px-4 py-2.5">
    <span id="bulk-count" class="text-sm font-semibold mr-3">0 selected</span>

    <button type="button"
        onclick="exportSelected()"
        class="bg-blue-500 hover:bg-blue-600 text-white text-xs font-bold px-3 py-1.5 rounded transition-colors">
        Export CSV
    </button>

    <button type="button"
        onclick="submitBulk('form-bulk-delete', 'Delete {count} selected player(s)?\nThey will be removed from the leaderboard.\nThis can be reversed in the database.')"
        class="bg-red-500 hover:bg-red-600 text-white text-xs font-bold px-3 py-1.5 rounded transition-colors">
        Delete Selected
    </button>

    <button type="button"
        onclick="submitBulk('form-bulk-block', 'Block {count} selected player(s)?')"
        class="bg-amber-500 hover:bg-amber-600 text-white text-xs font-bold px-3 py-1.5 rounded transition-colors">
        Block Selected
    </button>

    <button type="button"
        onclick="submitBulk('form-bulk-unblock', 'Unblock {count} selected player(s)?')"
        class="bg-green-600 hover:bg-green-700 text-white text-xs font-bold px-3 py-1.5 rounded transition-colors">
        Unblock Selected
    </button>

    {{-- Hidden forms populated by JS before submission --}}
    <form id="form-bulk-delete"   method="POST" action="{{ route('admin.players.bulk-destroy') }}"  class="hidden">@csrf</form>
    <form id="form-bulk-block"    method="POST" action="{{ route('admin.players.bulk-block') }}"    class="hidden">@csrf</form>
    <form id="form-bulk-unblock"  method="POST" action="{{ route('admin.players.bulk-unblock') }}"  class="hidden">@csrf</form>
    <form id="form-bulk-export"   method="POST" action="{{ route('admin.players.bulk-export') }}"   class="hidden">@csrf</form>
</div>

{{-- Table --}}
<div class="bg-white rounded-xl shadow border border-gray-100 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-gray-400 uppercase text-xs">
            <tr>
                <th class="px-4 py-3 w-8">
                    <input type="checkbox" id="select-all" class="rounded cursor-pointer">
                </th>
                <th class="px-4 py-3 text-left">Player</th>
                <th class="px-4 py-3 text-left">Email</th>
                <th class="px-4 py-3 text-center">Verified</th>
                <th class="px-4 py-3 text-center">Attempts</th>
                <th class="px-4 py-3 text-center">Blocked</th>
                <th class="px-4 py-3 text-center">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            @forelse($players as $p)
            <tr class="hover:bg-gray-50 {{ $p->is_blocked ? 'opacity-50' : '' }}" data-id="{{ $p->id }}">
                <td class="px-4 py-3">
                    <input type="checkbox" class="player-checkbox rounded cursor-pointer" value="{{ $p->id }}">
                </td>
                <td class="px-4 py-3">
                    <span class="font-semibold block">{{ $p->username }}</span>
                    @if($p->full_name)
                    <span class="text-xs text-gray-500 block">{{ $p->full_name }}</span>
                    @endif
                    @if($p->phone)
                    <span class="text-xs text-gray-400 block font-mono">{{ $p->phone }}</span>
                    @endif
                </td>
                <td class="px-4 py-3 text-gray-500 text-xs">{{ $p->email }}</td>
                <td class="px-4 py-3 text-center">
                    @if($p->email_verified_at)
                        <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-green-100"><i data-lucide="check" class="w-3 h-3 text-green-600"></i></span>
                    @else
                        <span class="text-gray-300">—</span>
                    @endif
                </td>
                <td class="px-4 py-3 text-center">{{ $p->challenge_attempts_count }}</td>
                <td class="px-4 py-3 text-center">
                    @if($p->is_blocked)
                        <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-red-100"><i data-lucide="ban" class="w-3 h-3 text-red-500"></i></span>
                    @else
                        <span class="text-gray-300">—</span>
                    @endif
                </td>
                <td class="px-4 py-3 text-center">
                    <div class="flex gap-2 justify-center">
                        <a href="{{ route('admin.players.show', $p) }}" class="text-blue-500 hover:underline text-xs">View</a>
                        @if($p->is_blocked)
                        <form method="POST" action="{{ route('admin.players.unblock', $p) }}">
                            @csrf
                            <button class="text-green-500 hover:underline text-xs">Unblock</button>
                        </form>
                        @else
                        <form method="POST" action="{{ route('admin.players.block', $p) }}" onsubmit="return confirm('Block this player?')">
                            @csrf
                            <button class="text-red-400 hover:underline text-xs">Block</button>
                        </form>
                        @endif
                        <form method="POST" action="{{ route('admin.players.destroy', $p) }}"
                              onsubmit="return confirm('Delete {{ addslashes($p->username) }}? They will be removed from the leaderboard.')">
                            @csrf
                            @method('DELETE')
                            <button class="text-gray-400 hover:text-red-600 hover:underline text-xs transition-colors">Delete</button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="7" class="px-4 py-8 text-center text-gray-300">No players yet.</td></tr>
            @endforelse
        </tbody>
    </table>
    @if($players->hasPages())
    <div class="px-4 py-3 border-t border-gray-100">{{ $players->links() }}</div>
    @endif
</div>

@endsection

@push('scripts')
<script>
const selectAllEl  = document.getElementById('select-all');
const bulkBar      = document.getElementById('bulk-bar');
const bulkCountEl  = document.getElementById('bulk-count');

function getChecked() {
    return Array.from(document.querySelectorAll('.player-checkbox:checked'));
}

function getAll() {
    return Array.from(document.querySelectorAll('.player-checkbox'));
}

function syncBulkBar() {
    const checked = getChecked();
    const all     = getAll();

    bulkBar.classList.toggle('hidden', checked.length === 0);
    bulkCountEl.textContent = checked.length + ' selected';

    selectAllEl.indeterminate = checked.length > 0 && checked.length < all.length;
    selectAllEl.checked       = all.length > 0 && checked.length === all.length;
}

selectAllEl.addEventListener('change', () => {
    getAll().forEach(c => c.checked = selectAllEl.checked);
    syncBulkBar();
});

document.querySelectorAll('.player-checkbox').forEach(c => {
    c.addEventListener('change', syncBulkBar);
});

function injectIds(form) {
    form.querySelectorAll('input[name="ids[]"]').forEach(i => i.remove());
    getChecked().forEach(c => {
        const input = document.createElement('input');
        input.type  = 'hidden';
        input.name  = 'ids[]';
        input.value = c.value;
        form.appendChild(input);
    });
}

function submitBulk(formId, confirmTpl) {
    const checked = getChecked();
    if (checked.length === 0) return;

    const msg = confirmTpl.replace('{count}', checked.length);
    if (!confirm(msg)) return;

    const form = document.getElementById(formId);
    injectIds(form);
    form.submit();
}

function exportSelected() {
    const checked = getChecked();
    if (checked.length === 0) return;

    const form = document.getElementById('form-bulk-export');
    injectIds(form);
    form.submit();
}
</script>
@endpush
