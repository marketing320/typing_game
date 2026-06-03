<?php

namespace App\Http\Controllers;

use App\Models\RehearsalAttempt;
use App\Models\TypingText;
use App\Services\ScoringService;
use Illuminate\Http\Request;

class RehearsalController extends Controller
{
    public function __construct(private ScoringService $scoring) {}

    public function index()
    {
        $text = TypingText::where('mode', 'rehearsal')
            ->where('is_active', true)
            ->inRandomOrder()
            ->first();

        if (!$text) {
            return view('rehearsal.index', ['text' => null]);
        }

        return view('rehearsal.index', compact('text'));
    }

    public function submit(Request $request)
    {
        $data = $request->validate([
            'typing_text_id' => 'required|exists:typing_texts,id',
            'user_input' => 'required|string',
            'duration_seconds' => 'required|numeric|min:0',
            'anonymous_id' => 'nullable|string|max:64',
        ]);

        $text = TypingText::findOrFail($data['typing_text_id']);
        $scores = $this->scoring->analyze($text->content, $data['user_input'], (float) $data['duration_seconds']);

        $attempt = RehearsalAttempt::create(array_merge($scores, [
            'typing_text_id' => $text->id,
            'anonymous_id' => $data['anonymous_id'] ?? null,
            'started_at' => now()->subSeconds($data['duration_seconds']),
            'completed_at' => now(),
            'duration_seconds' => $data['duration_seconds'],
            'user_input' => $data['user_input'],
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]));

        return response()->json([
            'success' => true,
            'wpm' => $attempt->wpm,
            'accuracy' => $attempt->accuracy,
            'correct_words' => $attempt->correct_words,
            'wrong_words' => $attempt->wrong_words,
            'duration_seconds' => $attempt->duration_seconds,
        ]);
    }
}
