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

    public function destroy(Player $player)
    {
        $username = $player->username;
        $player->delete();

        return redirect()->route('admin.players.index')
            ->with('success', "Player \"{$username}\" has been deleted.");
    }

    public function bulkDestroy(Request $request)
    {
        $ids = array_filter((array) $request->input('ids', []));

        if (empty($ids)) {
            return back()->with('error', 'No players selected.');
        }

        $count = Player::whereIn('id', $ids)->delete();

        return redirect()->route('admin.players.index')
            ->with('success', "{$count} player(s) deleted.");
    }

    public function bulkBlock(Request $request)
    {
        $ids = array_filter((array) $request->input('ids', []));

        if (empty($ids)) {
            return back()->with('error', 'No players selected.');
        }

        $count = Player::whereIn('id', $ids)->update(['is_blocked' => true]);

        return redirect()->route('admin.players.index')
            ->with('success', "{$count} player(s) blocked.");
    }

    public function bulkUnblock(Request $request)
    {
        $ids = array_filter((array) $request->input('ids', []));

        if (empty($ids)) {
            return back()->with('error', 'No players selected.');
        }

        $count = Player::whereIn('id', $ids)->update(['is_blocked' => false]);

        return redirect()->route('admin.players.index')
            ->with('success', "{$count} player(s) unblocked.");
    }
}
