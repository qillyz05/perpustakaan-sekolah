<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Perpustakaan Sekolah')</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex min-h-screen flex-col" x-data="{ menu: false }">
    <header class="sticky top-0 z-40 border-b border-slate-200 bg-white/90 backdrop-blur">
        <div class="mx-auto flex max-w-6xl items-center justify-between gap-4 px-4 py-3">
            <a href="{{ route('home') }}" class="flex items-center gap-2">
                <span class="grid h-9 w-9 place-items-center rounded-xl bg-primary-600 text-lg font-bold text-white">P</span>
                <span class="text-lg font-bold text-slate-900">Perpustakaan<span class="text-primary-600">Sekolah</span></span>
            </a>

            <nav class="hidden items-center gap-1 md:flex">
                <a href="{{ route('home') }}" class="rounded-lg px-3 py-2 text-sm font-medium {{ request()->routeIs('home') ? 'bg-primary-50 text-primary-700' : 'text-slate-600 hover:bg-slate-100' }}">Beranda</a>
                <a href="{{ route('katalog') }}" class="rounded-lg px-3 py-2 text-sm font-medium {{ request()->routeIs('katalog*') ? 'bg-primary-50 text-primary-700' : 'text-slate-600 hover:bg-slate-100' }}">Katalog Buku</a>
                @auth
                    <a href="{{ route('dashboard') }}" class="rounded-lg px-3 py-2 text-sm font-medium text-slate-600 hover:bg-slate-100">Dashboard</a>
                    <form method="POST" action="{{ route('logout') }}" class="ml-1">
                        @csrf
                        <button class="btn-secondary btn-sm">Keluar</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="btn-primary btn-sm ml-1">Masuk</a>
                @endauth
            </nav>

            <button class="grid h-9 w-9 place-items-center rounded-lg border border-slate-200 md:hidden" @click="menu = !menu" aria-label="Menu">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>
        </div>

        <div class="border-t border-slate-100 bg-white px-4 py-2 md:hidden" x-show="menu" x-cloak>
            <a href="{{ route('home') }}" class="block rounded-lg px-3 py-2 text-sm font-medium text-slate-600 hover:bg-slate-100">Beranda</a>
            <a href="{{ route('katalog') }}" class="block rounded-lg px-3 py-2 text-sm font-medium text-slate-600 hover:bg-slate-100">Katalog Buku</a>
            @auth
                <a href="{{ route('dashboard') }}" class="block rounded-lg px-3 py-2 text-sm font-medium text-slate-600 hover:bg-slate-100">Dashboard</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="block w-full rounded-lg px-3 py-2 text-left text-sm font-medium text-rose-600 hover:bg-rose-50">Keluar</button>
                </form>
            @else
                <a href="{{ route('login') }}" class="block rounded-lg px-3 py-2 text-sm font-semibold text-primary-700 hover:bg-primary-50">Masuk</a>
            @endauth
        </div>
    </header>

    <main class="flex-1">
        @yield('content')
    </main>

    <footer class="border-t border-slate-200 bg-white">
        <div class="mx-auto flex max-w-6xl flex-col items-center justify-between gap-2 px-4 py-6 text-sm text-slate-500 sm:flex-row">
            <p>&copy; {{ date('Y') }} Perpustakaan Sekolah. Semua hak dilindungi.</p>
            <p>Sistem Informasi Perpustakaan</p>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>
