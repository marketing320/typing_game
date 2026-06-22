@extends('layouts.app')

@section('title', 'Challenge Access')

@section('content')
<div class="max-w-md mx-auto px-4 py-12">
    <div class="text-center mb-8">
        <div class="inline-flex items-center justify-center w-16 h-16 bg-amber-100 border-2 border-amber-300 mb-4">
            <i data-lucide="trophy" class="w-8 h-8 text-amber-700"></i>
        </div>
        <h1 class="text-sm font-bold text-amber-900">Challenge Mode</h1>
        <p class="text-amber-600 text-[10px] mt-3 leading-loose">One shot at the top. Make it count.</p>
    </div>

    <!-- Before you start -->
    <div class="bg-white pixel-card p-5 mb-6">
        <h2 class="text-[11px] font-bold text-amber-900 mb-4 flex items-center gap-2">
            <i data-lucide="scroll-text" class="w-4 h-4 text-amber-700"></i> Before You Start
        </h2>
        <ul class="space-y-2.5 mb-4">
            <li class="flex items-start gap-2 text-[10px] text-amber-800 leading-loose"><i data-lucide="check" class="w-3.5 h-3.5 text-amber-600 shrink-0 mt-0.5"></i> One official attempt only</li>
            <li class="flex items-start gap-2 text-[10px] text-amber-800 leading-loose"><i data-lucide="check" class="w-3.5 h-3.5 text-amber-600 shrink-0 mt-0.5"></i> Type as fast and accurately as possible</li>
            <li class="flex items-start gap-2 text-[10px] text-amber-800 leading-loose"><i data-lucide="check" class="w-3.5 h-3.5 text-amber-600 shrink-0 mt-0.5"></i> Your score depends on WPM and accuracy</li>
            <li class="flex items-start gap-2 text-[10px] text-amber-800 leading-loose"><i data-lucide="check" class="w-3.5 h-3.5 text-amber-600 shrink-0 mt-0.5"></i> Your username will appear on the leaderboard</li>
            <li class="flex items-start gap-2 text-[10px] text-amber-800 leading-loose"><i data-lucide="x" class="w-3.5 h-3.5 text-red-500 shrink-0 mt-0.5"></i> Copy/paste or auto-typing is not allowed</li>
        </ul>
        <div class="bg-amber-50 border-2 border-amber-200 px-3 py-2.5 text-[10px] text-amber-700 leading-loose">
            <strong class="text-amber-900">How scoring works:</strong> Your final score is based on typing speed and accuracy. Typing fast helps, but mistakes will reduce your result. Remember no Backspacing allowed!
        </div>
    </div>

    <!-- Geolocation status -->
    <div id="geo-status" class="mb-6 hidden">
        <div id="geo-checking" class="bg-blue-50 border-2 border-blue-200 px-4 py-3 text-[10px] text-blue-700 flex items-center gap-2">
            <svg class="w-4 h-4 animate-spin shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
            </svg>
            Checking your location...
        </div>
        <div id="geo-blocked" class="hidden bg-red-50 border-2 border-red-300 px-4 py-3 text-[10px] text-red-700 flex items-start gap-2">
            <i data-lucide="alert-triangle" class="w-4 h-4 shrink-0 mt-0.5"></i>
            <div><strong>Location Blocked</strong><br><span id="geo-message">You are outside the allowed area.</span></div>
        </div>
        <div id="geo-allowed" class="hidden bg-green-50 border-2 border-green-300 px-4 py-3 text-[10px] text-green-700 flex items-center gap-2">
            <i data-lucide="check-circle-2" class="w-4 h-4 shrink-0"></i>
            Location verified. You may proceed.
        </div>
        <div id="geo-denied" class="hidden bg-yellow-50 border-2 border-yellow-300 px-4 py-3 text-[10px] text-yellow-700 flex items-center gap-2">
            <i data-lucide="alert-triangle" class="w-4 h-4 shrink-0"></i>
            Location access denied. Please allow location in your browser to continue.
        </div>
    </div>

    <!-- Form -->
    <div id="form-container" class="bg-white pixel-card p-6">
        <form id="access-form" class="space-y-5">
            @csrf
            <div>
                <label class="block text-[10px] font-bold text-amber-800 mb-1.5">Email Address</label>
                <p class="text-[10px] text-amber-500 mb-2 leading-loose">We'll send your verification code here. Your email will not be shown publicly.</p>
                <input type="email" id="input-email" name="email" required
                    class="w-full border-2 border-amber-300 px-4 py-3 focus:outline-none focus:border-amber-600 text-xs font-[inherit] bg-amber-50"
                    placeholder="your@email.com">
            </div>
            <div>
                <label class="block text-[10px] font-bold text-amber-800 mb-1.5">Username</label>
                <p class="text-[10px] text-amber-500 mb-2 leading-loose">This name will appear on the leaderboard.</p>
                <input type="text" id="input-username" name="username" required maxlength="64"
                    class="w-full border-2 border-amber-300 px-4 py-3 focus:outline-none focus:border-amber-600 text-xs font-[inherit] bg-amber-50"
                    placeholder="MonkeyTyper99">
            </div>
            <div id="form-error" class="hidden text-red-600 text-[10px] font-medium flex items-center gap-1.5">
                <i data-lucide="alert-circle" class="w-4 h-4 shrink-0"></i>
                <span id="form-error-text"></span>
            </div>
            <button type="submit" id="submit-btn"
                class="w-full bg-amber-700 text-white text-xs font-bold py-3 pixel-btn hover:bg-amber-800 transition-colors flex items-center justify-center gap-2"
                style="box-shadow: 3px 3px 0 #1c0a00; border-color: #451a03;">
                Send OTP <i data-lucide="send" class="w-4 h-4"></i>
            </button>
        </form>
    </div>

    <p class="text-center text-[10px] text-amber-500 mt-4 flex items-center justify-center gap-1.5 leading-loose">
        <i data-lucide="mail" class="w-3.5 h-3.5"></i>
        You will receive a one-time password via email to verify your identity.
    </p>
    @if($requireGeofence)
    <p class="text-center text-[10px] text-amber-500 mt-3 flex items-start justify-center gap-1.5 leading-loose">
        <i data-lucide="map-pin" class="w-3.5 h-3.5 shrink-0 mt-0.5"></i>
        Location access is only used to confirm you are inside the official challenge area. Your location will not be displayed publicly.
    </p>
    @endif
</div>

<!-- Geo block modal -->
<div id="geo-modal" class="hidden fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4">
    <div class="bg-white p-8 max-w-sm w-full text-center pixel-card">
        <div class="inline-flex items-center justify-center w-14 h-14 bg-red-100 border-2 border-red-300 mb-4">
            <i data-lucide="map-pin-off" class="w-7 h-7 text-red-600"></i>
        </div>
        <h2 class="text-xs font-bold text-red-700 mb-3">Outside Challenge Area</h2>
        <p id="modal-message" class="text-gray-600 text-[10px] mb-6 leading-loose">You are outside the allowed event area.</p>
        <a href="{{ route('rehearsal.index') }}" class="flex items-center justify-center gap-2 bg-amber-100 text-amber-800 text-[10px] font-bold py-3 pixel-btn hover:bg-amber-200 transition-colors">
            Try Practice Mode instead <i data-lucide="arrow-right" class="w-4 h-4"></i>
        </a>
    </div>
</div>

<!-- Email already used modal -->
<div id="email-modal" class="hidden fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4">
    <div class="bg-white p-8 max-w-sm w-full text-center pixel-card">
        <div class="inline-flex items-center justify-center w-14 h-14 bg-amber-100 border-2 border-amber-300 mb-4">
            <i data-lucide="mail-warning" class="w-7 h-7 text-amber-600"></i>
        </div>
        <h2 class="text-xs font-bold text-amber-800 mb-3">Email Already Used</h2>
        <p id="email-used-message" class="text-gray-600 text-[10px] mb-6 leading-loose">This email has already been used for today's challenge. Come back tomorrow for another shot!</p>
        <div class="space-y-2">
            <a href="{{ route('leaderboard.index') }}" class="flex items-center justify-center gap-2 bg-amber-700 text-white text-[10px] font-bold py-3 pixel-btn hover:bg-amber-800 transition-colors" style="box-shadow: 3px 3px 0 #1c0a00; border-color: #451a03;">
                See the Leaderboard <i data-lucide="award" class="w-4 h-4"></i>
            </a>
            <a href="{{ route('rehearsal.index') }}" class="flex items-center justify-center gap-2 bg-amber-100 text-amber-800 text-[10px] font-bold py-3 pixel-btn hover:bg-amber-200 transition-colors">
                Try Practice Mode instead <i data-lucide="arrow-right" class="w-4 h-4"></i>
            </a>
            <button type="button" onclick="document.getElementById('email-modal').classList.add('hidden')" class="w-full text-amber-500 text-[10px] py-2 hover:text-amber-700 transition-colors">
                Use a different email
            </button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const CHECK_LOC_URL = '{{ route('challenge.check-location') }}';
const REQUEST_OTP_URL = '{{ route('challenge.request-otp') }}';
const OTP_PAGE = '{{ route('challenge.otp') }}';
const CSRF = document.querySelector('meta[name=csrf-token]').content;
const REQUIRE_GEOFENCE = {{ $requireGeofence ? 'true' : 'false' }};

let geoAllowed = !REQUIRE_GEOFENCE; // pre-allowed only when geofence is not required

function checkGeolocation() {
    document.getElementById('geo-status').classList.remove('hidden');
    document.getElementById('geo-checking').classList.remove('hidden');
    lucide.createIcons();

    if (!navigator.geolocation) {
        document.getElementById('geo-checking').classList.add('hidden');
        document.getElementById('geo-denied').classList.remove('hidden');
        lucide.createIcons();
        geoAllowed = false;
        return;
    }

    navigator.geolocation.getCurrentPosition(
        (pos) => {
            fetch(CHECK_LOC_URL, {
                method: 'POST',
                headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF},
                body: JSON.stringify({latitude: pos.coords.latitude, longitude: pos.coords.longitude})
            })
            .then(r => r.json())
            .then(data => {
                document.getElementById('geo-checking').classList.add('hidden');
                if (data.allowed) {
                    document.getElementById('geo-allowed').classList.remove('hidden');
                    geoAllowed = true;
                } else {
                    geoAllowed = false;
                    document.getElementById('geo-blocked').classList.remove('hidden');
                    document.getElementById('geo-message').textContent = data.message;
                    document.getElementById('modal-message').textContent = data.message;
                    document.getElementById('geo-modal').classList.remove('hidden');
                }
                lucide.createIcons();
            });
        },
        () => {
            // User denied browser location permission
            document.getElementById('geo-checking').classList.add('hidden');
            document.getElementById('geo-denied').classList.remove('hidden');
            lucide.createIcons();
            geoAllowed = false;
        },
        {timeout: 10000}
    );
}

document.getElementById('access-form').addEventListener('submit', async function (e) {
    e.preventDefault();
    if (!geoAllowed) return;

    const btn = document.getElementById('submit-btn');
    const errEl = document.getElementById('form-error');
    btn.disabled = true;
    btn.innerHTML = '<svg class="w-4 h-4 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg> Sending...';
    errEl.classList.add('hidden');

    const email = document.getElementById('input-email').value;
    const username = document.getElementById('input-username').value;

    try {
        const res = await fetch(REQUEST_OTP_URL, {
            method: 'POST',
            headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF},
            body: JSON.stringify({email, username})
        });
        const data = await res.json();

        if (data.success) {
            window.location.href = OTP_PAGE;
        } else if (data.code === 'email_used_today') {
            document.getElementById('email-used-message').textContent = data.message;
            document.getElementById('email-modal').classList.remove('hidden');
            btn.disabled = false;
            btn.innerHTML = 'Send OTP <i data-lucide="send" class="w-4 h-4"></i>';
            lucide.createIcons();
        } else {
            document.getElementById('form-error-text').textContent = data.message;
            errEl.classList.remove('hidden');
            lucide.createIcons();
            btn.disabled = false;
            btn.innerHTML = 'Send OTP <i data-lucide="send" class="w-4 h-4"></i>';
            lucide.createIcons();
        }
    } catch {
        document.getElementById('form-error-text').textContent = 'Network error. Please try again.';
        errEl.classList.remove('hidden');
        lucide.createIcons();
        btn.disabled = false;
        btn.innerHTML = 'Send OTP <i data-lucide="send" class="w-4 h-4"></i>';
        lucide.createIcons();
    }
});

if (REQUIRE_GEOFENCE) checkGeolocation();
</script>
@endpush
