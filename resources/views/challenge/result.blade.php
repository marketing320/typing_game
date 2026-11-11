@extends('layouts.app')

@section('title', 'Your Result')

@section('content')
@if($attempt->status === 'completed')
    <div id="lucky-draw-modal" role="dialog" aria-modal="true" aria-labelledby="lucky-draw-title" aria-describedby="lucky-draw-message" class="fixed inset-0 bg-black/75 flex items-center justify-center z-50 p-4 overflow-y-auto" onkeydown="if (event.key === 'Escape') this.classList.add('hidden')">
        <div class="bg-white max-w-sm w-full ui-card p-6 text-center my-auto">
            <i data-lucide="{{ $attempt->accuracy > 80 ? 'trophy' : 'thumbs-up' }}" class="w-10 h-10 text-amber-600 mx-auto mb-4" aria-hidden="true"></i>
            <h2 id="lucky-draw-title" class="text-xl font-bold text-amber-800 mb-4">{{ $attempt->accuracy > 80 ? 'Congratulations' : 'Thanks for playing!' }}</h2>
            <p id="lucky-draw-message" class="text-sm text-amber-900 leading-loose mb-6">
                @if($attempt->accuracy > 80)
                    You have achieved 80% accuracy and are entitled to join our lucky draw! Please go to the counter to receive your lucky draw token.
                @else
                    You gave it a great shot! You’ll need over 80% accuracy to qualify for the lucky draw. Keep practising—we’re cheering you on! 🎉
                @endif
            </p>
            <button type="button" onclick="document.getElementById('lucky-draw-modal').classList.add('hidden')" class="w-full bg-amber-700 text-white text-sm font-bold py-3 ui-button hover:bg-amber-800 transition-colors">Got it</button>
        </div>
    </div>
@endif

<div class="page-shell" style="max-width: 760px;">

    <div class="page-heading">
        <span class="eyebrow">Challenge complete</span><br>
        @if($attempt->wpm >= 60)
            <div class="inline-flex items-center justify-center w-16 h-16 bg-amber-100 border-2 border-amber-400 mb-4 animate-bounce">
                <i data-lucide="trophy" class="w-8 h-8 text-amber-600"></i>
            </div>
            <h1>Well typed!</h1>
        @elseif($attempt->wpm >= 30)
            <div class="inline-flex items-center justify-center w-16 h-16 bg-green-100 border-2 border-green-400 mb-4 animate-bounce">
                <i data-lucide="star" class="w-8 h-8 text-green-600"></i>
            </div>
            <h1>Challenge complete.</h1>
        @else
            <div class="inline-flex items-center justify-center w-16 h-16 bg-blue-100 border-2 border-blue-300 mb-4">
                <i data-lucide="thumbs-up" class="w-8 h-8 text-blue-600"></i>
            </div>
            <h1>Thanks for playing.</h1>
        @endif
        <p class="text-amber-600 text-sm mt-3 leading-loose">{{ $attempt->player->username }} &middot; {{ $attempt->challenge->title }}</p>
    </div>

    <!-- Main score cards -->
    <div class="results-primary grid grid-cols-2 gap-4 mb-6">
        <div class="bg-amber-700 text-white p-6 text-center ui-card">
            <div class="text-2xl font-bold">{{ $attempt->wpm }}</div>
            <div class="text-amber-200 text-sm mt-2">Words Per Minute</div>
        </div>
        <div class="bg-white p-6 text-center ui-card">
            <div class="text-2xl font-bold text-amber-700">{{ number_format($attempt->accuracy, 1) }}%</div>
            <div class="text-amber-500 text-sm mt-2">Accuracy</div>
        </div>
    </div>

    <!-- Detail stats -->
    <div class="bg-white ui-card p-5 mb-6">
        <h3 class="font-bold text-amber-800 text-sm mb-5 border-b border-amber-100 pb-3">The breakdown</h3>
        <div class="results-details grid grid-cols-3 gap-3 text-center text-sm">
            <div>
                <div class="font-bold text-green-600 text-sm">{{ $attempt->correct_words }}</div>
                <div class="text-gray-400 mt-1">Correct Words</div>
            </div>
            <div>
                <div class="font-bold text-red-500 text-sm">{{ $attempt->wrong_words }}</div>
                <div class="text-gray-400 mt-1">Wrong Words</div>
            </div>
            <div>
                <div class="font-bold text-blue-600 text-sm">{{ number_format($attempt->duration_seconds, 1) }}s</div>
                <div class="text-gray-400 mt-1">Duration</div>
            </div>
            <div>
                <div class="font-bold text-green-600 text-sm">{{ $attempt->correct_characters }}</div>
                <div class="text-gray-400 mt-1">Correct Chars</div>
            </div>
            <div>
                <div class="font-bold text-red-500 text-sm">{{ $attempt->wrong_characters }}</div>
                <div class="text-gray-400 mt-1">Wrong Chars</div>
            </div>
            <div>
                <div class="font-bold text-orange-500 text-sm">{{ $attempt->mistake_count }}</div>
                <div class="text-gray-400 mt-1">Mistakes</div>
            </div>
        </div>
    </div>

    <div class="flex gap-3">
        <a href="{{ route('leaderboard.index') }}"
           class="flex-1 bg-amber-700 text-white text-sm font-bold py-3 text-center ui-button hover:bg-amber-800 transition-colors flex items-center justify-center gap-2">
            <i data-lucide="award" class="w-4 h-4"></i> Leaderboard
        </a>
        <a href="{{ route('rehearsal.index') }}"
           class="flex-1 bg-white text-amber-700 text-sm font-bold py-3 text-center ui-button hover:bg-amber-50 transition-colors flex items-center justify-center gap-2">
            <i data-lucide="target" class="w-4 h-4"></i> Practice More
        </a>
    </div>

</div>
@endsection
