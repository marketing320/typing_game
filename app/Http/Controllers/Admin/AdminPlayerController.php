<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChallengeAttempt;
use App\Models\Player;
use App\Models\TypingChallenge;
use Illuminate\Http\Request;

class AdminPlayerController extends Controller
{
    public function index(Request $request)
    {
        // asc = best rank (#1) first; desc = worst first. Unranked players always last.
        $dir = $request->input('dir') === 'desc' ? 'desc' : 'asc';

        $query = $this->applySearch(
            Player::withCount('challengeAttempts'),
            $request->input('search')
        );

        $activeChallenge = TypingChallenge::where('status', 'active')->latest()->first();

        if ($activeChallenge) {
            // Per-player best Score (WPM × Accuracy) on the active challenge.
            $query->addSelect(['best_score' => ChallengeAttempt::selectRaw('MAX(wpm * accuracy)')
                ->whereColumn('player_id', 'players.id')
                ->where('challenge_id', $activeChallenge->id)
                ->where('status', 'completed')
            ]);

            $query->orderByRaw('best_score IS NULL ASC')                       // ranked first, unranked last
                  ->orderByRaw('best_score ' . ($dir === 'asc' ? 'DESC' : 'ASC')); // #1 = highest score
        }

        $players = $query->orderByDesc('players.created_at')                    // stable fallback
            ->paginate(20)
            ->withQueryString();

        $rankMap = $this->activeChallengeRankMap();

        return view('admin.players.index', compact('players', 'rankMap', 'dir'));
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

    /**
     * player_id => rank on the active challenge's leaderboard (best Score per email),
     * mirroring the public leaderboard. Empty array when no challenge is active.
     */
    private function activeChallengeRankMap(): array
    {
        $challenge = TypingChallenge::where('status', 'active')->latest()->first();
        if (!$challenge) {
            return [];
        }

        $attempts = ChallengeAttempt::with('player:id,email')
            ->where('challenge_id', $challenge->id)
            ->where('status', 'completed')
            ->whereHas('player')
            ->orderByRaw('(wpm * accuracy) DESC')
            ->orderByDesc('accuracy')
            ->orderBy('duration_seconds')
            ->get()
            ->unique(fn ($a) => $a->player->email)   // best attempt per email
            ->values();

        $map  = [];
        $rank = 1;
        $prev = null;
        foreach ($attempts as $index => $a) {
            if ($prev !== null
                && round($a->wpm * $a->accuracy / 100, 2) == round($prev->wpm * $prev->accuracy / 100, 2)
                && $a->accuracy         == $prev->accuracy
                && $a->duration_seconds == $prev->duration_seconds
            ) {
                // tie — keep the same rank
            } else {
                $rank = $index + 1;
            }
            $map[$a->player_id] = $rank;
            $prev = $a;
        }

        return $map;
    }

    public function show(Player $player)
    {
        $player->load('challengeAttempts.challenge');
        $rank = $this->activeChallengeRankMap()[$player->id] ?? null;
        return view('admin.players.show', compact('player', 'rank'));
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
