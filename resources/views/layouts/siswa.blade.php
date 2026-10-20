<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') &middot; Perpustakaan Sekolah</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body x-data="{ menu: false }">
    <header class="sticky top-0 z-40 border-b border-slate-200 bg-white/90 backdrop-blur">
        <div class="mx-auto flex max-w-5xl items-center gap-4 px-4 py-3">
            <a href="{{ route('home') }}" class="flex items-center gap-2">
                <span class="grid h-9 w-9 place-items-center rounded-xl bg-primary-600 font-bold text-white">P</span>
                <span class="hidden text-lg font-bold text-slate-900 sm:block">Perpustakaan<span class="text-primary-600">Sekolah</span></span>
            </a>
            <nav class="ml-2 hidden items-center gap-1 md:flex">
                <a href="{{ route('siswa.dashboard') }}" class="rounded-lg px-3 py-2 text-sm font-medium {{ request()->routeIs('siswa.dashboard') ? 'bg-primary-50 text-primary-700' : 'text-slate-600 hover:bg-slate-100' }}">Dashboard</a>
                <a href="{{ route('siswa.riwayat') }}" class="rounded-lg px-3 py-2 text-sm font-medium {{ request()->routeIs('siswa.riwayat') ? 'bg-primary-50 text-primary-700' : 'text-slate-600 hover:bg-slate-100' }}">Riwayat</a>
                <a href="{{ route('katalog') }}" class="rounded-lg px-3 py-2 text-sm font-medium text-slate-600 hover:bg-slate-100">Katalog</a>
            </nav>
            <div class="ml-auto flex items-center gap-2">
                <span class="hidden text-sm text-slate-500 sm:block">{{ auth()->user()->name }}</span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="btn-secondary btn-sm">Keluar</button>
                </form>
                <button class="grid h-9 w-9 place-items-center rounded-lg border border-slate-200 md:hidden" @click="menu = !menu" aria-label="Menu">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
            </div>
        </div>
        <div class="border-t border-slate-100 bg-white px-4 py-2 md:hidden" x-show="menu" x-cloak>
            <a href="{{ route('siswa.dashboard') }}" class="block rounded-lg px-3 py-2 text-sm font-medium text-slate-600 hover:bg-slate-100">Dashboard</a>
            <a href="{{ route('siswa.riwayat') }}" class="block rounded-lg px-3 py-2 text-sm font-medium text-slate-600 hover:bg-slate-100">Riwayat</a>
            <a href="{{ route('katalog') }}" class="block rounded-lg px-3 py-2 text-sm font-medium text-slate-600 hover:bg-slate-100">Katalog</a>
        </div>
    </header>

    <main class="mx-auto max-w-5xl px-4 py-8">
        @yield('content')
    </main>

    @stack('scripts')
</body>
</html>
