@extends('layouts.app')

@section('title', 'Typing Arcade')

@section('content')
<div class="title-screen">
    <div class="title-sky" aria-hidden="true">
        <span class="arcade-cloud cloud-one"></span><span class="arcade-cloud cloud-two"></span>
        <span class="arcade-cloud cloud-three"></span><span class="arcade-sun"></span>
        <span class="distant-hill hill-one"></span><span class="distant-hill hill-two"></span>
    </div>
    <div class="title-content">
        <p class="title-kicker"><span></span> THE TYPING ARCADE <span></span></p>
        <h1 class="game-wordmark" aria-label="{{ config('app.name') }}">Word<span>Sprint</span><i data-lucide="zap" aria-hidden="true"></i></h1>
        <p class="title-tagline">Ready. Set. Type.</p>
        <div class="title-race" aria-hidden="true">@include('partials.arcade-racer')</div>
        <section class="arcade-menu" aria-labelledby="mode-heading">
            <h2 id="mode-heading">CHOOSE YOUR MODE</h2>
            <div class="arcade-mode-grid">
                <a href="{{ route('rehearsal.index') }}" class="arcade-mode mode-practice">
                    <span class="mode-medallion"><i data-lucide="keyboard" aria-hidden="true"></i></span>
                    <span class="arcade-mode-copy"><strong>Practice</strong><span>Warm up. Play as often as you like.</span></span>
                    <i data-lucide="play" class="mode-play" aria-hidden="true"></i>
                </a>
                <a href="{{ route('challenge.access') }}" class="arcade-mode mode-official">
                    <span class="mode-medallion"><i data-lucide="zap" aria-hidden="true"></i></span>
                    <span class="arcade-mode-copy"><strong>Challenge</strong><span>One attempt. Make it count.</span></span>
                    <i data-lucide="play" class="mode-play" aria-hidden="true"></i>
                </a>
            </div>
            <a class="high-score-link" href="{{ route('leaderboard.index') }}"><i data-lucide="trophy" aria-hidden="true"></i> Leaderboard <i data-lucide="chevron-right" aria-hidden="true"></i></a>
        </section>
        @if($activeChallenge)
            <p class="arcade-event"><span class="status-dot"></span> {{ $activeChallenge->title }}</p>
        @endif
        <p class="title-instructions">Type the passage. Build your speed. Keep your accuracy.</p>
    </div>
</div>
@endsection
