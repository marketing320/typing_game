@extends('layouts.app')

@section('title', 'Leaderboard')

@section('content')
<div class="max-w-3xl mx-auto px-4 py-10">

    <div class="text-center mb-8">
        <div class="inline-flex items-center justify-center w-16 h-16 bg-amber-100 border-2 border-amber-300 mb-3">
            <i data-lucide="award" class="w-8 h-8 text-amber-700"></i>
        </div>
        <div class="flex items-center justify-center gap-3 mb-1">
            <h1 class="text-sm font-bold text-amber-900">Leaderboard</h1>
            <span class="inline-flex items-center gap-1.5 text-[10px] text-green-600 font-bold">
                <span class="w-2 h-2 bg-green-500 animate-pulse inline-block"></span>LIVE
            </span>
            <button type="button" onclick="document.getElementById('score-info-modal').classList.remove('hidden')"
                class="inline-flex items-center justify-center w-4 h-4 text-amber-500 hover:text-amber-700 transition-colors align-middle"
                title="How are points calculated?">
                <i data-lucide="info" class="w-3.5 h-3.5"></i>
            </button>
        </div>
        <p class="text-amber-600 text-[10px] mt-1 leading-loose inline-flex items-center gap-1.5 justify-center">
            
        </p>
    </div>

    @if($activeChallenge)
    <div class="bg-amber-700 text-white px-4 py-3 text-[10px] text-center mb-6 flex items-center justify-center gap-2 border-2 border-amber-900 pixel-card"
         style="box-shadow: 4px 4px 0 #1c0a00;">
        <i data-lucide="trophy" class="w-4 h-4 shrink-0"></i>
        <strong>{{ $activeChallenge->title }}</strong>
        @if($activeChallenge->status === 'active')
            <span class="ml-1 bg-green-500 text-white text-[10px] px-2 py-0.5 inline-flex items-center gap-1">
                <span class="w-1.5 h-1.5 bg-white animate-pulse inline-block"></span> LIVE
            </span>
        @endif
    </div>
    @endif

    {{-- Empty state (shown when no entries on first load) --}}
    <div id="empty-state" class="{{ $entries->isEmpty() ? '' : 'hidden' }} bg-white pixel-card p-12 text-center text-amber-500">
        <div class="inline-flex items-center justify-center w-14 h-14 bg-amber-50 border-2 border-amber-200 mb-3">
            <i data-lucide="inbox" class="w-7 h-7 text-amber-400"></i>
        </div>
        <p class="mb-4 text-[10px] leading-loose">No completed attempts yet. Be the first!</p>
        <a href="{{ route('challenge.access') }}" class="inline-flex items-center gap-2 bg-amber-600 text-white text-[10px] font-bold px-6 py-2.5 pixel-btn hover:bg-amber-700 transition-colors">
            Enter Challenge <i data-lucide="arrow-right" class="w-4 h-4"></i>
        </a>
    </div>

    {{-- Table (shown when entries exist) --}}
    <div id="table-wrap" class="{{ $entries->isEmpty() ? 'hidden' : '' }} bg-white pixel-card overflow-x-auto">
        <table class="w-full text-[10px]">
            <thead class="bg-amber-800 text-amber-100 border-b-2 border-amber-950">
                <tr>
                    <th class="px-4 py-3 text-left">Rank</th>
                    <th class="px-4 py-3 text-left">Player</th>
                    <th class="px-4 py-3 text-right">Score</th>
                    <th class="px-4 py-3 text-right">WPM</th>
                    <th class="px-4 py-3 text-right hidden sm:table-cell">Accuracy</th>
                    <th class="px-4 py-3 text-right hidden md:table-cell">Duration</th>
                </tr>
            </thead>
            <tbody id="leaderboard-body" class="divide-y-2 divide-amber-50">
                @foreach($entries as $entry)
                <tr class="hover:bg-amber-50 transition-colors"
                    data-username="{{ $entry['username'] }}"
                    data-rank="{{ $entry['rank'] }}">
                    <td class="px-4 py-3 font-bold">
                        @if($entry['rank'] === 1)
                            <span class="inline-flex items-center justify-center w-6 h-6 bg-yellow-400 text-yellow-900 text-[10px] font-black border-2 border-yellow-600">{{ $entry['rank'] }}</span>
                        @elseif($entry['rank'] === 2)
                            <span class="inline-flex items-center justify-center w-6 h-6 bg-gray-300 text-gray-700 text-[10px] font-black border-2 border-gray-400">{{ $entry['rank'] }}</span>
                        @elseif($entry['rank'] === 3)
                            <span class="inline-flex items-center justify-center w-6 h-6 bg-amber-600 text-white text-[10px] font-black border-2 border-amber-800">{{ $entry['rank'] }}</span>
                        @else
                            <span class="text-gray-400">#{{ $entry['rank'] }}</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 font-bold break-all max-w-[160px]">{{ $entry['username'] }}</td>
                    <td class="px-4 py-3 text-right font-bold text-amber-700">{{ number_format($entry['score'], 2) }}</td>
                    <td class="px-4 py-3 text-right font-bold text-blue-600">{{ $entry['wpm'] }}</td>
                    <td class="px-4 py-3 text-right text-green-600 hidden sm:table-cell">{{ number_format($entry['accuracy'], 1) }}%</td>
                    <td class="px-4 py-3 text-right text-gray-400 hidden md:table-cell">{{ number_format($entry['duration_seconds'], 1) }}s</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

</div>

<!-- Scoring info modal -->
<div id="score-info-modal" class="hidden fixed inset-0 bg-black/70 flex items-center justify-center z-50 p-4">
    <div class="bg-white max-w-sm w-full pixel-card border-2 border-amber-900" style="box-shadow: 5px 5px 0 #1c0a00;">
        <div class="bg-amber-800 px-5 py-4 border-b-2 border-amber-950 flex items-center justify-between">
            <h2 class="text-[11px] font-bold text-white flex items-center gap-2">
                <i data-lucide="calculator" class="w-4 h-4 text-amber-200"></i> How Points Work
            </h2>
            <button type="button" onclick="document.getElementById('score-info-modal').classList.add('hidden')"
                class="text-amber-300 hover:text-white">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>
        <div class="p-5 space-y-4 text-[10px] text-amber-800 leading-loose">
            <div class="bg-amber-50 border-2 border-amber-200 p-3 text-center">
                <span class="font-bold text-amber-900">Score = WPM × Accuracy ÷ 100</span>
            </div>
            <div>
                <p class="font-bold text-amber-900 mb-1">WPM (Words Per Minute)</p>
                <p>Your correct characters ÷ 5, measured per minute. Only correct characters count.</p>
            </div>
            <div>
                <p class="font-bold text-amber-900 mb-1">Accuracy</p>
                <p>Correct characters ÷ total characters, shown as a percentage.</p>
            </div>
            <div>
                <p class="font-bold text-amber-900 mb-1">Ranking order</p>
                <p>Highest Score first &rarr; then Accuracy &rarr; then fastest time. Exact ties share the same rank.</p>
            </div>
            <p class="text-amber-500">Tip: backspace is disabled, so accuracy is king. Type carefully! 🐵</p>
        </div>
        <div class="px-5 pb-5">
            <button type="button" onclick="document.getElementById('score-info-modal').classList.add('hidden')"
                class="w-full bg-amber-700 text-white text-[10px] font-bold py-3 pixel-btn hover:bg-amber-800 transition-colors"
                style="box-shadow: 3px 3px 0 #1c0a00; border-color: #451a03;">
                Got it!
            </button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const LEADERBOARD_DATA_URL = '{{ route('leaderboard.data') }}';

// Snapshot of username → rank from server-rendered HTML
const currentState = new Map();
document.querySelectorAll('#leaderboard-body tr[data-username]').forEach(row => {
    currentState.set(row.dataset.username, parseInt(row.dataset.rank));
});

function escapeHtml(str) {
    return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;');
}

function fmt(num, decimals) {
    return parseFloat(num).toFixed(decimals);
}

function rankBadge(rank) {
    if (rank === 1) return `<span class="inline-flex items-center justify-center w-6 h-6 bg-yellow-400 text-yellow-900 text-[10px] font-black border-2 border-yellow-600">${rank}</span>`;
    if (rank === 2) return `<span class="inline-flex items-center justify-center w-6 h-6 bg-gray-300 text-gray-700 text-[10px] font-black border-2 border-gray-400">${rank}</span>`;
    if (rank === 3) return `<span class="inline-flex items-center justify-center w-6 h-6 bg-amber-600 text-white text-[10px] font-black border-2 border-amber-800">${rank}</span>`;
    return `<span class="text-gray-400">#${rank}</span>`;
}

function buildRow(e) {
    return `<tr class="hover:bg-amber-50 transition-colors" data-username="${escapeHtml(e.username)}" data-rank="${e.rank}">
        <td class="px-4 py-3 font-bold">${rankBadge(e.rank)}</td>
        <td class="px-4 py-3 font-bold break-all max-w-[160px]">${escapeHtml(e.username)}</td>
        <td class="px-4 py-3 text-right font-bold text-amber-700">${fmt(e.score, 2)}</td>
        <td class="px-4 py-3 text-right font-bold text-blue-600">${e.wpm}</td>
        <td class="px-4 py-3 text-right text-green-600 hidden sm:table-cell">${fmt(e.accuracy, 1)}%</td>
        <td class="px-4 py-3 text-right text-gray-400 hidden md:table-cell">${fmt(e.duration_seconds, 1)}s</td>
    </tr>`;
}

function applyUpdate(data) {
    const tbody = document.getElementById('leaderboard-body');
    const tableWrap = document.getElementById('table-wrap');
    const emptyState = document.getElementById('empty-state');

    const entries = data.entries || [];

    // If no entries yet and we're already showing empty state, do nothing
    if (entries.length === 0) return;

    // Entries arrived but table was hidden — show table, hide empty state
    if (tableWrap.classList.contains('hidden')) {
        emptyState.classList.add('hidden');
        tableWrap.classList.remove('hidden');
    }

    // Diff: find rows whose rank changed or that are newly added
    const changed = new Set();
    entries.forEach(e => {
        const prev = currentState.get(e.username);
        if (prev === undefined || prev !== e.rank) changed.add(e.username);
    });

    // Update state snapshot
    currentState.clear();
    entries.forEach(e => currentState.set(e.username, e.rank));

    // Replace rows
    tbody.innerHTML = entries.map(buildRow).join('');

    // Highlight changed/new rows
    if (changed.size > 0) {
        tbody.querySelectorAll('tr[data-username]').forEach(row => {
            if (changed.has(row.dataset.username)) {
                row.classList.add('row-highlight');
                setTimeout(() => row.classList.remove('row-highlight'), 1200);
            }
        });
    }
}

async function fetchLeaderboard() {
    try {
        const res = await fetch(LEADERBOARD_DATA_URL, {cache: 'no-store'});
        if (!res.ok) return;
        const data = await res.json();
        applyUpdate(data);
    } catch (_) {
        // silently skip on network error
    }
}

// Poll every 5 seconds, paused when tab is hidden
let timer = setInterval(fetchLeaderboard, 5000);

document.addEventListener('visibilitychange', () => {
    if (document.hidden) {
        clearInterval(timer);
        timer = null;
    } else {
        fetchLeaderboard();          // immediate catch-up fetch on tab focus
        timer = setInterval(fetchLeaderboard, 5000);
    }
});
</script>
@endpush
