@extends('layouts.admin')
@section('title', 'Typing Texts')
@section('page-title', 'Typing Texts')

@section('content')
<div class="flex justify-between items-center mb-4">
    <p class="text-sm text-gray-400">{{ $texts->total() }} total</p>
    <a href="{{ route('admin.typing-texts.create') }}" class="bg-gray-900 text-white text-sm font-bold px-4 py-2 rounded-lg hover:bg-gray-700 transition">+ New Text</a>
</div>

<div class="bg-white rounded-xl shadow border border-gray-100 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-gray-400 uppercase text-xs">
            <tr>
                <th class="px-4 py-3 text-left">Title</th>
                <th class="px-4 py-3 text-center">Mode</th>
                <th class="px-4 py-3 text-center">Difficulty</th>
                <th class="px-4 py-3 text-center">Active</th>
                <th class="px-4 py-3 text-left">Challenge</th>
                <th class="px-4 py-3 text-center">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            @forelse($texts as $t)
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-3 font-semibold">{{ $t->title ?? '—' }}</td>
                <td class="px-4 py-3 text-center">
                    <span class="px-2 py-0.5 text-xs rounded-full {{ $t->mode === 'challenge' ? 'bg-purple-100 text-purple-700' : 'bg-blue-100 text-blue-700' }}">
                        {{ ucfirst($t->mode) }}
                    </span>
                </td>
                <td class="px-4 py-3 text-center capitalize text-xs text-gray-400">{{ $t->difficulty }}</td>
                <td class="px-4 py-3 text-center">
                    @if($t->is_active)
                        <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-green-100"><i data-lucide="check" class="w-3 h-3 text-green-600"></i></span>
                    @else
                        <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-gray-100"><i data-lucide="minus" class="w-3 h-3 text-gray-400"></i></span>
                    @endif
                </td>
                <td class="px-4 py-3 text-xs text-gray-400">{{ Str::limit($t->challenge?->title ?? 'Practice', 30) }}</td>
                <td class="px-4 py-3 text-center">
                    <div class="flex gap-2 justify-center">
                        <a href="{{ route('admin.typing-texts.edit', $t) }}" class="text-blue-500 hover:underline text-xs">Edit</a>
                        <form method="POST" action="{{ route('admin.typing-texts.destroy', $t) }}" onsubmit="return confirm('Delete?')">
                            @csrf @method('DELETE')
                            <button class="text-red-400 hover:underline text-xs">Delete</button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="6" class="px-4 py-8 text-center text-gray-300">No texts yet.</td></tr>
            @endforelse
        </tbody>
    </table>
    @if($texts->hasPages())
    <div class="px-4 py-3 border-t border-gray-100">{{ $texts->links() }}</div>
    @endif
</div>
@endsection
