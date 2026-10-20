<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') &middot; Perpustakaan Sekolah</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body x-data="{ sidebar: false }">
    <div class="min-h-screen lg:flex">
        <aside class="fixed inset-y-0 left-0 z-50 w-72 -translate-x-full bg-slate-900 text-slate-300 transition-transform lg:static lg:translate-x-0"
               :class="sidebar ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'">
            <div class="flex h-16 items-center gap-2 border-b border-white/10 px-5">
                <span class="grid h-9 w-9 place-items-center rounded-xl bg-primary-500 font-bold text-white">P</span>
                <div class="leading-tight">
                    <p class="text-sm font-bold text-white">Perpustakaan</p>
                    <p class="text-xs text-slate-400">Panel Pustakawan</p>
                </div>
            </div>

            <nav class="space-y-1 p-4">
                @php
                    $nav = [
                        ['route' => 'admin.dashboard', 'label' => 'Dashboard', 'icon' => 'M3 12l9-9 9 9M5 10v10h5v-6h4v6h5V10'],
                        ['route' => 'admin.buku.index', 'label' => 'Data Buku', 'icon' => 'M4 5h16v14H4zM8 5v14'],
                        ['route' => 'admin.kategori.index', 'label' => 'Kategori', 'icon' => 'M4 6h16M4 12h16M4 18h10'],
                        ['route' => 'admin.siswa.index', 'label' => 'Anggota', 'icon' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z'],
                        ['route' => 'admin.peminjaman.index', 'label' => 'Peminjaman', 'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5l2 4h5a2 2 0 012 2v12a2 2 0 01-2 2z'],
                        ['route' => 'admin.laporan.index', 'label' => 'Laporan', 'icon' => 'M9 17V9m4 8V5m4 12v-6M5 21h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v14a2 2 0 002 2z'],
                        ['route' => 'admin.pengaturan.edit', 'label' => 'Pengaturan', 'icon' => 'M10.3 4.3a1.7 1.7 0 013.4 0l.1.6a1.7 1.7 0 002.5 1l.5-.3a1.7 1.7 0 012.4 2.4l-.3.5a1.7 1.7 0 001 2.5l.6.1a1.7 1.7 0 010 3.4l-.6.1a1.7 1.7 0 00-1 2.5l.3.5a1.7 1.7 0 01-2.4 2.4l-.5-.3a1.7 1.7 0 00-2.5 1l-.1.6a1.7 1.7 0 01-3.4 0l-.1-.6a1.7 1.7 0 00-2.5-1l-.5.3a1.7 1.7 0 01-2.4-2.4l.3-.5a1.7 1.7 0 00-1-2.5l-.6-.1a1.7 1.7 0 010-3.4l.6-.1a1.7 1.7 0 001-2.5l-.3-.5A1.7 1.7 0 017 1.9l.5.3a1.7 1.7 0 002.5-1z'],
                    ];
                @endphp

                @foreach ($nav as $item)
                    <a href="{{ route($item['route']) }}"
                       class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition {{ request()->routeIs(str_replace('.index', '.*', $item['route'])) || request()->routeIs($item['route']) ? 'bg-primary-600 text-white' : 'hover:bg-white/5 hover:text-white' }}">
                        <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $item['icon'] }}"/></svg>
                        {{ $item['label'] }}
                    </a>
                @endforeach
            </nav>

            <div class="absolute bottom-0 w-full border-t border-white/10 p-4">
                <div class="mb-3 flex items-center gap-3">
                    <span class="grid h-9 w-9 place-items-center rounded-full bg-primary-500/20 text-sm font-semibold text-primary-300">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                    <div class="min-w-0 leading-tight">
                        <p class="truncate text-sm font-medium text-white">{{ auth()->user()->name }}</p>
                        <p class="truncate text-xs capitalize text-slate-400">{{ auth()->user()->role }}</p>
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="btn w-full bg-white/10 text-sm text-white hover:bg-white/20">Keluar</button>
                </form>
            </div>
        </aside>

        <div class="fixed inset-0 z-40 bg-slate-900/50 lg:hidden" x-show="sidebar" x-cloak @click="sidebar = false"></div>

        <div class="flex min-w-0 flex-1 flex-col">
            <header class="sticky top-0 z-30 flex h-16 items-center gap-4 border-b border-slate-200 bg-white px-4 lg:px-8">
                <button class="grid h-9 w-9 place-items-center rounded-lg border border-slate-200 lg:hidden" @click="sidebar = true" aria-label="Buka menu">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
                <div>
                    <h1 class="text-lg font-bold text-slate-900">@yield('heading', 'Dashboard')</h1>
                    <p class="text-xs text-slate-500">@yield('subheading', 'Panel Pustakawan')</p>
                </div>
                <a href="{{ route('home') }}" target="_blank" class="btn-secondary btn-sm ml-auto">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                    Lihat Situs
                </a>
            </header>

            <main class="flex-1 px-4 py-6 lg:px-8">
                @yield('content')
            </main>
        </div>
    </div>

    @stack('scripts')
</body>
</html>
