@extends('layouts.admin')
@section('title', $player->username)
@section('page-title', $player->username)

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Profile card -->
    <div class="bg-white rounded-xl shadow border border-gray-100 p-6">
        <h3 class="font-bold text-gray-700 mb-4">Player Info</h3>
        <dl class="space-y-3 text-sm">
            <div><dt class="text-gray-400 text-xs">Email</dt><dd class="font-medium">{{ $player->email }}</dd></div>
            <div>
                <dt class="text-gray-400 text-xs">Verified</dt>
                <dd class="flex items-center gap-1.5 font-medium">
                    @if($player->email_verified_at)
                        <i data-lucide="check-circle-2" class="w-4 h-4 text-green-500"></i> {{ $player->email_verified_at->format('d M Y') }}
                    @else
                        <i data-lucide="clock" class="w-4 h-4 text-gray-400"></i> <span class="text-gray-400">Unverified</span>
                    @endif
                </dd>
            </div>
            <div><dt class="text-gray-400 text-xs">Last Login</dt><dd>{{ $player->last_login_at?->diffForHumans() ?? '—' }}</dd></div>
            <div>
                <dt class="text-gray-400 text-xs">Status</dt>
                <dd class="flex items-center gap-1.5 font-medium">
                    @if($player->is_blocked)
                        <i data-lucide="ban" class="w-4 h-4 text-red-500"></i> <span class="text-red-600">Blocked</span>
                    @else
                        <i data-lucide="circle-check-big" class="w-4 h-4 text-green-500"></i> <span class="text-green-600">Active</span>
                    @endif
                </dd>
            </div>
            <div><dt class="text-gray-400 text-xs">Joined</dt><dd>{{ $player->created_at->format('d M Y') }}</dd></div>
        </dl>
        <div class="mt-5 pt-4 border-t border-gray-100 space-y-2">
            @if($player->is_blocked)
            <form method="POST" action="{{ route('admin.players.unblock', $player) }}">
                @csrf
                <button class="w-full bg-green-600 text-white text-sm font-bold py-2 rounded-lg hover:bg-green-700 transition">Unblock Player</button>
            </form>
            @else
            <form method="POST" action="{{ route('admin.players.block', $player) }}" onsubmit="return confirm('Block this player?')">
                @csrf
                <button class="w-full bg-red-500 text-white text-sm font-bold py-2 rounded-lg hover:bg-red-600 transition">Block Player</button>
            </form>
            @endif
            <form method="POST" action="{{ route('admin.players.destroy', $player) }}"
                  onsubmit="return confirm('Delete {{ addslashes($player->username) }}?\n\nTheir account will be soft-deleted and removed from the leaderboard.\nAttempt records are kept for auditing.')">
                @csrf
                @method('DELETE')
                <button class="w-full bg-gray-100 text-gray-500 text-sm font-bold py-2 rounded-lg hover:bg-red-50 hover:text-red-600 transition">
                    Delete Player
                </button>
            </form>
        </div>
    </div>

    <!-- Attempts -->
    <div class="lg:col-span-2 bg-white rounded-xl shadow border border-gray-100 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100">
            <h3 class="font-bold text-gray-700">Attempt History</h3>
        </div>
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-400 uppercase text-xs">
                <tr>
                    <th class="px-4 py-3 text-left">Challenge</th>
                    <th class="px-4 py-3 text-right">WPM</th>
                    <th class="px-4 py-3 text-right">Accuracy</th>
                    <th class="px-4 py-3 text-center">Status</th>
                    <th class="px-4 py-3 text-right">Date</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($player->challengeAttempts as $a)
                <tr>
                    <td class="px-4 py-3 text-xs">{{ Str::limit($a->challenge->title ?? '—', 30) }}</td>
                    <td class="px-4 py-3 text-right font-bold">{{ $a->wpm }}</td>
                    <td class="px-4 py-3 text-right text-green-600">{{ $a->accuracy }}%</td>
                    <td class="px-4 py-3 text-center">
                        <span class="px-2 py-0.5 text-xs rounded-full {{ $a->status === 'completed' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                            {{ ucfirst($a->status) }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-right text-xs text-gray-400">{{ $a->created_at->format('d M Y') }}</td>
                </tr>
                @empty
                <tr><td colspan="5" class="px-4 py-8 text-center text-gray-300">No attempts yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
