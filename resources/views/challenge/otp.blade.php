@extends('layouts.app')

@section('title', 'Verify OTP')

@section('content')
<div class="max-w-md mx-auto px-4 py-12">
    <div class="text-center mb-8">
        <div class="inline-flex items-center justify-center w-16 h-16 bg-amber-100 border-2 border-amber-300 mb-4">
            <i data-lucide="mail" class="w-8 h-8 text-amber-700"></i>
        </div>
        <h1 class="text-sm font-bold text-amber-900">Check Your Email</h1>
        <p class="text-amber-600 text-[10px] mt-3 leading-loose">Enter the 6-digit code we sent to <strong>{{ session('challenge_email') }}</strong></p>
    </div>

    {{-- @if(app()->environment('local') && session('dev_otp'))
    <div class="mb-5 bg-amber-950 border-2 border-amber-700 px-4 py-3 text-xs">
        <div class="flex items-center gap-2 text-amber-400 font-bold mb-2">
            <i data-lucide="terminal" class="w-4 h-4 shrink-0"></i>
            DEV MODE — OTP Bypass
        </div>
        <p class="text-amber-300 text-[10px] mb-3 leading-loose">Mailer not required in local environment. Your OTP:</p>
        <div class="flex items-center gap-3">
            <span class="font-[inherit] text-base font-bold tracking-widest text-white bg-amber-900 border-2 border-amber-700 px-4 py-2">{{ session('dev_otp') }}</span>
            <button onclick="fillDevOtp('{{ session('dev_otp') }}')"
                class="flex items-center gap-1.5 text-[10px] bg-amber-700 hover:bg-amber-600 text-white px-3 py-2 pixel-btn transition-colors">
                <i data-lucide="copy-check" class="w-3.5 h-3.5"></i> Auto-fill
            </button>
        </div>
    </div>
    @endif--}}

    <div class="bg-white pixel-card p-6">
        <div id="otp-error" class="hidden mb-4 bg-red-50 border-2 border-red-300 text-red-600 px-4 py-3 text-[10px] flex items-center gap-2">
            <i data-lucide="alert-circle" class="w-4 h-4 shrink-0"></i>
            <span id="otp-error-text"></span>
        </div>

        <div class="flex gap-2 justify-center mb-6">
            @for ($i = 0; $i < 6; $i++)
            <input type="text" maxlength="1" inputmode="numeric" pattern="[0-9]"
                class="otp-digit w-11 h-14 text-center text-base font-bold border-2 border-amber-300 focus:outline-none focus:border-amber-600 bg-amber-50 font-[inherit]"
                autocomplete="off">
            @endfor
        </div>

        <button id="verify-btn" onclick="verifyOtp()"
            class="w-full bg-amber-700 text-white text-xs font-bold py-3 pixel-btn hover:bg-amber-800 transition-colors flex items-center justify-center gap-2"
            style="box-shadow: 3px 3px 0 #1c0a00; border-color: #451a03;">
            Verify OTP <i data-lucide="shield-check" class="w-5 h-5"></i>
        </button>
    </div>

    <p class="text-center text-[10px] text-amber-500 mt-4 leading-loose">
        Code expires in 10 minutes.
        <a href="{{ route('challenge.access') }}" class="underline inline-flex items-center gap-1">
            <i data-lucide="refresh-cw" class="w-3 h-3"></i> Resend OTP
        </a>
    </p>
</div>
@endsection

@push('scripts')
<script>
const VERIFY_URL = '{{ route('challenge.verify-otp') }}';
const CSRF = document.querySelector('meta[name=csrf-token]').content;

const digits = Array.from(document.querySelectorAll('.otp-digit'));

digits.forEach((el, i) => {
    el.addEventListener('input', () => {
        el.value = el.value.replace(/\D/g, '');
        if (el.value && i < digits.length - 1) {
            digits[i + 1].focus();
        }
    });
    el.addEventListener('keydown', (e) => {
        if (e.key === 'Backspace' && !el.value && i > 0) {
            digits[i - 1].focus();
        }
    });
});

digits[0].focus();

function fillDevOtp(code) {
    code.split('').forEach((ch, i) => {
        if (digits[i]) digits[i].value = ch;
    });
    digits[5].focus();
}

async function verifyOtp() {
    const code = digits.map(d => d.value).join('');
    if (code.length < 6) return;

    const btn = document.getElementById('verify-btn');
    const errEl = document.getElementById('otp-error');
    btn.disabled = true;
    btn.innerHTML = '<svg class="w-4 h-4 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg> Verifying...';
    errEl.classList.add('hidden');

    try {
        const res = await fetch(VERIFY_URL, {
            method: 'POST',
            headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF},
            body: JSON.stringify({otp_code: code})
        });
        const data = await res.json();

        if (data.success && data.redirect) {
            window.location.href = data.redirect;
        } else {
            document.getElementById('otp-error-text').textContent = data.message || 'Verification failed.';
            errEl.classList.remove('hidden');
            lucide.createIcons();
            btn.disabled = false;
            btn.innerHTML = 'Verify OTP <i data-lucide="shield-check" class="w-5 h-5"></i>';
            lucide.createIcons();
            digits.forEach(d => d.value = '');
            digits[0].focus();
        }
    } catch {
        document.getElementById('otp-error-text').textContent = 'Network error. Please try again.';
        errEl.classList.remove('hidden');
        lucide.createIcons();
        btn.disabled = false;
        btn.innerHTML = 'Verify OTP <i data-lucide="shield-check" class="w-5 h-5"></i>';
        lucide.createIcons();
    }
}
</script>
@endpush
