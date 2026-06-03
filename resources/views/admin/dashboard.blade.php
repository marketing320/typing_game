@extends('layouts.admin')
@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<!-- Stats grid -->
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
    <div class="bg-white rounded-xl p-5 shadow border border-gray-100">
        <div class="text-3xl font-extrabold text-blue-600">{{ $stats['total_players'] }}</div>
        <div class="text-sm text-gray-400 mt-1">Total Players</div>
    </div>
    <div class="bg-white rounded-xl p-5 shadow border border-gray-100">
        <div class="text-3xl font-extrabold text-purple-600">{{ $stats['total_attempts'] }}</div>
        <div class="text-sm text-gray-400 mt-1">Total Attempts</div>
    </div>
    <div class="bg-white rounded-xl p-5 shadow border border-gray-100">
        <div class="text-3xl font-extrabold text-amber-600">{{ $stats['avg_wpm'] }}</div>
        <div class="text-sm text-gray-400 mt-1">Avg WPM</div>
    </div>
    <div class="bg-white rounded-xl p-5 shadow border border-gray-100">
        <div class="text-3xl font-extrabold text-green-600">{{ $stats['highest_wpm'] }}</div>
        <div class="text-sm text-gray-400 mt-1">Highest WPM</div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-6">
    <div class="bg-white rounded-xl p-5 shadow border border-gray-100">
        <div class="text-3xl font-extrabold text-orange-500">{{ $stats['today_attempts'] }}</div>
        <div class="text-sm text-gray-400 mt-1">Today's Attempts</div>
    </div>
    <div class="lg:col-span-2 bg-white rounded-xl p-5 shadow border border-gray-100">
        <div class="text-xs text-gray-400 mb-1">Active Challenge</div>
        @if($stats['active_challenge'])
            <div class="font-bold text-gray-800">{{ $stats['active_challenge']->title }}</div>
            <span class="inline-block mt-1 bg-green-100 text-green-700 text-xs px-2 py-0.5 rounded-full">ACTIVE</span>
        @else
            <div class="text-gray-400 text-sm">No active challenge</div>
            <a href="{{ route('admin.challenges.create') }}" class="text-blue-500 text-xs hover:underline">Create one →</a>
        @endif
    </div>
</div>

<!-- Recent attempts -->
<div class="bg-white rounded-xl shadow border border-gray-100 overflow-hidden">
    <div class="px-5 py-4 border-b border-gray-100 flex justify-between items-center">
        <h3 class="font-semibold text-gray-700">Recent Attempts</h3>
        <a href="{{ route('admin.attempts.index') }}" class="text-blue-500 text-xs hover:underline">View all →</a>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-400 uppercase text-xs">
                <tr>
                    <th class="px-4 py-3 text-left">Player</th>
                    <th class="px-4 py-3 text-left">Challenge</th>
                    <th class="px-4 py-3 text-right">WPM</th>
                    <th class="px-4 py-3 text-right">Accuracy</th>
                    <th class="px-4 py-3 text-center">Status</th>
                    <th class="px-4 py-3 text-right">Time</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($recentAttempts as $a)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3">{{ $a->player->username ?? '—' }}</td>
                    <td class="px-4 py-3 text-gray-400 text-xs">{{ Str::limit($a->challenge->title ?? '—', 30) }}</td>
                    <td class="px-4 py-3 text-right font-bold">{{ $a->wpm }}</td>
                    <td class="px-4 py-3 text-right text-green-600">{{ $a->accuracy }}%</td>
                    <td class="px-4 py-3 text-center">
                        <span class="px-2 py-0.5 text-xs rounded-full
                            {{ $a->status === 'completed' ? 'bg-green-100 text-green-700' : '' }}
                            {{ $a->status === 'started' ? 'bg-blue-100 text-blue-700' : '' }}
                            {{ $a->status === 'failed' ? 'bg-red-100 text-red-700' : '' }}
                            {{ $a->status === 'disqualified' ? 'bg-gray-100 text-gray-700' : '' }}">
                            {{ ucfirst($a->status) }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-right text-gray-400 text-xs">{{ $a->created_at->diffForHumans() }}</td>
                </tr>
                @empty
                <tr><td colspan="6" class="px-4 py-8 text-center text-gray-300">No attempts yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
