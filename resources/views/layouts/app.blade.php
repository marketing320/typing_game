<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Title --}}
    <title>@yield('title', config('app.name')) | {{ config('app.name') }}</title>

    {{-- Core SEO --}}
    <meta name="description" content="@yield('meta_description', 'Test your typing speed and accuracy in a live challenge. Compete, climb the leaderboard, and prove your skills!')">
    <meta name="robots" content="@yield('meta_robots', 'index, follow')">
    <link rel="canonical" href="{{ request()->url() }}">

    {{-- Open Graph --}}
    <meta property="og:type"        content="@yield('og_type', 'website')">
    <meta property="og:site_name"   content="{{ config('app.name') }}">
    <meta property="og:title"       content="@yield('title', config('app.name')) | {{ config('app.name') }}">
    <meta property="og:description" content="@yield('meta_description', 'Test your typing speed and accuracy in a live challenge. Compete, climb the leaderboard, and prove your skills!')">
    <meta property="og:url"         content="{{ request()->url() }}">
    @hasSection('og_image')
    <meta property="og:image"       content="@yield('og_image')">
    <meta property="og:image:alt"   content="@yield('title', config('app.name'))">
    @endif

    {{-- Twitter Card --}}
    <meta name="twitter:card"        content="@yield('twitter_card', 'summary')">
    <meta name="twitter:title"       content="@yield('title', config('app.name')) | {{ config('app.name') }}">
    <meta name="twitter:description" content="@yield('meta_description', 'Test your typing speed and accuracy in a live challenge. Compete, climb the leaderboard, and prove your skills!')">
    @hasSection('og_image')
    <meta name="twitter:image"       content="@yield('og_image')">
    @endif

    {{-- Favicon --}}
    <link rel="icon" type="image/x-icon" href="/favicon.ico">
    <link rel="shortcut icon" href="/favicon.ico">

    {{-- Fonts --}}
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
            <span>{{ config('app.name') }}</span>
        </a>
        <div class="flex gap-5 text-[10px]">
            <a href="{{ route('rehearsal.index') }}" class="flex items-center gap-1.5 hover:text-amber-300 transition">
                <i data-lucide="target" class="w-4 h-4"></i><span class="hidden sm:inline">Practice</span>
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
        <p>&gt;&gt; {{ config('app.name') }}! Type Fast, Win Big! &lt;&lt;</p>
    </footer>

    @stack('scripts')
    <script>lucide.createIcons();</script>
</body>
</html>
