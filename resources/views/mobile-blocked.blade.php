@extends('layouts.app')

@section('title', 'Desktop Required')
@section('meta_robots', 'noindex')

@section('content')
<div class="flow-shell">
    <div class="bg-white ui-card p-8 text-center">
        <img src="{{ asset('img/Desk-Typing-1.webp') }}" alt="Our official PC typing setup"
             class="w-full max-w-xs mx-auto mb-6 border-2 border-amber-900 ui-card" width="320" loading="lazy">
        <div class="page-heading"><span class="eyebrow">Save this one for your keyboard</span><h1>Big keyboard energy.</h1><p>The official challenge needs a desktop.</p></div>
        <p class="text-amber-700 text-sm leading-loose mb-6">{!! nl2br(e($message)) !!}</p>

        <div class="space-y-2">
            <a href="{{ route('rehearsal.index') }}"
               class="flex items-center justify-center gap-2 bg-amber-700 text-white text-sm font-bold py-3 ui-button hover:bg-amber-800 transition-colors">
                Try Practice Mode <i data-lucide="arrow-right" class="w-4 h-4"></i>
            </a>
            <a href="{{ route('leaderboard.index') }}"
               class="flex items-center justify-center gap-2 bg-amber-100 text-amber-800 text-sm font-bold py-3 ui-button hover:bg-amber-200 transition-colors">
                View Leaderboard <i data-lucide="award" class="w-4 h-4"></i>
            </a>
        </div>
    </div>
</div>
@endsection
