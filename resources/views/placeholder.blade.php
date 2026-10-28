<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>{{ $title }} - Dapur Babeh</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gray-100 p-8">
    <div class="mx-auto max-w-xl rounded-xl bg-white p-8 shadow">
        <h1 class="text-xl font-bold">{{ $title }}</h1>
        <p class="mt-2 text-gray-600">
            Login sebagai <strong>{{ auth()->user()->name }}</strong> ({{ auth()->user()->role }})
        </p>

        <form method="POST" action="{{ route('logout') }}" class="mt-6">
            @csrf
            <button class="rounded-lg bg-gray-800 px-4 py-2 text-white">Logout</button>
        </form>
    </div>
</body>
</html>