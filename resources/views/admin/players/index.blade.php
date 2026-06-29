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
<div id="bulk-bar" class="hidden mb-3 bg-gray-900 text-white rounded-lg px-4 py-2.5">
    <div class="flex items-center gap-2 flex-wrap">
        <span id="bulk-count" class="text-sm font-semibold mr-3">0 selected</span>

        <button type="button" onclick="exportSelected()"
            class="bg-blue-500 hover:bg-blue-600 text-white text-xs font-bold px-3 py-1.5 rounded transition-colors">
            Export CSV
        </button>

        {{-- Delete/Block/Unblock act on ticked rows only (never the whole dataset) --}}
        <span id="bulk-mutate-actions" class="flex items-center gap-2">
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
        </span>

        <button type="button" id="clear-selection" onclick="clearSelectAllMatching()"
            class="hidden text-gray-300 hover:text-white text-xs underline ml-1">
            Clear selection
        </button>
    </div>

    {{-- "Select all matching" banner — shown once the whole visible page is ticked and more pages exist --}}
    <div id="select-all-banner" class="hidden mt-2 text-xs text-gray-300">
        All <span id="page-count"></span> on this page selected.
        <button type="button" onclick="enableSelectAllMatching()"
            class="underline font-bold text-blue-300 hover:text-blue-200 ml-1">
            Select all {{ number_format($players->total()) }} players
        </button>
        <span class="text-gray-500">(for export)</span>
    </div>

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
                <th class="px-4 py-3 text-center">
                    <a href="{{ route('admin.players.index', array_filter(['search' => request('search'), 'dir' => $dir === 'asc' ? 'desc' : 'asc'])) }}"
                       class="inline-flex items-center justify-center gap-1 hover:text-gray-600" title="Sort by ranking">
                        Ranking
                        <i data-lucide="{{ $dir === 'asc' ? 'arrow-up' : 'arrow-down' }}" class="w-3 h-3"></i>
                    </a>
                </th>
                <th class="px-4 py-3 text-center">Blocked</th>
                <th class="px-4 py-3 text-center">WhatsApp</th>
                <th class="px-4 py-3 text-center">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            @forelse($players as $p)
            @php
                $dispPhone = $p->phone ?? '';
                $waPhone   = preg_replace('/\D+/', '', $dispPhone);
                $waLink    = $waPhone !== '' ? 'https://wa.me/' . $waPhone : '';
                $rank      = $rankMap[$p->id] ?? null;
            @endphp
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
                    @if($rank)
                        <span class="font-bold text-amber-700">#{{ $rank }}</span>
                    @else
                        <span class="text-gray-300">—</span>
                    @endif
                </td>
                <td class="px-4 py-3 text-center">
                    @if($p->is_blocked)
                        <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-red-100"><i data-lucide="ban" class="w-3 h-3 text-red-500"></i></span>
                    @else
                        <span class="text-gray-300">—</span>
                    @endif
                </td>
                <td class="px-4 py-3 text-center">
                    @if($dispPhone)
                        <a href="{{ $waLink }}" target="_blank" rel="noopener noreferrer"
                           class="text-green-500 hover:text-green-600 transition-colors inline-flex" title="WhatsApp {{ $dispPhone }}">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4">
                                <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413z"/>
                            </svg>
                        </a>
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
            <tr><td colspan="9" class="px-4 py-8 text-center text-gray-300">No players yet.</td></tr>
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
const selectAllEl   = document.getElementById('select-all');
const bulkBar       = document.getElementById('bulk-bar');
const bulkCountEl   = document.getElementById('bulk-count');
const banner        = document.getElementById('select-all-banner');
const pageCountEl    = document.getElementById('page-count');
const mutateActions = document.getElementById('bulk-mutate-actions');
const clearBtn      = document.getElementById('clear-selection');

const TOTAL  = {{ $players->total() }};
const SEARCH = @json(request('search', ''));

// When true, bulk EXPORT targets the whole filtered dataset, not just ticked rows.
let selectAllMatching = false;

function getChecked() {
    return Array.from(document.querySelectorAll('.player-checkbox:checked'));
}

function getAll() {
    return Array.from(document.querySelectorAll('.player-checkbox'));
}

function syncBulkBar() {
    const checked = getChecked();
    const all     = getAll();

    if (selectAllMatching) {
        bulkBar.classList.remove('hidden');
        bulkCountEl.textContent = 'All ' + TOTAL.toLocaleString() + ' players selected';
        mutateActions.classList.add('hidden');   // delete/block/unblock never run on the whole dataset
        clearBtn.classList.remove('hidden');
        banner.classList.add('hidden');
        selectAllEl.checked = true;
        selectAllEl.indeterminate = false;
        return;
    }

    bulkBar.classList.toggle('hidden', checked.length === 0);
    bulkCountEl.textContent = checked.length + ' selected';
    mutateActions.classList.remove('hidden');
    clearBtn.classList.add('hidden');

    selectAllEl.indeterminate = checked.length > 0 && checked.length < all.length;
    selectAllEl.checked       = all.length > 0 && checked.length === all.length;

    // Offer whole-dataset selection only when the full page is ticked AND more pages exist
    if (all.length > 0 && checked.length === all.length && TOTAL > all.length) {
        pageCountEl.textContent = all.length;
        banner.classList.remove('hidden');
    } else {
        banner.classList.add('hidden');
    }
}

selectAllEl.addEventListener('change', () => {
    selectAllMatching = false;
    getAll().forEach(c => c.checked = selectAllEl.checked);
    syncBulkBar();
});

document.querySelectorAll('.player-checkbox').forEach(c => {
    c.addEventListener('change', () => { selectAllMatching = false; syncBulkBar(); });
});

function enableSelectAllMatching() {
    selectAllMatching = true;
    syncBulkBar();
}

function clearSelectAllMatching() {
    selectAllMatching = false;
    selectAllEl.checked = false;
    getAll().forEach(c => c.checked = false);
    syncBulkBar();
}

function clearExtra(form) {
    form.querySelectorAll('input[name="ids[]"], input[name="select_all"], input[name="search"]').forEach(i => i.remove());
}

function appendHidden(form, name, value) {
    const input = document.createElement('input');
    input.type  = 'hidden';
    input.name  = name;
    input.value = value;
    form.appendChild(input);
}

function injectIds(form) {
    clearExtra(form);
    getChecked().forEach(c => appendHidden(form, 'ids[]', c.value));
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
    const form = document.getElementById('form-bulk-export');

    if (selectAllMatching) {
        if (!confirm('Export all ' + TOTAL.toLocaleString() + ' players' + (SEARCH ? ' matching "' + SEARCH + '"' : '') + '?')) return;
        clearExtra(form);
        appendHidden(form, 'select_all', '1');
        if (SEARCH) appendHidden(form, 'search', SEARCH);
        form.submit();
        return;
    }

    if (getChecked().length === 0) return;
    injectIds(form);
    form.submit();
}
</script>
@endpush
