@extends('layouts.app')

@section('title', 'Home')

@section('content')
<div class="max-w-2xl mx-auto px-4 py-10">

    <!-- Hero -->
    <div class="text-center mb-10">
        <div class="inline-flex items-center justify-center w-20 h-20 bg-amber-100 mb-6 pixel-card-light">
            <i data-lucide="keyboard" class="w-10 h-10 text-amber-700"></i>
        </div>
        <h1 class="text-lg font-bold text-amber-900 mb-4">Typing Game</h1>
        <p class="text-amber-700 text-xs leading-loose">Test your speed. Prove your accuracy. Claim the top spot.</p>
    </div>

    <!-- Mode cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">

        <!-- Rehearsal Mode -->
        <a href="{{ route('rehearsal.index') }}"
           class="group bg-white p-8 pixel-card block text-center hover:bg-amber-50 transition-colors">
            <div class="inline-flex items-center justify-center w-14 h-14 bg-amber-50 mb-5 border-2 border-amber-200">
                <i data-lucide="target" class="w-7 h-7 text-amber-600"></i>
            </div>
            <h2 class="text-xs font-bold text-amber-800 mb-3">Rehearsal Mode</h2>
            <p class="text-amber-600 text-[10px] mb-6 leading-loose">Practice freely. No registration needed. Sharpen your skills.</p>
            <span class="inline-flex items-center gap-1.5 bg-amber-100 text-amber-800 text-[10px] font-bold px-5 py-2.5 pixel-btn">
                Start Practicing <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
            </span>
        </a>

        <!-- Challenge Mode -->
        <a href="{{ route('challenge.access') }}"
           class="group bg-amber-800 p-8 border-2 border-amber-950 pixel-card block text-center hover:bg-amber-750 transition-colors"
           style="box-shadow: 4px 4px 0 #1c0a00;">
            <div class="inline-flex items-center justify-center w-14 h-14 bg-amber-700 mb-5 border-2 border-amber-600">
                <i data-lucide="trophy" class="w-7 h-7 text-amber-200"></i>
            </div>
            <h2 class="text-xs font-bold text-amber-100 mb-3">Challenge Mode</h2>
            <p class="text-amber-300 text-[10px] mb-6 leading-loose">
                @if($activeChallenge)
                    <span class="inline-flex items-center gap-1 bg-green-500 text-white text-[10px] px-2 py-0.5 mb-1">
                        <i data-lucide="radio" class="w-3 h-3"></i> LIVE
                    </span><br>
                    {{ $activeChallenge->title }}
                @else
                    Official challenge. Verify your email. One shot at glory.
                @endif
            </p>
            <span class="inline-flex items-center gap-1.5 bg-amber-600 text-white text-[10px] font-bold px-5 py-2.5"
                  style="border: 2px solid #451a03; box-shadow: 3px 3px 0 #1c0a00;">
                Enter Challenge <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
            </span>
        </a>

    </div>

    <!-- How it works -->
    <div class="mt-10 bg-white p-6 pixel-card">
        <h3 class="font-bold text-amber-800 text-xs mb-6 text-center">[ HOW CHALLENGE MODE WORKS ]</h3>
        <ol class="space-y-5 text-[10px] text-amber-700 leading-loose">
            <li class="flex items-start gap-4">
                <span class="inline-flex items-center justify-center w-6 h-6 bg-amber-800 text-amber-100 text-[10px] font-bold shrink-0">1</span>
                Enter your email and username
            </li>
            <li class="flex items-start gap-4">
                <span class="inline-flex items-center justify-center w-6 h-6 bg-amber-800 text-amber-100 text-[10px] font-bold shrink-0">2</span>
                Verify with the OTP sent to your inbox
            </li>
            <li class="flex items-start gap-4">
                <span class="inline-flex items-center justify-center w-6 h-6 bg-amber-800 text-amber-100 text-[10px] font-bold shrink-0">3</span>
                Type the challenge text as fast and accurately as possible
            </li>
            <li class="flex items-start gap-4">
                <span class="inline-flex items-center justify-center w-6 h-6 bg-amber-800 text-amber-100 text-[10px] font-bold shrink-0">4</span>
                Your score is calculated and posted to the leaderboard
            </li>
        </ol>
    </div>

</div>
@endsection
