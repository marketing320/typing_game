@extends('layouts.app')

@section('title', 'Practice Mode')

@section('content')
<div class="max-w-3xl mx-auto px-4 py-8">
    <div class="flex items-center justify-center gap-3 mb-4">
        <i data-lucide="target" class="w-6 h-6 text-amber-700"></i>
        <h1 class="text-sm font-bold text-amber-900">Practice Mode</h1>
    </div>
    <p class="text-center text-amber-600 text-[10px] mb-6 leading-loose">Practise freely. No registration needed.</p>

    @if (!$text)
        <div class="text-center text-amber-700 text-xs py-12">No practice text available yet. Check back soon!</div>
    @else

    <!-- Rules modal -->
    <div id="rules-modal" class="fixed inset-0 bg-black/70 flex items-center justify-center z-50 p-4">
        <div class="bg-white p-8 max-w-sm w-full text-center pixel-card border-2 border-amber-900" style="box-shadow: 5px 5px 0 #1c0a00;">
            <div class="inline-flex items-center justify-center w-14 h-14 bg-amber-100 border-2 border-amber-300 mb-4">
                <i data-lucide="scroll-text" class="w-7 h-7 text-amber-700"></i>
            </div>
            <h2 class="text-xs font-bold text-amber-900 mb-4">Before You Start</h2>
            <ul class="text-left space-y-3 mb-5">
                <li class="flex items-start gap-2.5 text-[10px] text-amber-800 leading-loose">
                    <i data-lucide="x-circle" class="w-4 h-4 text-red-500 shrink-0 mt-0.5"></i>
                    You cannot use your backspace button.
                </li>
            </ul>
            <p class="text-[10px] text-amber-700 font-bold mb-6 leading-loose">HAVE FUN! 🐵</p>
            <button type="button" onclick="dismissRules()"
                class="w-full bg-amber-700 text-white text-[10px] font-bold py-3 pixel-btn hover:bg-amber-800 transition-colors"
                style="box-shadow: 3px 3px 0 #1c0a00; border-color: #451a03;">
                Got it, let's practice! &gt;&gt;
            </button>
        </div>
    </div>

    <!-- Stats bar -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-4" id="stats-bar">
        <div class="bg-white p-3 text-center pixel-card">
            <div class="text-base font-bold text-amber-700" id="stat-wpm">0</div>
            <div class="text-[10px] text-amber-500 mt-1">WPM</div>
        </div>
        <div class="bg-white p-3 text-center pixel-card">
            <div class="text-base font-bold text-green-600" id="stat-accuracy">100%</div>
            <div class="text-[10px] text-amber-500 mt-1">Accuracy</div>
        </div>
        <div class="bg-white p-3 text-center pixel-card">
            <div class="text-base font-bold text-red-500" id="stat-mistakes">0</div>
            <div class="text-[10px] text-amber-500 mt-1">Mistakes</div>
        </div>
        <div class="bg-white p-3 text-center pixel-card">
            <div class="text-base font-bold text-blue-600" id="stat-time">0s</div>
            <div class="text-[10px] text-amber-500 mt-1">Time</div>
        </div>
    </div>

    <!-- Typing area -->
    <div class="bg-white pixel-card overflow-hidden" id="game-container">
        <div class="bg-amber-800 px-4 py-2 text-amber-100 text-[10px] flex justify-between border-b-2 border-amber-950">
            <span>{{ $text->title ?? 'Practice Text' }}</span>
            <span class="capitalize">{{ $text->difficulty }} · {{ strtoupper($text->language) }}</span>
        </div>

        <div class="p-6">
            <div id="text-display" onclick="document.getElementById('hidden-input').focus()">@foreach(str_split($text->content) as $i => $char)<span id="char-{{ $i }}" class="char">{{ $char }}</span>@endforeach</div>
        </div>

        <div class="px-6 pb-6">
            <input
                id="hidden-input"
                type="text"
                autocomplete="off"
                autocorrect="off"
                autocapitalize="off"
                spellcheck="false"
                class="w-full border-2 border-amber-300 px-4 py-3 text-xs focus:outline-none focus:border-amber-600 font-[inherit] bg-amber-50"
                placeholder="Click here and start typing..."
            >
        </div>
    </div>

    <!-- Result overlay -->
    <div id="result-panel" class="hidden mt-6 bg-white pixel-card p-6 text-center">
        <div class="inline-flex items-center justify-center w-14 h-14 bg-green-100 border-2 border-green-400 mb-4">
            <i data-lucide="check-circle-2" class="w-7 h-7 text-green-600"></i>
        </div>
        <h2 class="text-sm font-bold text-amber-800 mb-6">[ GREAT JOB! ]</h2>
        <div class="grid grid-cols-2 gap-4 mb-6">
            <div class="bg-amber-50 border-2 border-amber-200 p-4">
                <div class="text-xl font-bold text-amber-700" id="result-wpm">—</div>
                <div class="text-[10px] text-amber-500 mt-1">WPM</div>
            </div>
            <div class="bg-green-50 border-2 border-green-200 p-4">
                <div class="text-xl font-bold text-green-600" id="result-accuracy">—</div>
                <div class="text-[10px] text-green-500 mt-1">Accuracy</div>
            </div>
        </div>
        <button onclick="resetGame()" class="inline-flex items-center gap-2 bg-amber-700 text-white text-xs font-bold px-8 py-3 pixel-btn hover:bg-amber-800 transition-colors">
            <i data-lucide="refresh-cw" class="w-4 h-4"></i> Try Again
        </button>
    </div>

    @endif
</div>
@endsection

@push('scripts')
<script>
const TEXT_ID = {{ $text->id ?? 'null' }};
const FULL_TEXT = @json($text->content ?? '');
const SUBMIT_URL = '{{ route('rehearsal.submit') }}';
const CSRF = document.querySelector('meta[name=csrf-token]').content;

let chars = FULL_TEXT.split('');
let typedIndex = 0;
let mistakes = 0;
let startTime = null;
let timerInterval = null;
let completed = false;

const input = document.getElementById('hidden-input');
const resultPanel = document.getElementById('result-panel');

function getAnonymousId() {
    let id = localStorage.getItem('monkey_anon_id');
    if (!id) {
        id = 'anon_' + Math.random().toString(36).substr(2, 12);
        localStorage.setItem('monkey_anon_id', id);
    }
    return id;
}

function updateChar(index, state) {
    const el = document.getElementById('char-' + index);
    if (!el) return;
    el.className = 'char ' + ({correct: 'text-green-600', wrong: 'text-red-500 bg-red-50', current: 'border-b-2 border-amber-500 animate-pulse', pending: ''} [state] || '');
}

function highlightCurrent() {
    for (let i = 0; i < chars.length; i++) {
        const el = document.getElementById('char-' + i);
        if (!el) continue;
        if (el.classList.contains('text-green-600') || el.classList.contains('text-red-500')) continue;
        if (i === typedIndex) {
            el.className = 'char border-b-2 border-amber-500 animate-pulse';
        } else {
            el.className = 'char text-gray-400';
        }
    }
}

function calcWpm() {
    if (!startTime) return 0;
    const elapsed = (Date.now() - startTime) / 1000;
    const correct = Array.from(document.querySelectorAll('.char.text-green-600')).length;
    return elapsed > 0 ? Math.round((correct / 5) / (elapsed / 60)) : 0;
}

function calcAccuracy() {
    if (typedIndex === 0) return 100;
    const correct = Array.from(document.querySelectorAll('.char.text-green-600')).length;
    return Math.round((correct / typedIndex) * 100);
}

input.addEventListener('input', function (e) {
    if (completed) return;

    const value = e.target.value;
    const lastChar = value[value.length - 1];

    if (!startTime && lastChar) {
        startTime = Date.now();
        timerInterval = setInterval(() => {
            const elapsed = ((Date.now() - startTime) / 1000).toFixed(0);
            document.getElementById('stat-time').textContent = elapsed + 's';
            document.getElementById('stat-wpm').textContent = calcWpm();
            document.getElementById('stat-accuracy').textContent = calcAccuracy() + '%';
        }, 300);
    }

    if (!lastChar) return;

    if (lastChar === chars[typedIndex]) {
        updateChar(typedIndex, 'correct');
    } else {
        updateChar(typedIndex, 'wrong');
        mistakes++;
        document.getElementById('stat-mistakes').textContent = mistakes;
    }

    typedIndex++;
    e.target.value = '';
    highlightCurrent();

    if (typedIndex >= chars.length) {
        completeGame();
    }
});

function completeGame() {
    completed = true;
    clearInterval(timerInterval);
    const duration = (Date.now() - startTime) / 1000;

    const typed = Array.from({length: typedIndex}, (_, i) => {
        const el = document.getElementById('char-' + i);
        return el?.classList.contains('text-green-600') ? chars[i] : '_';
    }).join('');

    fetch(SUBMIT_URL, {
        method: 'POST',
        headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF},
        body: JSON.stringify({
            typing_text_id: TEXT_ID,
            user_input: typed,
            duration_seconds: duration.toFixed(3),
            anonymous_id: getAnonymousId(),
        })
    })
    .then(r => r.json())
    .then(data => {
        document.getElementById('result-wpm').textContent = data.wpm;
        document.getElementById('result-accuracy').textContent = data.accuracy + '%';
        resultPanel.classList.remove('hidden');
        lucide.createIcons();
        document.getElementById('game-container').classList.add('opacity-50');
    });
}

function resetGame() {
    typedIndex = 0;
    mistakes = 0;
    startTime = null;
    completed = false;
    clearInterval(timerInterval);
    document.getElementById('stat-wpm').textContent = '0';
    document.getElementById('stat-accuracy').textContent = '100%';
    document.getElementById('stat-mistakes').textContent = '0';
    document.getElementById('stat-time').textContent = '0s';
    document.querySelectorAll('.char').forEach((el) => {
        el.className = 'char text-gray-400';
    });
    resultPanel.classList.add('hidden');
    document.getElementById('game-container').classList.remove('opacity-50');
    input.value = '';
    highlightCurrent();
    input.focus();
}

function dismissRules() {
    const m = document.getElementById('rules-modal');
    if (m) m.classList.add('hidden');
    input.focus();
}

highlightCurrent();
</script>
@endpush
