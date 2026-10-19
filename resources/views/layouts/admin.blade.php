<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') | {{ config('app.name') }} Admin</title>
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @vite(['resources/css/app.css'])
    @stack('head')
</head>
<body class="admin-ui">
<a class="skip-link" href="#admin-main">Skip to content</a>

<div class="admin-shell">
    <!-- Sidebar -->
    <aside class="admin-sidebar">
        <div>
            <a href="{{ route('admin.dashboard') }}" class="brand">
                <span class="brand-mark"><i data-lucide="keyboard" aria-hidden="true"></i></span>
                <span class="brand-copy">{{ config('app.name') }}<small>Control room</small></span>
            </a>
        </div>
        <p class="sidebar-label">Your workspace</p>
        <nav class="admin-nav" aria-label="Admin navigation">
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
               @if(request()->routeIs($item['match'])) aria-current="page" @endif>
                <i data-lucide="{{ $item['icon'] }}" aria-hidden="true"></i>
                {{ $item['label'] }}
            </a>
            @endforeach
        </nav>
        <div class="admin-account">
            <div>
                <i data-lucide="user" class="w-3.5 h-3.5"></i>
                {{ session('admin_name', 'Admin') }}
            </div>
            <form action="{{ route('admin.logout') }}" method="POST">
                @csrf
                <button type="submit">
                    <i data-lucide="log-out" class="w-4 h-4"></i> Logout
                </button>
            </form>
        </div>
    </aside>

    <!-- Main -->
    <div class="admin-content">
        <header class="admin-header">
            <div><span class="eyebrow">{{ config('app.name') }} / Workspace</span><h1>@yield('page-title', 'Dashboard')</h1></div>
            <a href="{{ route('home') }}">View game <i data-lucide="arrow-up-right" aria-hidden="true"></i></a>
        </header>

        <main id="admin-main" class="admin-main">
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
