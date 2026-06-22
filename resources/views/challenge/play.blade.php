@extends('layouts.app')

@section('title', 'Challenge')

@section('content')

{{-- Pre-game profile modal --}}
<div id="profile-modal" class="fixed inset-0 bg-black/75 flex items-start justify-center z-50 p-4 overflow-y-auto">
    <div class="bg-amber-50 max-w-sm w-full pixel-card border-2 border-amber-900 my-6" style="box-shadow: 5px 5px 0 #1c0a00;">

        {{-- Modal header --}}
        <div class="bg-amber-800 px-5 py-4 border-b-2 border-amber-950 text-center">
            <div class="inline-flex items-center justify-center w-10 h-10 bg-amber-700 border-2 border-amber-600 mb-2">
                <i data-lucide="clipboard-list" class="w-5 h-5 text-amber-200"></i>
            </div>
            <h2 class="text-[11px] font-bold text-white">hold up bestie ✋</h2>
            <p class="text-amber-400 text-[10px] mt-1 leading-loose">just need ur deets before we cook 🔥</p>
        </div>

        <div class="p-5 space-y-4">

            {{-- Full name --}}
            <div>
                <label for="p-fullname" class="block text-[10px] font-bold text-amber-900 mb-1.5">
                    ur full name <span class="text-red-500">*</span>
                </label>
                <input type="text" id="p-fullname"
                    placeholder="e.g. Ahmad Ridhwan..."
                    maxlength="100"
                    class="w-full border-2 border-amber-300 bg-white px-3 py-2.5 text-[11px] focus:outline-none focus:border-amber-600 font-[inherit]">
                <p id="err-name" class="hidden text-[10px] text-red-500 mt-1">name can't be blank bestie 😭</p>
            </div>

            {{-- Email --}}
            <div>
                <label class="block text-[10px] font-bold text-amber-900 mb-1.5">
                    email <span class="text-amber-400 font-normal text-[9px]">(verified + locked 🔒)</span>
                </label>
                <input type="email" value="{{ $player->email }}" readonly
                    class="w-full border-2 border-amber-200 bg-amber-100 px-3 py-2.5 text-[11px] text-amber-500 cursor-not-allowed font-[inherit]">
            </div>

            {{-- Phone --}}
            <div>
                <label class="block text-[10px] font-bold text-amber-900 mb-1.5">
                    phone no. <span class="text-red-500">*</span>
                </label>
                <div class="flex">
                    <div class="relative flex-shrink-0" id="cp-wrap">
                        <button type="button" id="cp-btn" onclick="toggleCpDd(event)"
                            class="flex items-center gap-1 border-2 border-r-0 border-amber-300 bg-white px-2.5 py-2.5 text-[11px] font-bold text-amber-800 hover:bg-amber-50 focus:outline-none font-[inherit] h-full">
                            <span id="cp-flag">🇲🇾</span>
                            <span id="cp-code" class="text-[10px]">+60</span>
                            <i data-lucide="chevron-down" class="w-3 h-3 text-amber-400"></i>
                        </button>
                        <div id="cp-dd" class="hidden absolute top-full left-0 mt-0.5 w-64 bg-white border-2 border-amber-300 shadow-xl z-[60]">
                            <div class="p-1.5 border-b border-amber-100">
                                <input type="text" id="cp-search" placeholder="search country..."
                                    oninput="filterCp(this.value)"
                                    class="w-full border border-amber-200 px-2 py-1.5 text-[10px] focus:outline-none font-[inherit]">
                            </div>
                            <div id="cp-list" class="overflow-y-auto" style="max-height:180px;"></div>
                        </div>
                    </div>
                    <input type="tel" id="p-phone"
                        placeholder="12-3456789"
                        maxlength="20"
                        class="flex-1 min-w-0 border-2 border-amber-300 bg-white px-3 py-2.5 text-[11px] focus:outline-none focus:border-amber-600 font-[inherit]">
                </div>
                <p id="err-phone" class="hidden text-[10px] text-red-500 mt-1">don't ghost us, drop ur number 📱</p>
            </div>

            {{-- Referral source --}}
            <div>
                <label class="block text-[10px] font-bold text-amber-900 mb-2">
                    how'd you find us? 👀 <span class="text-red-500">*</span>
                </label>
                <div class="space-y-2">
                    @foreach($referralOptions as $opt)
                    <label class="flex items-center gap-2.5 cursor-pointer">
                        <input type="radio" name="referral_source" value="{{ trim($opt) }}"
                            class="w-3.5 h-3.5 accent-amber-700 cursor-pointer flex-shrink-0">
                        <span class="text-[10px] text-amber-800 font-bold leading-relaxed">{{ trim($opt) }}</span>
                    </label>
                    @endforeach
                </div>
                <p id="err-referral" class="hidden text-[10px] text-red-500 mt-1">spill it, how'd you find us? 👀</p>
            </div>

            {{-- Submit --}}
            <button id="profile-btn" onclick="submitProfile()"
                class="w-full bg-amber-800 text-amber-100 text-[10px] font-bold py-3.5 mt-2 pixel-btn hover:bg-amber-700 transition-colors"
                style="border-color: #1c0a00; box-shadow: 3px 3px 0 #1c0a00;">
                aight let's gooo!! &gt;&gt;
            </button>

        </div>
    </div>
</div>

{{-- Rules modal (shown after profile, before START) --}}
<div id="rules-modal" class="hidden fixed inset-0 bg-black/75 flex items-start justify-center z-50 p-4 overflow-y-auto">
    <div class="bg-white max-w-sm w-full pixel-card border-2 border-amber-900 my-6" style="box-shadow: 5px 5px 0 #1c0a00;">
        <div class="bg-amber-800 px-5 py-4 border-b-2 border-amber-950 text-center">
            <div class="inline-flex items-center justify-center w-10 h-10 bg-amber-700 border-2 border-amber-600 mb-2">
                <i data-lucide="scroll-text" class="w-5 h-5 text-amber-200"></i>
            </div>
            <h2 class="text-[11px] font-bold text-white">the rules 📜</h2>
            <p class="text-amber-400 text-[10px] mt-1 leading-loose">read before you smash that keyboard</p>
        </div>
        <div class="p-5">
            <ul class="text-left space-y-3 mb-5">
                <li class="flex items-start gap-2.5 text-[10px] text-amber-800 leading-loose">
                    <i data-lucide="x-circle" class="w-4 h-4 text-red-500 shrink-0 mt-0.5"></i>
                    You cannot use your backspace button.
                </li>
                <li class="flex items-start gap-2.5 text-[10px] text-amber-800 leading-loose">
                    <i data-lucide="lock" class="w-4 h-4 text-amber-600 shrink-0 mt-0.5"></i>
                    All final score calculations are finalized and cannot be altered by any means.
                </li>
                @if($challenge->allow_retry_next_day || $challenge->require_unique_email)
                <li class="flex items-start gap-2.5 text-[10px] text-amber-800 leading-loose">
                    <i data-lucide="calendar-heart" class="w-4 h-4 text-green-600 shrink-0 mt-0.5"></i>
                    You may come back and challenge us again tomorrow 😉
                </li>
                @endif
            </ul>
            <p class="text-center text-[10px] text-amber-700 font-bold mb-5 leading-loose">HAVE FUN AND GOODLUCK! 🍀</p>
            <button type="button" onclick="dismissRules()"
                class="w-full bg-amber-800 text-amber-100 text-[10px] font-bold py-3.5 pixel-btn hover:bg-amber-700 transition-colors"
                style="border-color: #1c0a00; box-shadow: 3px 3px 0 #1c0a00;">
                i'm ready! &gt;&gt;
            </button>
        </div>
    </div>
</div>

{{-- Main game --}}
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
const START_URL         = '{{ route('challenge.start') }}';
const SUBMIT_URL        = '{{ route('challenge.submit') }}';
const SAVE_PROFILE_URL  = '{{ route('challenge.save-profile') }}';
const CSRF              = document.querySelector('meta[name=csrf-token]').content;
const TEXT_CONTENT      = @json($text->content);

// ─── Country picker ───────────────────────────────────────────────────────────

const COUNTRIES = [
    {f:'🇲🇾',n:'Malaysia',c:'+60'},
    {f:'🇦🇫',n:'Afghanistan',c:'+93'},
    {f:'🇦🇱',n:'Albania',c:'+355'},
    {f:'🇩🇿',n:'Algeria',c:'+213'},
    {f:'🇦🇴',n:'Angola',c:'+244'},
    {f:'🇦🇷',n:'Argentina',c:'+54'},
    {f:'🇦🇲',n:'Armenia',c:'+374'},
    {f:'🇦🇺',n:'Australia',c:'+61'},
    {f:'🇦🇹',n:'Austria',c:'+43'},
    {f:'🇦🇿',n:'Azerbaijan',c:'+994'},
    {f:'🇧🇭',n:'Bahrain',c:'+973'},
    {f:'🇧🇩',n:'Bangladesh',c:'+880'},
    {f:'🇧🇪',n:'Belgium',c:'+32'},
    {f:'🇧🇴',n:'Bolivia',c:'+591'},
    {f:'🇧🇷',n:'Brazil',c:'+55'},
    {f:'🇧🇳',n:'Brunei',c:'+673'},
    {f:'🇧🇬',n:'Bulgaria',c:'+359'},
    {f:'🇰🇭',n:'Cambodia',c:'+855'},
    {f:'🇨🇲',n:'Cameroon',c:'+237'},
    {f:'🇨🇦',n:'Canada',c:'+1'},
    {f:'🇨🇱',n:'Chile',c:'+56'},
    {f:'🇨🇳',n:'China',c:'+86'},
    {f:'🇨🇴',n:'Colombia',c:'+57'},
    {f:'🇭🇷',n:'Croatia',c:'+385'},
    {f:'🇨🇿',n:'Czech Republic',c:'+420'},
    {f:'🇩🇰',n:'Denmark',c:'+45'},
    {f:'🇪🇨',n:'Ecuador',c:'+593'},
    {f:'🇪🇬',n:'Egypt',c:'+20'},
    {f:'🇪🇹',n:'Ethiopia',c:'+251'},
    {f:'🇫🇮',n:'Finland',c:'+358'},
    {f:'🇫🇷',n:'France',c:'+33'},
    {f:'🇬🇪',n:'Georgia',c:'+995'},
    {f:'🇩🇪',n:'Germany',c:'+49'},
    {f:'🇬🇭',n:'Ghana',c:'+233'},
    {f:'🇬🇷',n:'Greece',c:'+30'},
    {f:'🇭🇰',n:'Hong Kong',c:'+852'},
    {f:'🇭🇺',n:'Hungary',c:'+36'},
    {f:'🇮🇳',n:'India',c:'+91'},
    {f:'🇮🇩',n:'Indonesia',c:'+62'},
    {f:'🇮🇷',n:'Iran',c:'+98'},
    {f:'🇮🇶',n:'Iraq',c:'+964'},
    {f:'🇮🇪',n:'Ireland',c:'+353'},
    {f:'🇮🇱',n:'Israel',c:'+972'},
    {f:'🇮🇹',n:'Italy',c:'+39'},
    {f:'🇯🇵',n:'Japan',c:'+81'},
    {f:'🇯🇴',n:'Jordan',c:'+962'},
    {f:'🇰🇿',n:'Kazakhstan',c:'+7'},
    {f:'🇰🇪',n:'Kenya',c:'+254'},
    {f:'🇰🇷',n:'South Korea',c:'+82'},
    {f:'🇰🇼',n:'Kuwait',c:'+965'},
    {f:'🇱🇦',n:'Laos',c:'+856'},
    {f:'🇱🇧',n:'Lebanon',c:'+961'},
    {f:'🇲🇴',n:'Macao',c:'+853'},
    {f:'🇲🇩',n:'Moldova',c:'+373'},
    {f:'🇲🇳',n:'Mongolia',c:'+976'},
    {f:'🇲🇦',n:'Morocco',c:'+212'},
    {f:'🇲🇲',n:'Myanmar',c:'+95'},
    {f:'🇳🇵',n:'Nepal',c:'+977'},
    {f:'🇳🇱',n:'Netherlands',c:'+31'},
    {f:'🇳🇿',n:'New Zealand',c:'+64'},
    {f:'🇳🇬',n:'Nigeria',c:'+234'},
    {f:'🇳🇴',n:'Norway',c:'+47'},
    {f:'🇴🇲',n:'Oman',c:'+968'},
    {f:'🇵🇰',n:'Pakistan',c:'+92'},
    {f:'🇵🇸',n:'Palestine',c:'+970'},
    {f:'🇵🇾',n:'Paraguay',c:'+595'},
    {f:'🇵🇪',n:'Peru',c:'+51'},
    {f:'🇵🇭',n:'Philippines',c:'+63'},
    {f:'🇵🇱',n:'Poland',c:'+48'},
    {f:'🇵🇹',n:'Portugal',c:'+351'},
    {f:'🇶🇦',n:'Qatar',c:'+974'},
    {f:'🇷🇴',n:'Romania',c:'+40'},
    {f:'🇷🇺',n:'Russia',c:'+7'},
    {f:'🇸🇦',n:'Saudi Arabia',c:'+966'},
    {f:'🇸🇳',n:'Senegal',c:'+221'},
    {f:'🇷🇸',n:'Serbia',c:'+381'},
    {f:'🇸🇬',n:'Singapore',c:'+65'},
    {f:'🇿🇦',n:'South Africa',c:'+27'},
    {f:'🇪🇸',n:'Spain',c:'+34'},
    {f:'🇱🇰',n:'Sri Lanka',c:'+94'},
    {f:'🇸🇩',n:'Sudan',c:'+249'},
    {f:'🇸🇪',n:'Sweden',c:'+46'},
    {f:'🇨🇭',n:'Switzerland',c:'+41'},
    {f:'🇸🇾',n:'Syria',c:'+963'},
    {f:'🇹🇼',n:'Taiwan',c:'+886'},
    {f:'🇹🇿',n:'Tanzania',c:'+255'},
    {f:'🇹🇭',n:'Thailand',c:'+66'},
    {f:'🇹🇳',n:'Tunisia',c:'+216'},
    {f:'🇹🇷',n:'Turkey',c:'+90'},
    {f:'🇺🇬',n:'Uganda',c:'+256'},
    {f:'🇺🇦',n:'Ukraine',c:'+380'},
    {f:'🇦🇪',n:'UAE',c:'+971'},
    {f:'🇬🇧',n:'United Kingdom',c:'+44'},
    {f:'🇺🇸',n:'United States',c:'+1'},
    {f:'🇺🇾',n:'Uruguay',c:'+598'},
    {f:'🇺🇿',n:'Uzbekistan',c:'+998'},
    {f:'🇻🇪',n:'Venezuela',c:'+58'},
    {f:'🇻🇳',n:'Vietnam',c:'+84'},
    {f:'🇾🇪',n:'Yemen',c:'+967'},
    {f:'🇿🇲',n:'Zambia',c:'+260'},
    {f:'🇿🇼',n:'Zimbabwe',c:'+263'},
];

let cpCode = '+60';

function renderCpList(list) {
    document.getElementById('cp-list').innerHTML = list.map(c =>
        `<button type="button" onclick="pickCountry('${c.c}','${c.f}')"
            class="w-full flex items-center gap-2 px-3 py-2 text-[10px] hover:bg-amber-50 text-left font-[inherit]">
            <span>${c.f}</span>
            <span class="font-bold text-amber-800 flex-shrink-0">${c.c}</span>
            <span class="text-amber-600 truncate">${c.n}</span>
        </button>`
    ).join('');
}

function filterCp(q) {
    const ql = q.toLowerCase();
    renderCpList(ql ? COUNTRIES.filter(c => c.n.toLowerCase().includes(ql) || c.c.includes(ql)) : COUNTRIES);
}

function pickCountry(code, flag) {
    cpCode = code;
    document.getElementById('cp-flag').textContent = flag;
    document.getElementById('cp-code').textContent = code;
    document.getElementById('cp-dd').classList.add('hidden');
}

function toggleCpDd(e) {
    e.stopPropagation();
    const dd = document.getElementById('cp-dd');
    const wasHidden = dd.classList.contains('hidden');
    dd.classList.toggle('hidden');
    if (wasHidden) {
        document.getElementById('cp-search').value = '';
        renderCpList(COUNTRIES);
        setTimeout(() => document.getElementById('cp-search').focus(), 50);
    }
}

document.addEventListener('click', function(e) {
    if (!document.getElementById('cp-wrap').contains(e.target)) {
        document.getElementById('cp-dd').classList.add('hidden');
    }
});

// ─── Profile form ─────────────────────────────────────────────────────────────

async function submitProfile() {
    const name     = document.getElementById('p-fullname').value.trim();
    const phone    = document.getElementById('p-phone').value.trim();
    const referral = document.querySelector('input[name="referral_source"]:checked')?.value;

    document.getElementById('err-name').classList.toggle('hidden', !!name);
    document.getElementById('err-phone').classList.toggle('hidden', !!phone);
    document.getElementById('err-referral').classList.toggle('hidden', !!referral);
    if (!name || !phone || !referral) return;

    const btn = document.getElementById('profile-btn');
    btn.disabled = true;
    btn.textContent = 'saving ur deets... ⏳';

    try {
        const res  = await fetch(SAVE_PROFILE_URL, {
            method: 'POST',
            headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF},
            body: JSON.stringify({full_name: name, phone: cpCode + phone, referral_source: referral}),
        });
        const data = await res.json();

        if (data.success) {
            document.getElementById('profile-modal').classList.add('hidden');
            document.getElementById('rules-modal').classList.remove('hidden');
            lucide.createIcons();
        } else {
            btn.disabled = false;
            btn.textContent = "aight let's gooo!! >>";
            alert(data.message || 'something went sideways 😭 try again!');
        }
    } catch (e) {
        btn.disabled = false;
        btn.textContent = "aight let's gooo!! >>";
        alert('network error bestie 😭 refresh and try again');
    }
}

function dismissRules() {
    document.getElementById('rules-modal').classList.add('hidden');
}

// Prefill existing player data
(function init() {
    const existingName     = @json($player->full_name ?? '');
    const existingReferral = @json($player->referral_source ?? '');

    if (existingName) document.getElementById('p-fullname').value = existingName;

    if (existingReferral) {
        const r = document.querySelector(`input[name="referral_source"][value="${existingReferral}"]`);
        if (r) r.checked = true;
    }
})();

// ─── Game ─────────────────────────────────────────────────────────────────────

let attemptId    = null;
let chars        = TEXT_CONTENT.split('');
let typedIndex   = 0;
let mistakes     = 0;
let startTime    = null;
let timerInterval = null;
let completed    = false;
let typedRecord  = [];

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
        const res  = await fetch(START_URL, {
            method: 'POST',
            headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF},
            body: JSON.stringify({device_fingerprint: fp}),
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

        startTime     = Date.now();
        timerInterval = setInterval(() => {
            const elapsed = ((Date.now() - startTime) / 1000).toFixed(0);
            document.getElementById('stat-time').textContent = elapsed + 's';
            document.getElementById('stat-wpm').textContent  = calcWpm();
            document.getElementById('stat-accuracy').textContent = calcAccuracy() + '%';
        }, 300);

    } catch (e) {
        alert('Network error. Please refresh.');
    }
}

document.getElementById('hidden-input').addEventListener('input', function(e) {
    if (completed || !attemptId) return;
    const val = e.target.value;
    const c   = val[val.length - 1];
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

// Enforce "no copy/paste or auto-typing" — block paste & drop on the typing field.
['paste', 'drop'].forEach(evt =>
    document.getElementById('hidden-input').addEventListener(evt, e => e.preventDefault())
);

async function submitResult() {
    completed = true;
    clearInterval(timerInterval);
    const duration  = (Date.now() - startTime) / 1000;
    const userInput = chars.map((c, i) => typedRecord[i] ? c : '_').join('');

    document.getElementById('game-area').classList.add('hidden');
    document.getElementById('submitting-overlay').classList.remove('hidden');

    const res  = await fetch(SUBMIT_URL, {
        method: 'POST',
        headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF},
        body: JSON.stringify({
            attempt_id:       attemptId,
            user_input:       userInput,
            duration_seconds: duration.toFixed(3),
        }),
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
