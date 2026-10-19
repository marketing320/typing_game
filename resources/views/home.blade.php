@extends('layouts.app')

@section('title', 'Find Your Flow')

@section('content')
<div class="home-shell">
    <section class="hero" aria-labelledby="hero-title">
        <div>
            <span class="eyebrow"><span class="status-dot"></span> Small keys. Big potential.</span>
            <h1 id="hero-title">Fast fingers.<br><em>Big energy.</em></h1>
            <p class="hero-description">Get in the zone. Find your rhythm. Turn your everyday typing into a leaderboard moment.</p>
            <div class="hero-actions">
                <a href="{{ route('rehearsal.index') }}" class="ui-button button-dark">Let's warm up <i data-lucide="arrow-up-right" class="w-4 h-4" aria-hidden="true"></i></a>
                <a href="{{ route('challenge.access') }}" class="ui-button button-outline">Take the challenge <i data-lucide="zap" class="w-4 h-4" aria-hidden="true"></i></a>
            </div>
            <p class="hero-footnote"><i data-lucide="check" aria-hidden="true"></i> Free practice. No sign-up. Just you and the keys.</p>
        </div>
        <div class="keyboard-art" aria-hidden="true">
            <span class="art-label">YOU &times; YOUR KEYBOARD</span>
            <span class="art-orbit"></span><span class="art-orbit"></span>
            <div class="keyboard-board">
                <span class="keycap">T</span><span class="keycap">Y</span><span class="keycap">P</span><span class="keycap keycap-accent">E</span>
                <span class="keycap keycap-wide">FIND YOUR FLOW</span><span class="keycap">&nearr;</span>
            </div>
            <span class="art-sticker">main character speed</span>
            <i data-lucide="sparkles" class="art-star"></i>
            <span class="art-caption"><i data-lucide="headphones"></i> Zone in. Type it out.</span>
        </div>
    </section>

    <section aria-labelledby="modes-title">
        <div class="section-heading">
            <h2 id="modes-title">Pick your pace.</h2>
            <p>A chill warm-up or a little friendly competition?</p>
        </div>
        <div class="mode-grid">
            <a href="{{ route('rehearsal.index') }}" class="mode-card">
                <div class="mode-top"><span class="mode-icon"><i data-lucide="command" aria-hidden="true"></i></span><span class="pill">No pressure &middot; No sign-up</span></div>
                <h3>Practice mode</h3>
                <p>Find your rhythm, build your speed, and chase your personal best. Run it back as often as you like.</p>
                <div class="mode-bottom"><span>Start practicing</span><span class="mode-arrow"><i data-lucide="arrow-up-right" aria-hidden="true"></i></span></div>
            </a>
            <a href="{{ route('challenge.access') }}" class="mode-card mode-challenge">
                <div class="mode-top"><span class="mode-icon"><i data-lucide="zap" aria-hidden="true"></i></span><span class="pill"><i data-lucide="{{ $activeChallenge ? 'radio' : 'shield-check' }}" aria-hidden="true"></i>{{ $activeChallenge ? 'Challenge live' : 'Verified entry' }}</span></div>
                <h3>Challenge mode</h3>
                <p>Bring your best to the official challenge. Verify your email, make your attempt, and see where you land.
                    @if($activeChallenge)<span class="live-challenge">{{ $activeChallenge->title }}</span>@endif
                </p>
                <div class="mode-bottom"><span>Enter the challenge</span><span class="mode-arrow"><i data-lucide="arrow-up-right" aria-hidden="true"></i></span></div>
            </a>
        </div>
    </section>

</div>
@endsection
