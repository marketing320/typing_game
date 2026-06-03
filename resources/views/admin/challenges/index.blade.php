@extends('layouts.admin')
@section('title', 'Challenges')
@section('page-title', 'Challenges')

@section('content')
<div class="flex justify-between items-center mb-4">
    <p class="text-sm text-gray-400">{{ $challenges->total() }} total</p>
    <a href="{{ route('admin.challenges.create') }}" class="bg-gray-900 text-white text-sm font-bold px-4 py-2 rounded-lg hover:bg-gray-700 transition">+ New Challenge</a>
</div>

<div class="bg-white rounded-xl shadow border border-gray-100 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-gray-400 uppercase text-xs">
            <tr>
                <th class="px-4 py-3 text-left">Title</th>
                <th class="px-4 py-3 text-center">Status</th>
                <th class="px-4 py-3 text-center">Geofence</th>
                <th class="px-4 py-3 text-center">Retry</th>
                <th class="px-4 py-3 text-right">Created</th>
                <th class="px-4 py-3 text-center">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            @forelse($challenges as $c)
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-3 font-semibold">{{ $c->title }}</td>
                <td class="px-4 py-3 text-center">
                    <span class="px-2 py-0.5 text-xs rounded-full
                        {{ $c->status === 'active' ? 'bg-green-100 text-green-700' : '' }}
                        {{ $c->status === 'draft' ? 'bg-yellow-100 text-yellow-700' : '' }}
                        {{ $c->status === 'ended' ? 'bg-gray-100 text-gray-500' : '' }}">
                        {{ ucfirst($c->status) }}
                    </span>
                </td>
                <td class="px-4 py-3 text-center">
                    @if($c->require_geofence)
                        <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-green-100"><i data-lucide="check" class="w-3 h-3 text-green-600"></i></span>
                    @else
                        <span class="text-gray-300">—</span>
                    @endif
                </td>
                <td class="px-4 py-3 text-center">
                    @if($c->allow_retry_next_day)
                        <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-green-100"><i data-lucide="check" class="w-3 h-3 text-green-600"></i></span>
                    @else
                        <span class="text-gray-300">—</span>
                    @endif
                </td>
                <td class="px-4 py-3 text-right text-gray-400 text-xs">{{ $c->created_at->format('d M Y') }}</td>
                <td class="px-4 py-3 text-center">
                    <div class="flex gap-2 justify-center">
                        <a href="{{ route('admin.challenges.edit', $c) }}" class="text-blue-500 hover:underline text-xs">Edit</a>
                        <form method="POST" action="{{ route('admin.challenges.destroy', $c) }}" onsubmit="return confirm('Delete this challenge?')">
                            @csrf @method('DELETE')
                            <button class="text-red-400 hover:underline text-xs">Delete</button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="6" class="px-4 py-8 text-center text-gray-300">No challenges yet.</td></tr>
            @endforelse
        </tbody>
    </table>
    @if($challenges->hasPages())
    <div class="px-4 py-3 border-t border-gray-100">{{ $challenges->links() }}</div>
    @endif
</div>
@endsection
