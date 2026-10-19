<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login | {{ config('app.name') }}</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('wordsprint.ico') }}">
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>
    @vite('resources/css/app.css')
</head>
<body class="admin-ui admin-login">
<div class="login-shell">
    <div class="login-brand">
        <a href="{{ route('home') }}" class="brand"><span class="brand-mark"><i data-lucide="keyboard" aria-hidden="true"></i></span><span class="brand-copy">{{ config('app.name') }}<small>Control room</small></span></a>
    </div>
    <div class="login-card">
        <h1>Admin sign in</h1>
        <p>Manage challenges, players, and results.</p>
        @if ($errors->any())
        <div class="mb-4 bg-red-50 border border-red-200 text-red-600 rounded-lg px-4 py-3 text-sm">
            {{ $errors->first() }}
        </div>
        @endif
        <form method="POST" action="{{ route('admin.login.post') }}" class="space-y-4">
            @csrf
            <div>
                <label for="admin-email" class="block text-sm font-semibold text-gray-700 mb-1">Email</label>
                <input type="email" id="admin-email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                    class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:border-blue-400 text-sm">
            </div>
            <div>
                <label for="admin-password" class="block text-sm font-semibold text-gray-700 mb-1">Password</label>
                <input type="password" id="admin-password" name="password" required autocomplete="current-password"
                    class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:border-blue-400 text-sm">
            </div>
            <button type="submit" class="w-full bg-gray-900 text-white font-bold py-2.5 rounded-lg hover:bg-gray-700 transition mt-2">
                Sign In
            </button>
        </form>
    </div>
</div>
<script>lucide.createIcons();</script>
</body>
</html>
