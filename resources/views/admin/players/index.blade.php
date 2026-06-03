@extends('layouts.admin')
@section('title', 'Players')
@section('page-title', 'Players')

@section('content')
<div class="mb-4">
    <form method="GET" class="flex gap-2">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search username or email..."
            class="border border-gray-300 rounded-lg px-3 py-2 text-sm flex-1 max-w-xs focus:outline-none focus:border-blue-400">
        <button type="submit" class="bg-gray-800 text-white text-sm px-4 py-2 rounded-lg">Search</button>
    </form>
</div>

<div class="bg-white rounded-xl shadow border border-gray-100 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-gray-400 uppercase text-xs">
            <tr>
                <th class="px-4 py-3 text-left">Username</th>
                <th class="px-4 py-3 text-left">Email</th>
                <th class="px-4 py-3 text-center">Verified</th>
                <th class="px-4 py-3 text-center">Attempts</th>
                <th class="px-4 py-3 text-center">Blocked</th>
                <th class="px-4 py-3 text-center">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            @forelse($players as $p)
            <tr class="hover:bg-gray-50 {{ $p->is_blocked ? 'opacity-50' : '' }}">
                <td class="px-4 py-3 font-semibold">{{ $p->username }}</td>
                <td class="px-4 py-3 text-gray-500 text-xs">{{ $p->email }}</td>
                <td class="px-4 py-3 text-center">
                    @if($p->email_verified_at)
                        <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-green-100"><i data-lucide="check" class="w-3 h-3 text-green-600"></i></span>
                    @else
                        <span class="text-gray-300">—</span>
                    @endif
                </td>
                <td class="px-4 py-3 text-center">{{ $p->challenge_attempts_count }}</td>
                <td class="px-4 py-3 text-center">
                    @if($p->is_blocked)
                        <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-red-100"><i data-lucide="ban" class="w-3 h-3 text-red-500"></i></span>
                    @else
                        <span class="text-gray-300">—</span>
                    @endif
                </td>
                <td class="px-4 py-3 text-center">
                    <div class="flex gap-2 justify-center">
                        <a href="{{ route('admin.players.show', $p) }}" class="text-blue-500 hover:underline text-xs">View</a>
                        @if($p->is_blocked)
                        <form method="POST" action="{{ route('admin.players.unblock', $p) }}">
                            @csrf
                            <button class="text-green-500 hover:underline text-xs">Unblock</button>
                        </form>
                        @else
                        <form method="POST" action="{{ route('admin.players.block', $p) }}" onsubmit="return confirm('Block this player?')">
                            @csrf
                            <button class="text-red-400 hover:underline text-xs">Block</button>
                        </form>
                        @endif
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="6" class="px-4 py-8 text-center text-gray-300">No players yet.</td></tr>
            @endforelse
        </tbody>
    </table>
    @if($players->hasPages())
    <div class="px-4 py-3 border-t border-gray-100">{{ $players->links() }}</div>
    @endif
</div>
@endsection
