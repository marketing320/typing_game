<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login | Typing Monkey</title>
    @vite('resources/css/app.css')
</head>
<body class="min-h-screen bg-gray-900 flex items-center justify-center p-4">
<div class="w-full max-w-sm">
    <div class="text-center mb-8 text-white">
        <div class="text-5xl mb-2">🐒</div>
        <h1 class="text-2xl font-bold">Typing Monkey Admin</h1>
    </div>
    <div class="bg-white rounded-2xl shadow-xl p-8">
        @if ($errors->any())
        <div class="mb-4 bg-red-50 border border-red-200 text-red-600 rounded-lg px-4 py-3 text-sm">
            {{ $errors->first() }}
        </div>
        @endif
        <form method="POST" action="{{ route('admin.login.post') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" required autofocus
                    class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:border-blue-400 text-sm">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Password</label>
                <input type="password" name="password" required
                    class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:border-blue-400 text-sm">
            </div>
            <button type="submit" class="w-full bg-gray-900 text-white font-bold py-2.5 rounded-lg hover:bg-gray-700 transition mt-2">
                Sign In
            </button>
        </form>
    </div>
</div>
</body>
</html>
