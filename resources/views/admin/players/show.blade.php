@extends('layouts.admin')
@section('title', $player->full_name ?? $player->username)
@section('page-title', $player->full_name ?? $player->username)

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Profile card -->
    <div class="bg-white rounded-xl shadow border border-gray-100 p-6">
        <h3 class="font-bold text-gray-700 mb-4">Player Info</h3>
        <dl class="space-y-3 text-sm">
            <div><dt class="text-gray-400 text-xs">Username</dt><dd class="font-medium">{{ $player->username }}</dd></div>
            <div><dt class="text-gray-400 text-xs">Full Name</dt><dd class="font-medium">{{ $player->full_name ?? '—' }}</dd></div>
            <div><dt class="text-gray-400 text-xs">Email</dt><dd class="font-medium">{{ $player->email }}</dd></div>
            <div><dt class="text-gray-400 text-xs">Phone</dt><dd class="font-medium font-mono">{{ $player->phone ?? '—' }}</dd></div>
            <div><dt class="text-gray-400 text-xs">Referral Source</dt><dd class="font-medium">{{ $player->referral_source ?? '—' }}</dd></div>
            <div><dt class="text-gray-400 text-xs">Active Challenge Rank</dt><dd class="font-bold text-amber-700">{{ $rank ? '#'.$rank : '—' }}</dd></div>
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
            @php
                $waPhone = preg_replace('/\D+/', '', $player->phone ?? '');
                $waLink  = $waPhone !== '' ? 'https://wa.me/' . $waPhone : '';
            @endphp
            @if($waLink)
            <a href="{{ $waLink }}" target="_blank" rel="noopener noreferrer"
               class="w-full bg-green-500 text-white text-sm font-bold py-2 rounded-lg hover:bg-green-600 transition flex items-center justify-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4">
                    <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413z"/>
                </svg>
                WhatsApp
            </a>
            @endif
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
