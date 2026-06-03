@extends('layouts.admin')
@section('title', 'Attempts')
@section('page-title', 'Attempts')

@section('content')
<!-- Filters -->
<form method="GET" class="bg-white rounded-xl shadow border border-gray-100 p-4 mb-4 grid grid-cols-2 md:grid-cols-4 gap-3">
    <select name="challenge_id" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-blue-400">
        <option value="">All Challenges</option>
        @foreach($challenges as $c)
        <option value="{{ $c->id }}" {{ request('challenge_id') == $c->id ? 'selected' : '' }}>{{ $c->title }}</option>
        @endforeach
    </select>
    <select name="status" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-blue-400">
        <option value="">All Statuses</option>
        @foreach(['started', 'completed', 'failed', 'disqualified'] as $s)
        <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
        @endforeach
    </select>
    <input type="date" name="date" value="{{ request('date') }}" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-blue-400">
    <button type="submit" class="bg-gray-800 text-white text-sm rounded-lg hover:bg-gray-700 transition">Filter</button>
</form>

<div class="bg-white rounded-xl shadow border border-gray-100 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-gray-400 uppercase text-xs">
            <tr>
                <th class="px-4 py-3 text-left">Player</th>
                <th class="px-4 py-3 text-left hidden md:table-cell">Challenge</th>
                <th class="px-4 py-3 text-right">WPM</th>
                <th class="px-4 py-3 text-right">Accuracy</th>
                <th class="px-4 py-3 text-center">Status</th>
                <th class="px-4 py-3 text-center hidden lg:table-cell">Geofence</th>
                <th class="px-4 py-3 text-right">Date</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            @forelse($attempts as $a)
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-3 font-semibold">{{ $a->player->username ?? '—' }}</td>
                <td class="px-4 py-3 text-xs text-gray-400 hidden md:table-cell">{{ Str::limit($a->challenge->title ?? '—', 25) }}</td>
                <td class="px-4 py-3 text-right font-bold">{{ $a->wpm }}</td>
                <td class="px-4 py-3 text-right text-green-600">{{ $a->accuracy }}%</td>
                <td class="px-4 py-3 text-center">
                    <span class="px-2 py-0.5 text-xs rounded-full
                        {{ $a->status === 'completed' ? 'bg-green-100 text-green-700' : '' }}
                        {{ $a->status === 'started' ? 'bg-blue-100 text-blue-700' : '' }}
                        {{ $a->status === 'failed' ? 'bg-red-100 text-red-700' : '' }}
                        {{ $a->status === 'disqualified' ? 'bg-gray-100 text-gray-500' : '' }}">
                        {{ ucfirst($a->status) }}
                    </span>
                </td>
                <td class="px-4 py-3 text-center hidden lg:table-cell">
                    @if($a->is_within_geofence === null)
                        <span class="text-gray-300">—</span>
                    @elseif($a->is_within_geofence)
                        <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-green-100"><i data-lucide="check" class="w-3 h-3 text-green-600"></i></span>
                    @else
                        <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-red-100"><i data-lucide="x" class="w-3 h-3 text-red-500"></i></span>
                    @endif
                </td>
                <td class="px-4 py-3 text-right text-xs text-gray-400">{{ $a->created_at->format('d M Y H:i') }}</td>
            </tr>
            @empty
            <tr><td colspan="7" class="px-4 py-8 text-center text-gray-300">No attempts found.</td></tr>
            @endforelse
        </tbody>
    </table>
    @if($attempts->hasPages())
    <div class="px-4 py-3 border-t border-gray-100">{{ $attempts->appends(request()->query())->links() }}</div>
    @endif
</div>
@endsection
