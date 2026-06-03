@extends('layouts.app')

@section('title', 'Your Result')

@section('content')
<div class="max-w-2xl mx-auto px-4 py-10">

    <div class="text-center mb-8">
        @if($attempt->wpm >= 60)
            <div class="inline-flex items-center justify-center w-16 h-16 bg-amber-100 border-2 border-amber-400 mb-4 animate-bounce">
                <i data-lucide="trophy" class="w-8 h-8 text-amber-600"></i>
            </div>
            <h1 class="text-sm font-bold text-amber-900">[ INCREDIBLE! ]</h1>
        @elseif($attempt->wpm >= 30)
            <div class="inline-flex items-center justify-center w-16 h-16 bg-green-100 border-2 border-green-400 mb-4 animate-bounce">
                <i data-lucide="star" class="w-8 h-8 text-green-600"></i>
            </div>
            <h1 class="text-sm font-bold text-amber-900">[ WELL DONE! ]</h1>
        @else
            <div class="inline-flex items-center justify-center w-16 h-16 bg-blue-100 border-2 border-blue-300 mb-4">
                <i data-lucide="thumbs-up" class="w-8 h-8 text-blue-600"></i>
            </div>
            <h1 class="text-sm font-bold text-amber-900">[ GOOD TRY! ]</h1>
        @endif
        <p class="text-amber-600 text-[10px] mt-3 leading-loose">{{ $attempt->player->username }} &middot; {{ $attempt->challenge->title }}</p>
    </div>

    <!-- Main score cards -->
    <div class="grid grid-cols-2 gap-4 mb-6">
        <div class="bg-amber-700 text-white p-6 text-center pixel-card" style="box-shadow: 4px 4px 0 #1c0a00;">
            <div class="text-2xl font-bold">{{ $attempt->wpm }}</div>
            <div class="text-amber-200 text-[10px] mt-2">Words Per Minute</div>
        </div>
        <div class="bg-white p-6 text-center pixel-card">
            <div class="text-2xl font-bold text-amber-700">{{ number_format($attempt->accuracy, 1) }}%</div>
            <div class="text-amber-500 text-[10px] mt-2">Accuracy</div>
        </div>
    </div>

    <!-- Detail stats -->
    <div class="bg-white pixel-card p-5 mb-6">
        <h3 class="font-bold text-amber-800 text-[10px] mb-5 border-b-2 border-amber-100 pb-3">[ DETAILED RESULTS ]</h3>
        <div class="grid grid-cols-3 gap-3 text-center text-[10px]">
            <div>
                <div class="font-bold text-green-600 text-xs">{{ $attempt->correct_words }}</div>
                <div class="text-gray-400 mt-1">Correct Words</div>
            </div>
            <div>
                <div class="font-bold text-red-500 text-xs">{{ $attempt->wrong_words }}</div>
                <div class="text-gray-400 mt-1">Wrong Words</div>
            </div>
            <div>
                <div class="font-bold text-blue-600 text-xs">{{ number_format($attempt->duration_seconds, 1) }}s</div>
                <div class="text-gray-400 mt-1">Duration</div>
            </div>
            <div>
                <div class="font-bold text-green-600 text-xs">{{ $attempt->correct_characters }}</div>
                <div class="text-gray-400 mt-1">Correct Chars</div>
            </div>
            <div>
                <div class="font-bold text-red-500 text-xs">{{ $attempt->wrong_characters }}</div>
                <div class="text-gray-400 mt-1">Wrong Chars</div>
            </div>
            <div>
                <div class="font-bold text-orange-500 text-xs">{{ $attempt->mistake_count }}</div>
                <div class="text-gray-400 mt-1">Mistakes</div>
            </div>
        </div>
    </div>

    <div class="flex gap-3">
        <a href="{{ route('leaderboard.index') }}"
           class="flex-1 bg-amber-700 text-white text-[10px] font-bold py-3 text-center pixel-btn hover:bg-amber-800 transition-colors flex items-center justify-center gap-2"
           style="box-shadow: 3px 3px 0 #1c0a00; border-color: #451a03;">
            <i data-lucide="award" class="w-4 h-4"></i> Leaderboard
        </a>
        <a href="{{ route('rehearsal.index') }}"
           class="flex-1 bg-white text-amber-700 text-[10px] font-bold py-3 text-center pixel-btn hover:bg-amber-50 transition-colors flex items-center justify-center gap-2">
            <i data-lucide="target" class="w-4 h-4"></i> Practice More
        </a>
    </div>

</div>
@endsection
