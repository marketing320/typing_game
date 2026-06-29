<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Player;
use Illuminate\Http\Request;

class AdminPlayerController extends Controller
{
    public function index(Request $request)
    {
        $players = $this->applySearch(
            Player::withCount('challengeAttempts'),
            $request->input('search')
        )->latest()->paginate(20)->withQueryString();

        return view('admin.players.index', compact('players'));
    }

    private function applySearch($query, ?string $search)
    {
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('username', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        return $query;
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

    public function bulkExport(Request $request)
    {
        $query = Player::withCount('challengeAttempts');

        if ($request->boolean('select_all')) {
            // Whole dataset, respecting the current search filter.
            $this->applySearch($query, $request->input('search'));
        } else {
            $ids = array_filter((array) $request->input('ids', []));
            if (empty($ids)) {
                return back()->with('error', 'No players selected.');
            }
            $query->whereIn('id', $ids);
        }

        $query->orderBy('created_at', 'desc');

        $filename = 'players_' . now()->format('Ymd_His') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
        ];

        $callback = function () use ($query) {
            $out = fopen('php://output', 'w');
            // UTF-8 BOM so Excel opens it correctly
            fputs($out, "\xEF\xBB\xBF");
            fputcsv($out, ['ID', 'Username', 'Full Name', 'Email', 'Phone', 'Referral Source', 'Verified', 'Attempts', 'Blocked', 'Joined']);
            // cursor() streams rows one at a time — safe for exporting the full dataset.
            foreach ($query->cursor() as $p) {
                fputcsv($out, [
                    $p->id,
                    $p->username,
                    $p->full_name ?? '',
                    $p->email,
                    $p->phone ?? '',
                    $p->referral_source ?? '',
                    $p->email_verified_at ? 'Yes' : 'No',
                    $p->challenge_attempts_count,
                    $p->is_blocked ? 'Yes' : 'No',
                    $p->created_at->format('Y-m-d H:i:s'),
                ]);
            }
            fclose($out);
        };

        return response()->stream($callback, 200, $headers);
    }
}
