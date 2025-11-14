<!DOCTYPE html>
<html lang="id" x-data>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Aplikasi Kasir')</title>

    {{-- Tailwind CSS & JS --}}
    @vite('resources/css/app.css')
    @vite('resources/js/app.js')
    {{-- Alpine.js --}}
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <!-- Playfair Display untuk teks elegan -->
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
    <!-- Pacifico untuk "Bakery" playful -->
    <link href="https://fonts.googleapis.com/css2?family=Pacifico&display=swap" rel="stylesheet">

</head>

<body class="bg-gray-100 text-gray-800 min-h-screen flex flex-col">

    {{-- NAVBAR --}}
    <nav class="bg-gray-800 text-white shadow">
        <div class="w-full px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center max-w-full mx-auto">
                <a href="{{ url('/') }}" class="text-xl font-semibold">Kasir</a>
                <div class="flex space-x-6">
                    <a href="{{ route('sales.index') }}"
                        class="hover:text-gray-300 {{ request()->is('sales*') ? 'text-yellow-400' : '' }}">
                        Penjualan
                    </a>
                </div>
            </div>
        </div>
    </nav>

    {{-- MAIN CONTENT --}}
    <main class="flex-1 w-full px-4 sm:px-6 lg:px-8">
        {{-- Flash messages --}}
        @if (session('success'))
            <div class="mb-4 p-4 rounded-lg bg-green-100 border border-green-300 text-green-700" x-data="{ show: true }"
                x-show="show" x-transition>
                {{ session('success') }}
                <button type="button" @click="show = false" class="float-right font-bold">×</button>
            </div>
        @endif

        @if (session('error'))
            <div class="mb-4 p-4 rounded-lg bg-red-100 border border-red-300 text-red-700" x-data="{ show: true }"
                x-show="show" x-transition>
                {{ session('error') }}
                <button type="button" @click="show = false" class="float-right font-bold">×</button>
            </div>
        @endif

        {{-- Halaman Konten --}}
        <div class="max-w-full mx-auto">
            @yield('content')
        </div>
    </main>

    {{-- FOOTER --}}
    <footer class="bg-gray-800 text-gray-400 text-center py-4 mt-8 w-full">
        <small>&copy; {{ date('Y') }} Aplikasi Kasir — Dibuat dengan ❤️ oleh AlFoz Studio</small>
    </footer>

</body>

</html>
