@extends('layouts.app')

@section('title', 'Desktop Required')
@section('meta_robots', 'noindex')

@section('content')
<div class="max-w-md mx-auto px-4 py-12">
    <div class="bg-white pixel-card p-8 text-center">
        <img src="{{ asset('img/Desk-Typing-1.webp') }}" alt="Our official PC typing setup"
             class="w-full max-w-xs mx-auto mb-6 border-2 border-amber-900 pixel-card"
             style="box-shadow: 4px 4px 0 #1c0a00;" width="320" loading="lazy">
        <h1 class="text-sm font-bold text-amber-900 mb-4">Desktop Only</h1>
        <p class="text-amber-700 text-[10px] leading-loose mb-6">{!! nl2br(e($message)) !!}</p>

        <div class="space-y-2">
            <a href="{{ route('rehearsal.index') }}"
               class="flex items-center justify-center gap-2 bg-amber-700 text-white text-[10px] font-bold py-3 pixel-btn hover:bg-amber-800 transition-colors"
               style="box-shadow: 3px 3px 0 #1c0a00; border-color: #451a03;">
                Try Practice Mode <i data-lucide="arrow-right" class="w-4 h-4"></i>
            </a>
            <a href="{{ route('leaderboard.index') }}"
               class="flex items-center justify-center gap-2 bg-amber-100 text-amber-800 text-[10px] font-bold py-3 pixel-btn hover:bg-amber-200 transition-colors">
                View Leaderboard <i data-lucide="award" class="w-4 h-4"></i>
            </a>
        </div>
    </div>
</div>
@endsection
