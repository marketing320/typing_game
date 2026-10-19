@extends('layouts.app')

@section('title', 'Typing Practice and Challenges')

@section('content')
<div class="home-shell">
    <section class="hero" aria-labelledby="hero-title">
        <div>
            <span class="eyebrow"><span class="status-dot"></span> Practice and compete</span>
            <h1 id="hero-title">Test your typing<br><em>speed and accuracy.</em></h1>
            <p class="hero-description">Practice with short texts or enter the official challenge. Your results show how fast and accurately you type.</p>
            <div class="hero-actions">
                <a href="{{ route('rehearsal.index') }}" class="ui-button button-dark">Start practice <i data-lucide="arrow-up-right" class="w-4 h-4" aria-hidden="true"></i></a>
                <a href="{{ route('challenge.access') }}" class="ui-button button-outline">Take the challenge <i data-lucide="zap" class="w-4 h-4" aria-hidden="true"></i></a>
            </div>
            <p class="hero-footnote"><i data-lucide="check" aria-hidden="true"></i> Practice is free. No account needed.</p>
        </div>
        <div class="keyboard-art" aria-hidden="true">
            <span class="art-label">YOU &times; YOUR KEYBOARD</span>
            <span class="art-orbit"></span><span class="art-orbit"></span>
            <div class="keyboard-board">
                <span class="keycap">T</span><span class="keycap">Y</span><span class="keycap">P</span><span class="keycap keycap-accent">E</span>
                <span class="keycap keycap-wide">FIND YOUR FLOW</span><span class="keycap">&nearr;</span>
            </div>
            <span class="art-sticker">SPEED + ACCURACY</span>
            <i data-lucide="sparkles" class="art-star"></i>
            <span class="art-caption"><i data-lucide="headphones"></i> Practice at your own pace</span>
        </div>
    </section>

    <section aria-labelledby="modes-title">
        <div class="section-heading">
            <h2 id="modes-title">Choose a mode</h2>
            <p>Warm up with practice or enter the challenge.</p>
        </div>
        <div class="mode-grid">
            <a href="{{ route('rehearsal.index') }}" class="mode-card">
                <div class="mode-top"><span class="mode-icon"><i data-lucide="command" aria-hidden="true"></i></span><span class="pill">No sign-up</span></div>
                <h3>Practice mode</h3>
                <p>Type a short passage and check your speed and accuracy. Repeat it or try a different text.</p>
                <div class="mode-bottom"><span>Start practicing</span><span class="mode-arrow"><i data-lucide="arrow-up-right" aria-hidden="true"></i></span></div>
            </a>
            <a href="{{ route('challenge.access') }}" class="mode-card mode-challenge">
                <div class="mode-top"><span class="mode-icon"><i data-lucide="zap" aria-hidden="true"></i></span><span class="pill"><i data-lucide="{{ $activeChallenge ? 'radio' : 'shield-check' }}" aria-hidden="true"></i>{{ $activeChallenge ? 'Challenge live' : 'Verified entry' }}</span></div>
                <h3>Challenge mode</h3>
                <p>Verify your email, complete your attempt, and compare your score on the leaderboard.
                    @if($activeChallenge)<span class="live-challenge">{{ $activeChallenge->title }}</span>@endif
                </p>
                <div class="mode-bottom"><span>Enter the challenge</span><span class="mode-arrow"><i data-lucide="arrow-up-right" aria-hidden="true"></i></span></div>
            </a>
        </div>
    </section>

</div>
@endsection
