@extends('layouts.admin')
@section('title', 'Leaderboard')
@section('page-title', 'Leaderboard')

@section('content')
<div class="mb-4">
    <form method="GET" class="flex gap-2">
        <select name="challenge_id" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-blue-400">
            <option value="">All Challenges</option>
            @foreach($challenges as $c)
            <option value="{{ $c->id }}" {{ request('challenge_id') == $c->id ? 'selected' : '' }}>{{ $c->title }}</option>
            @endforeach
        </select>
        <button type="submit" class="bg-gray-800 text-white text-sm px-4 py-2 rounded-lg">Filter</button>
    </form>
</div>

<div class="bg-white rounded-xl shadow border border-gray-100 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-gray-400 uppercase text-xs">
            <tr>
                <th class="px-4 py-3 text-left">Rank</th>
                <th class="px-4 py-3 text-left">Player</th>
                <th class="px-4 py-3 text-right">WPM</th>
                <th class="px-4 py-3 text-right">Accuracy</th>
                <th class="px-4 py-3 text-right">Duration</th>
                <th class="px-4 py-3 text-right">Completed</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            @forelse($entries as $entry)
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-3 font-bold">
                    @if($entry['rank'] <= 3)
                        {{ ['🥇','🥈','🥉'][$entry['rank']-1] }}
                    @else
                        #{{ $entry['rank'] }}
                    @endif
                </td>
                <td class="px-4 py-3 font-semibold">{{ $entry['username'] }}</td>
                <td class="px-4 py-3 text-right font-bold text-amber-600">{{ $entry['wpm'] }}</td>
                <td class="px-4 py-3 text-right text-green-600">{{ number_format($entry['accuracy'], 1) }}%</td>
                <td class="px-4 py-3 text-right text-gray-400">{{ number_format($entry['duration_seconds'], 1) }}s</td>
                <td class="px-4 py-3 text-right text-xs text-gray-400">{{ \Carbon\Carbon::parse($entry['completed_at'])->format('d M Y H:i') }}</td>
            </tr>
            @empty
            <tr><td colspan="6" class="px-4 py-8 text-center text-gray-300">No completed attempts yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
