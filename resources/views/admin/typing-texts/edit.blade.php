@extends('layouts.admin')
@section('title', 'Edit Typing Text')
@section('page-title', 'Edit Typing Text')

@section('content')
<div class="max-w-2xl">
<form method="POST" action="{{ route('admin.typing-texts.update', $typingText) }}" class="bg-white rounded-xl shadow border border-gray-100 p-6 space-y-4">
    @csrf @method('PUT')
    @include('admin.typing-texts._form', ['typingText' => $typingText])
    <div class="flex gap-3 pt-2">
        <button type="submit" class="bg-gray-900 text-white font-bold px-6 py-2.5 rounded-lg hover:bg-gray-700 transition">Update</button>
        <a href="{{ route('admin.typing-texts.index') }}" class="text-gray-500 px-4 py-2.5 rounded-lg hover:bg-gray-100 transition text-sm">Cancel</a>
    </div>
</form>
</div>
@endsection
