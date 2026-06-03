@extends('layouts.app')

@section('title', 'Challenge')

@section('content')
<div class="max-w-3xl mx-auto px-4 py-8">

    <div class="text-center mb-4">
        <p class="text-amber-600 text-[10px] font-bold flex items-center justify-center gap-1.5">
            <i data-lucide="trophy" class="w-4 h-4"></i>{{ $challenge->title }}
        </p>
        <p class="text-[10px] text-amber-500 mt-1 leading-loose">Player: <strong>{{ $player->username }}</strong> | One attempt only — type carefully!</p>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-4 gap-3 mb-4">
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

    <!-- Progress bar -->
    <div class="w-full bg-amber-100 h-4 mb-4 border-2 border-amber-300">
        <div id="progress-bar" class="bg-amber-600 h-full transition-all duration-100" style="width: 0%"></div>
    </div>

    <!-- Game ready overlay -->
    <div id="ready-overlay" class="bg-amber-800 p-8 text-center text-white mb-4 border-2 border-amber-950 pixel-card"
         style="box-shadow: 4px 4px 0 #1c0a00;">
        <div class="inline-flex items-center justify-center w-16 h-16 bg-amber-700 border-2 border-amber-600 mb-5">
            <i data-lucide="keyboard" class="w-8 h-8 text-amber-200"></i>
        </div>
        <h2 class="text-sm font-bold mb-3">READY TO TYPE?</h2>
        <p class="text-amber-300 text-[10px] mb-6 leading-loose">Once you start, the timer begins.<br>You have ONE attempt.</p>
        <button id="start-btn" onclick="startGame()"
            class="bg-amber-400 text-amber-950 text-xs font-bold px-10 py-3 pixel-btn hover:bg-amber-300 transition-colors"
            style="border-color: #78350f; box-shadow: 3px 3px 0 #451a03;">
            &gt;&gt; START CHALLENGE &lt;&lt;
        </button>
    </div>

    <!-- Typing area (hidden until started) -->
    <div id="game-area" class="hidden">
        <div class="bg-white pixel-card overflow-hidden">
            <div class="bg-amber-800 px-4 py-2 text-amber-100 text-[10px] flex justify-between items-center border-b-2 border-amber-950">
                <span>Challenge Text</span>
                <span id="monkey-display" class="inline-flex items-center justify-center w-6 h-6 bg-amber-700 border border-amber-600 transition-colors">
                    <i id="monkey-icon" data-lucide="keyboard" class="w-3.5 h-3.5 text-amber-200"></i>
                </span>
            </div>
            <div class="p-6">
                <div id="text-display" onclick="document.getElementById('hidden-input').focus()">
                </div>
            </div>
            <div class="px-6 pb-6">
                <input id="hidden-input" type="text"
                    autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false"
                    class="w-full border-2 border-amber-300 px-4 py-3 text-xs focus:outline-none focus:border-amber-600 font-[inherit] bg-amber-50"
                    placeholder="Click here and start typing...">
            </div>
        </div>
    </div>

    <!-- Submitting overlay -->
    <div id="submitting-overlay" class="hidden text-center py-12">
        <div class="inline-flex items-center justify-center w-14 h-14 bg-amber-100 border-2 border-amber-300 mb-4">
            <i data-lucide="loader-2" class="w-7 h-7 text-amber-600 animate-spin"></i>
        </div>
        <p class="text-amber-700 text-xs font-bold">Calculating your score...</p>
    </div>

</div>
@endsection

@push('scripts')
<script>
const START_URL = '{{ route('challenge.start') }}';
const SUBMIT_URL = '{{ route('challenge.submit') }}';
const CSRF = document.querySelector('meta[name=csrf-token]').content;
const TEXT_CONTENT = @json($text->content);

let attemptId = null;
let chars = TEXT_CONTENT.split('');
let typedIndex = 0;
let mistakes = 0;
let startTime = null;
let timerInterval = null;
let completed = false;
let typedRecord = [];

function generateFingerprint() {
    return btoa([navigator.userAgent, screen.width, screen.height, Intl.DateTimeFormat().resolvedOptions().timeZone, navigator.language].join('|'));
}

function buildTextDisplay() {
    const display = document.getElementById('text-display');
    display.innerHTML = chars.map((c, i) => `<span id="char-${i}" class="char text-gray-400">${c}</span>`).join('');
    highlightCurrent();
}

function highlightCurrent() {
    const el = document.getElementById('char-' + typedIndex);
    if (el) el.className = 'char border-b-2 border-amber-500 animate-pulse';
}

function updateChar(i, ok) {
    const el = document.getElementById('char-' + i);
    if (el) el.className = 'char ' + (ok ? 'text-green-600' : 'text-red-500 bg-red-50');
}

function calcWpm() {
    if (!startTime) return 0;
    const elapsed = (Date.now() - startTime) / 1000;
    const correct = typedRecord.filter(v => v).length;
    return elapsed > 0 ? Math.round((correct / 5) / (elapsed / 60)) : 0;
}

function calcAccuracy() {
    if (typedIndex === 0) return 100;
    return Math.round((typedRecord.filter(v => v).length / typedIndex) * 100);
}

async function startGame() {
    document.getElementById('start-btn').disabled = true;
    document.getElementById('start-btn').textContent = 'Starting...';

    const fp = generateFingerprint();

    try {
        const res = await fetch(START_URL, {
            method: 'POST',
            headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF},
            body: JSON.stringify({device_fingerprint: fp})
        });
        const data = await res.json();

        if (!data.success) {
            alert(data.message || 'Could not start challenge.');
            window.location.href = '/';
            return;
        }

        attemptId = data.attempt_id;
        document.getElementById('ready-overlay').classList.add('hidden');
        document.getElementById('game-area').classList.remove('hidden');
        buildTextDisplay();
        document.getElementById('hidden-input').focus();

        startTime = Date.now();
        timerInterval = setInterval(() => {
            const elapsed = ((Date.now() - startTime) / 1000).toFixed(0);
            document.getElementById('stat-time').textContent = elapsed + 's';
            document.getElementById('stat-wpm').textContent = calcWpm();
            document.getElementById('stat-accuracy').textContent = calcAccuracy() + '%';
        }, 300);

    } catch(e) {
        alert('Network error. Please refresh.');
    }
}

document.getElementById('hidden-input').addEventListener('input', function(e) {
    if (completed || !attemptId) return;
    const val = e.target.value;
    const c = val[val.length - 1];
    if (!c) return;

    const ok = c === chars[typedIndex];
    updateChar(typedIndex, ok);
    typedRecord.push(ok);
    if (!ok) {
        mistakes++;
        document.getElementById('stat-mistakes').textContent = mistakes;
        const md = document.getElementById('monkey-display');
        md.classList.replace('bg-amber-700', 'bg-red-600');
        setTimeout(() => { md.classList.replace('bg-red-600', 'bg-amber-700'); }, 400);
    } else {
        const md = document.getElementById('monkey-display');
        md.classList.replace('bg-amber-700', 'bg-green-600');
        setTimeout(() => { md.classList.replace('bg-green-600', 'bg-amber-700'); }, 300);
    }

    typedIndex++;
    e.target.value = '';

    document.getElementById('progress-bar').style.width = ((typedIndex / chars.length) * 100) + '%';

    if (typedIndex < chars.length) {
        highlightCurrent();
    } else {
        submitResult();
    }
});

async function submitResult() {
    completed = true;
    clearInterval(timerInterval);
    const duration = (Date.now() - startTime) / 1000;
    const userInput = chars.map((c, i) => typedRecord[i] ? c : '_').join('');

    document.getElementById('game-area').classList.add('hidden');
    document.getElementById('submitting-overlay').classList.remove('hidden');

    const res = await fetch(SUBMIT_URL, {
        method: 'POST',
        headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF},
        body: JSON.stringify({
            attempt_id: attemptId,
            user_input: userInput,
            duration_seconds: duration.toFixed(3),
        })
    });
    const data = await res.json();

    if (data.success && data.redirect) {
        window.location.href = data.redirect;
    } else {
        alert(data.message || 'Submission error.');
    }
}

window.addEventListener('beforeunload', function(e) {
    if (attemptId && !completed) {
        e.preventDefault();
        e.returnValue = '';
    }
});
</script>
@endpush
