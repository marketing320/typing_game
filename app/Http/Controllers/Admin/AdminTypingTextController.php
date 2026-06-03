<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TypingChallenge;
use App\Models\TypingText;
use Illuminate\Http\Request;

class AdminTypingTextController extends Controller
{
    public function index()
    {
        $texts = TypingText::with('challenge')->latest()->paginate(15);
        return view('admin.typing-texts.index', compact('texts'));
    }

    public function create()
    {
        $challenges = TypingChallenge::whereIn('status', ['draft', 'active'])->get();
        return view('admin.typing-texts.create', compact('challenges'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'challenge_id' => 'required_if:mode,challenge|nullable|exists:typing_challenges,id',
            'mode' => 'required|in:challenge,rehearsal',
            'title' => 'nullable|string|max:255',
            'content' => 'required|string|min:10',
            'language' => 'required|string|max:10',
            'difficulty' => 'required|in:easy,medium,hard',
            'is_active' => 'boolean',
        ]);

        $data['created_by'] = session('admin_id');
        $data['is_active'] = $request->boolean('is_active');

        TypingText::create($data);

        return redirect()->route('admin.typing-texts.index')->with('success', 'Text created.');
    }

    public function edit(TypingText $typingText)
    {
        $challenges = TypingChallenge::whereIn('status', ['draft', 'active'])->get();
        return view('admin.typing-texts.edit', compact('typingText', 'challenges'));
    }

    public function update(Request $request, TypingText $typingText)
    {
        $data = $request->validate([
            'challenge_id' => 'required_if:mode,challenge|nullable|exists:typing_challenges,id',
            'mode' => 'required|in:challenge,rehearsal',
            'title' => 'nullable|string|max:255',
            'content' => 'required|string|min:10',
            'language' => 'required|string|max:10',
            'difficulty' => 'required|in:easy,medium,hard',
            'is_active' => 'boolean',
        ]);

        $data['is_active'] = $request->boolean('is_active');
        $typingText->update($data);

        return redirect()->route('admin.typing-texts.index')->with('success', 'Text updated.');
    }

    public function destroy(TypingText $typingText)
    {
        $typingText->delete();
        return redirect()->route('admin.typing-texts.index')->with('success', 'Text deleted.');
    }
}
