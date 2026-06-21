<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GeofenceRule;
use App\Models\TypingChallenge;
use Illuminate\Http\Request;

class AdminChallengeController extends Controller
{
    public function index()
    {
        $challenges = TypingChallenge::with('geofenceRule')->latest()->paginate(15);
        return view('admin.challenges.index', compact('challenges'));
    }

    public function create()
    {
        $geofenceRules = GeofenceRule::where('is_active', true)->get();
        $activeOther = TypingChallenge::where('status', 'active')->first();
        return view('admin.challenges.create', compact('geofenceRules', 'activeOther'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'               => 'required|string|max:255',
            'description'         => 'nullable|string',
            'status'              => 'required|in:draft,active,ended',
            'start_at'            => 'nullable|date',
            'end_at'              => 'nullable|date|after_or_equal:start_at',
            'allow_retry_next_day' => 'boolean',
            'max_attempts_per_day' => 'required|integer|min:1',
            'require_unique_email' => 'boolean',
            'require_geofence'    => 'boolean',
            'geofence_rule_id'    => 'required_if:require_geofence,1|nullable|exists:geofence_rules,id',
        ]);

        $data['created_by']          = session('admin_id');
        $data['allow_retry_next_day'] = $request->boolean('allow_retry_next_day');
        $data['require_unique_email'] = $request->boolean('require_unique_email');
        $data['require_geofence']     = $request->boolean('require_geofence');

        $challenge = TypingChallenge::create($data);

        // Only one active challenge at a time — end all others
        if ($data['status'] === 'active') {
            TypingChallenge::where('status', 'active')
                ->where('id', '!=', $challenge->id)
                ->update(['status' => 'ended']);
        }

        return redirect()->route('admin.challenges.index')->with('success', 'Challenge created.');
    }

    public function edit(TypingChallenge $challenge)
    {
        $geofenceRules = GeofenceRule::where('is_active', true)->get();
        $activeOther = TypingChallenge::where('status', 'active')
            ->where('id', '!=', $challenge->id)
            ->first();
        return view('admin.challenges.edit', compact('challenge', 'geofenceRules', 'activeOther'));
    }

    public function update(Request $request, TypingChallenge $challenge)
    {
        $data = $request->validate([
            'title'               => 'required|string|max:255',
            'description'         => 'nullable|string',
            'status'              => 'required|in:draft,active,ended',
            'start_at'            => 'nullable|date',
            'end_at'              => 'nullable|date',
            'allow_retry_next_day' => 'boolean',
            'max_attempts_per_day' => 'required|integer|min:1',
            'require_unique_email' => 'boolean',
            'require_geofence'    => 'boolean',
            'geofence_rule_id'    => 'required_if:require_geofence,1|nullable|exists:geofence_rules,id',
        ]);

        $data['allow_retry_next_day'] = $request->boolean('allow_retry_next_day');
        $data['require_unique_email'] = $request->boolean('require_unique_email');
        $data['require_geofence']     = $request->boolean('require_geofence');

        $challenge->update($data);

        // Only one active challenge at a time — end all others
        if ($data['status'] === 'active') {
            TypingChallenge::where('status', 'active')
                ->where('id', '!=', $challenge->id)
                ->update(['status' => 'ended']);
        }

        return redirect()->route('admin.challenges.index')->with('success', 'Challenge updated.');
    }

    public function destroy(TypingChallenge $challenge)
    {
        $challenge->delete();
        return redirect()->route('admin.challenges.index')->with('success', 'Challenge deleted.');
    }
}
