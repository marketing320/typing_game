@extends('layouts.admin')
@section('title', 'Geofence Rules')
@section('page-title', 'Geofence Rules')

@section('content')
<div class="flex justify-between items-center mb-4">
    <p class="text-sm text-gray-400">{{ $rules->total() }} rules</p>
    <a href="{{ route('admin.geofence.create') }}" class="bg-gray-900 text-white text-sm font-bold px-4 py-2 rounded-lg hover:bg-gray-700 transition">+ New Rule</a>
</div>
<div class="bg-white rounded-xl shadow border border-gray-100 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-gray-400 uppercase text-xs">
            <tr>
                <th class="px-4 py-3 text-left">Name</th>
                <th class="px-4 py-3 text-center">Lat / Lng</th>
                <th class="px-4 py-3 text-center">Radius</th>
                <th class="px-4 py-3 text-center">Active</th>
                <th class="px-4 py-3 text-center">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            @forelse($rules as $rule)
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-3 font-semibold">{{ $rule->name }}</td>
                <td class="px-4 py-3 text-center text-xs text-gray-400">{{ $rule->latitude }}, {{ $rule->longitude }}</td>
                <td class="px-4 py-3 text-center">{{ number_format($rule->radius_meters) }}m</td>
                <td class="px-4 py-3 text-center">
                    @if($rule->is_active)
                        <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-green-100"><i data-lucide="check" class="w-3 h-3 text-green-600"></i></span>
                    @else
                        <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-gray-100"><i data-lucide="minus" class="w-3 h-3 text-gray-400"></i></span>
                    @endif
                </td>
                <td class="px-4 py-3 text-center">
                    <div class="flex gap-2 justify-center">
                        <a href="{{ route('admin.geofence.edit', $rule) }}" class="text-blue-500 hover:underline text-xs">Edit</a>
                        <form method="POST" action="{{ route('admin.geofence.destroy', $rule) }}" onsubmit="return confirm('Delete?')">
                            @csrf @method('DELETE')
                            <button class="text-red-400 hover:underline text-xs">Delete</button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="5" class="px-4 py-8 text-center text-gray-300">No geofence rules yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
