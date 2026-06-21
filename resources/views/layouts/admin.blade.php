<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') | Typing Game Admin</title>
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @vite(['resources/css/app.css'])
    @stack('head')
</head>
<body class="min-h-screen bg-gray-100 text-gray-800 font-sans">

<div class="flex min-h-screen">
    <!-- Sidebar -->
    <aside class="w-60 bg-gray-900 text-gray-200 flex flex-col shrink-0">
        <div class="px-6 py-5 border-b border-gray-700">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2 font-bold text-lg text-white">
                <i data-lucide="keyboard" class="w-5 h-5 text-amber-400"></i>
                Admin Panel
            </a>
        </div>
        <nav class="flex-1 px-4 py-4 space-y-1 text-sm">
            @php
            $navItems = [
                ['route' => 'admin.dashboard', 'icon' => 'layout-dashboard', 'label' => 'Dashboard', 'match' => 'admin.dashboard'],
                ['route' => 'admin.challenges.index', 'icon' => 'trophy', 'label' => 'Challenges', 'match' => 'admin.challenges.*'],
                ['route' => 'admin.typing-texts.index', 'icon' => 'file-text', 'label' => 'Typing Texts', 'match' => 'admin.typing-texts.*'],
                ['route' => 'admin.geofence.index', 'icon' => 'map-pin', 'label' => 'Geofence', 'match' => 'admin.geofence.*'],
                ['route' => 'admin.players.index', 'icon' => 'users', 'label' => 'Players', 'match' => 'admin.players.*'],
                ['route' => 'admin.attempts.index', 'icon' => 'clipboard-list', 'label' => 'Attempts', 'match' => 'admin.attempts.*'],
                ['route' => 'admin.leaderboard.index', 'icon' => 'award', 'label' => 'Leaderboard', 'match' => 'admin.leaderboard.*'],
                ['route' => 'admin.settings.index', 'icon' => 'settings', 'label' => 'Settings', 'match' => 'admin.settings.*'],
            ];
            @endphp
            @foreach($navItems as $item)
            <a href="{{ route($item['route']) }}"
               class="flex items-center gap-2.5 px-3 py-2 rounded-lg hover:bg-gray-700 transition {{ request()->routeIs($item['match']) ? 'bg-gray-700 text-white' : 'text-gray-300' }}">
                <i data-lucide="{{ $item['icon'] }}" class="w-4 h-4 shrink-0"></i>
                {{ $item['label'] }}
            </a>
            @endforeach
        </nav>
        <div class="px-4 py-4 border-t border-gray-700">
            <div class="text-xs text-gray-400 mb-2 flex items-center gap-1.5">
                <i data-lucide="user" class="w-3.5 h-3.5"></i>
                {{ session('admin_name', 'Admin') }}
            </div>
            <form action="{{ route('admin.logout') }}" method="POST">
                @csrf
                <button type="submit" class="flex items-center gap-1.5 text-sm text-red-400 hover:text-red-200 transition">
                    <i data-lucide="log-out" class="w-4 h-4"></i> Logout
                </button>
            </form>
        </div>
    </aside>

    <!-- Main -->
    <div class="flex-1 flex flex-col overflow-hidden">
        <header class="bg-white shadow px-6 py-4 flex items-center justify-between">
            <h1 class="text-lg font-semibold text-gray-700">@yield('page-title', 'Dashboard')</h1>
        </header>

        <main class="flex-1 overflow-auto p-6">
            @if (session('success'))
                <div class="mb-4 bg-green-100 border border-green-300 text-green-700 rounded-lg px-4 py-3 text-sm flex items-center gap-2">
                    <i data-lucide="check-circle" class="w-4 h-4 shrink-0"></i>
                    {{ session('success') }}
                </div>
            @endif
            @if (session('error'))
                <div class="mb-4 bg-red-100 border border-red-300 text-red-700 rounded-lg px-4 py-3 text-sm flex items-center gap-2">
                    <i data-lucide="alert-circle" class="w-4 h-4 shrink-0"></i>
                    {{ session('error') }}
                </div>
            @endif

            @yield('content')
        </main>
    </div>
</div>

@stack('scripts')
<script>lucide.createIcons();</script>
</body>
</html>
