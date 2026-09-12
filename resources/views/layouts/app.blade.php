<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Xiaoxa')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-[#fbfaf8] text-gray-900 min-h-screen">
    {{-- NAVBAR STICKY --}}
    <div class="sticky top-0 z-50">
        <x-navbar />
    </div>

    <main class="flex-1">
        @yield('content')
    </main>

    {{-- FOOTER --}}
    <x-footer />
</body>
</html>