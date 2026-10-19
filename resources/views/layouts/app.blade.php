<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Title --}}
    <title>@yield('title', config('app.name')) | {{ config('app.name') }}</title>

    {{-- Core SEO --}}
    <meta name="description" content="@yield('meta_description', 'Practice typing, enter a challenge, and compare your speed and accuracy on the leaderboard.')">
    <meta name="robots" content="@yield('meta_robots', 'index, follow')">
    <link rel="canonical" href="{{ request()->url() }}">

    {{-- Open Graph --}}
    <meta property="og:type"        content="@yield('og_type', 'website')">
    <meta property="og:site_name"   content="{{ config('app.name') }}">
    <meta property="og:title"       content="@yield('title', config('app.name')) | {{ config('app.name') }}">
    <meta property="og:description" content="@yield('meta_description', 'Practice typing, enter a challenge, and compare your speed and accuracy on the leaderboard.')">
    <meta property="og:url"         content="{{ request()->url() }}">
    @hasSection('og_image')
    <meta property="og:image"       content="@yield('og_image')">
    <meta property="og:image:alt"   content="@yield('title', config('app.name'))">
    @endif

    {{-- Twitter Card --}}
    <meta name="twitter:card"        content="@yield('twitter_card', 'summary')">
    <meta name="twitter:title"       content="@yield('title', config('app.name')) | {{ config('app.name') }}">
    <meta name="twitter:description" content="@yield('meta_description', 'Practice typing, enter a challenge, and compare your speed and accuracy on the leaderboard.')">
    @hasSection('og_image')
    <meta name="twitter:image"       content="@yield('og_image')">
    @endif

    {{-- WordSprint --}}
    <link rel="icon" type="image/x-icon" href="{{ asset('WordSprint-icon.png') }}">

    {{-- Fonts --}}

    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
</head>
<body class="game-ui arcade-ui min-h-screen {{ request()->routeIs('home') ? 'is-title-screen' : '' }}">
    <a class="skip-link" href="#main-content">Skip to content</a>

    <div class="arcade-surround" aria-hidden="true"><span>WORDSPRINT</span><span>TYPING ARCADE</span></div>
    <div class="arcade-cabinet" id="arcade-cabinet">
    <header class="site-header">
        <div class="nav-shell">
            <a href="{{ route('home') }}" class="brand" aria-label="{{ config('app.name') }} home">
                <span class="brand-mark"><i data-lucide="keyboard" aria-hidden="true"></i></span>
                <span class="brand-copy">{{ config('app.name') }}<small>Find your flow</small></span>
            </a>
            <nav class="site-nav" aria-label="Main navigation">
                <a href="{{ route('rehearsal.index') }}" @if(request()->routeIs('rehearsal.*')) aria-current="page" @endif>
                    <i data-lucide="command" aria-hidden="true"></i><span>Practice</span>
                </a>
                <a href="{{ route('challenge.access') }}" @if(request()->routeIs('challenge.*')) aria-current="page" @endif>
                    <i data-lucide="zap" aria-hidden="true"></i><span>Challenge</span>
                </a>
                <a href="{{ route('leaderboard.index') }}" @if(request()->routeIs('leaderboard.*')) aria-current="page" @endif>
                    <i data-lucide="trophy" aria-hidden="true"></i><span>Leaderboard</span>
                </a>
            </nav>
            <button type="button" class="cabinet-fullscreen" data-fullscreen-toggle hidden aria-label="Enter fullscreen" aria-pressed="false" title="Enter fullscreen">
                <i data-lucide="maximize" aria-hidden="true"></i><span data-fullscreen-label>Fullscreen</span>
            </button>
        </div>
    </header>

    <main id="main-content" class="site-main">
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

    <footer class="site-footer">
        <p class="footer-tagline"><i data-lucide="keyboard" aria-hidden="true"></i><span>Type with your keyboard</span></p>
        <a href="{{ route('home') }}"><i data-lucide="house" aria-hidden="true"></i> Main menu</a>
    </footer>
    <p class="sr-only" role="status" data-fullscreen-status></p>
    </div>

    @stack('scripts')
    <script>lucide.createIcons();</script>
</body>
</html>
