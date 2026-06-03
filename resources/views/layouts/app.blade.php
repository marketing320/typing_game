<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Typing Game') | Typing Game</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
</head>
<body class="game-ui min-h-screen bg-amber-50 text-gray-800">

    <nav class="bg-amber-800 text-amber-50 px-4 py-4 flex items-center justify-between border-b-4 border-amber-950">
        <a href="{{ route('home') }}" class="flex items-center gap-3 text-xs text-amber-100 hover:text-white transition">
            <i data-lucide="keyboard" class="w-5 h-5 shrink-0"></i>
            <span>Typing Game</span>
        </a>
        <div class="flex gap-5 text-[10px]">
            <a href="{{ route('rehearsal.index') }}" class="flex items-center gap-1.5 hover:text-amber-300 transition">
                <i data-lucide="target" class="w-4 h-4"></i><span class="hidden sm:inline">Rehearsal</span>
            </a>
            <a href="{{ route('challenge.access') }}" class="flex items-center gap-1.5 hover:text-amber-300 transition">
                <i data-lucide="trophy" class="w-4 h-4"></i><span class="hidden sm:inline">Challenge</span>
            </a>
            <a href="{{ route('leaderboard.index') }}" class="flex items-center gap-1.5 hover:text-amber-300 transition">
                <i data-lucide="award" class="w-4 h-4"></i><span class="hidden sm:inline">Leaderboard</span>
            </a>
        </div>
    </nav>

    <main class="min-h-screen">
        @if (session('error'))
            <div class="max-w-2xl mx-auto mt-4 px-4">
                <div class="bg-red-100 border-2 border-red-400 text-red-700 px-4 py-3 text-xs flex items-center gap-2">
                    <i data-lucide="alert-circle" class="w-4 h-4 shrink-0"></i>
                    {{ session('error') }}
                </div>
            </div>
        @endif

        @yield('content')
    </main>

    <footer class="bg-amber-900 text-amber-300 text-center py-6 text-[10px] mt-8 border-t-4 border-amber-950">
        <p>&gt;&gt; Typing Game — Type Fast, Win Big! &lt;&lt;</p>
    </footer>

    @stack('scripts')
    <script>lucide.createIcons();</script>
</body>
</html>
