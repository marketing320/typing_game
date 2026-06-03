<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Player;
use Illuminate\Http\Request;

class AdminPlayerController extends Controller
{
    public function index(Request $request)
    {
        $query = Player::withCount('challengeAttempts');

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('username', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $players = $query->latest()->paginate(20);

        return view('admin.players.index', compact('players'));
    }

    public function show(Player $player)
    {
        $player->load('challengeAttempts.challenge');
        return view('admin.players.show', compact('player'));
    }

    public function block(Player $player)
    {
        $player->update(['is_blocked' => true]);
        return back()->with('success', "Player {$player->username} has been blocked.");
    }

    public function unblock(Player $player)
    {
        $player->update(['is_blocked' => false]);
        return back()->with('success', "Player {$player->username} has been unblocked.");
    }
}
