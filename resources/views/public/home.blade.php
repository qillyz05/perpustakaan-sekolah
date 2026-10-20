@extends('layouts.app')

@section('title', 'Beranda')

@section('content')
    <section class="relative overflow-hidden bg-slate-900 text-white">
        <div class="absolute inset-0 opacity-20" style="background-image: radial-gradient(circle at 20% 20%, #3d6cee 0, transparent 45%), radial-gradient(circle at 80% 0%, #22c55e 0, transparent 40%);"></div>
        <div class="relative mx-auto max-w-6xl px-4 py-16 lg:py-24">
            <p class="mb-3 inline-flex items-center gap-2 rounded-full bg-white/10 px-3 py-1 text-xs font-medium text-primary-200">
                <span class="h-1.5 w-1.5 rounded-full bg-emerald-400"></span>
                Sistem Informasi Perpustakaan Sekolah
            </p>
            <h1 class="max-w-2xl text-3xl font-extrabold leading-tight sm:text-4xl lg:text-5xl">
                Temukan buku favoritmu di perpustakaan sekolah
            </h1>
            <p class="mt-4 max-w-xl text-slate-300">
                Jelajahi koleksi buku pelajaran, fiksi, sains, dan sejarah. Cek ketersediaan lalu pinjam langsung di meja perpustakaan.
            </p>

            <form action="{{ route('katalog') }}" method="GET" class="mt-8 flex w-full max-w-xl flex-col gap-2 sm:flex-row">
                <div class="relative flex-1">
                    <label for="q-hero" class="sr-only">Cari buku</label>
                    <svg class="pointer-events-none absolute left-3.5 top-1/2 h-5 w-5 -translate-y-1/2 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.3-4.3M17 11a6 6 0 11-12 0 6 6 0 0112 0z"/></svg>
                    <input type="search" id="q-hero" name="q" placeholder="Cari judul, penulis, atau ISBN..." class="input h-12 w-full border-transparent bg-white pl-11 pr-4 text-slate-800">
                </div>
                <button type="submit" class="btn-primary h-12 shrink-0 px-6">Cari</button>
            </form>
        </div>
    </section>

    <section class="relative z-10 mx-auto mt-6 max-w-6xl px-4 sm:-mt-8">
        <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
            @foreach ([
                ['label' => 'Judul Buku', 'value' => $totalBuku, 'color' => 'text-primary-600'],
                ['label' => 'Total Eksemplar', 'value' => $totalEksemplar, 'color' => 'text-emerald-600'],
                ['label' => 'Anggota Siswa', 'value' => $totalAnggota, 'color' => 'text-amber-600'],
                ['label' => 'Kategori', 'value' => $totalKategori, 'color' => 'text-indigo-600'],
            ] as $stat)
                <div class="card p-5">
                    <p class="text-3xl font-extrabold {{ $stat['color'] }}">{{ number_format($stat['value']) }}</p>
                    <p class="mt-1 text-sm text-slate-500">{{ $stat['label'] }}</p>
                </div>
            @endforeach
        </div>
    </section>

    <section class="mx-auto max-w-6xl px-4 py-14">
        <div class="mb-6 flex items-end justify-between">
            <div>
                <h2 class="text-xl font-bold text-slate-900">Koleksi Terbaru</h2>
                <p class="text-sm text-slate-500">Buku yang baru ditambahkan ke perpustakaan</p>
            </div>
            <a href="{{ route('katalog') }}" class="text-sm font-semibold text-primary-600 hover:text-primary-700">Lihat semua &rarr;</a>
        </div>

        @if ($unggulan->isEmpty())
            <x-empty-state title="Belum ada koleksi" description="Koleksi buku belum ditambahkan oleh pustakawan." />
        @else
            <div class="grid grid-cols-2 gap-5 sm:grid-cols-3 lg:grid-cols-4">
                @foreach ($unggulan as $item)
                    <a href="{{ route('katalog.show', $item) }}" class="card group overflow-hidden p-4 transition hover:-translate-y-1 hover:shadow-md">
                        <div class="flex justify-center">
                            <x-cover-buku :buku="$item" />
                        </div>
                        <div class="mt-3">
                            <span class="badge bg-primary-50 text-primary-700">{{ $item->kategori->nama }}</span>
                            <h3 class="mt-2 line-clamp-2 text-sm font-semibold text-slate-800 group-hover:text-primary-700">{{ $item->judul }}</h3>
                            <p class="mt-1 line-clamp-1 text-xs text-slate-500">{{ $item->penulis }}</p>
                        </div>
                    </a>
                @endforeach
            </div>
        @endif
    </section>

    <section class="border-t border-slate-200 bg-white">
        <div class="mx-auto max-w-6xl px-4 py-14">
            <h2 class="mb-2 text-xl font-bold text-slate-900">Kategori Koleksi</h2>
            <p class="mb-6 text-sm text-slate-500">Telusuri buku berdasarkan kategori</p>
            <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-6">
                @foreach ($kategori as $kat)
                    <a href="{{ route('katalog', ['kategori' => $kat->slug]) }}" class="card p-4 text-center transition hover:border-primary-300 hover:shadow-md">
                        <span class="mx-auto grid h-11 w-11 place-items-center rounded-xl bg-primary-50 text-primary-600">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 5h16v14H4zM4 9h16"/></svg>
                        </span>
                        <p class="mt-2 text-sm font-semibold text-slate-800">{{ $kat->nama }}</p>
                        <p class="text-xs text-slate-500">{{ $kat->buku_count }} buku</p>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    <section class="mx-auto max-w-6xl px-4 py-14">
        <h2 class="mb-2 text-xl font-bold text-slate-900">Paling Banyak Dipinjam</h2>
        <p class="mb-6 text-sm text-slate-500">Buku favorit para siswa</p>
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            @forelse ($terpopuler as $item)
                <a href="{{ route('katalog.show', $item) }}" class="card flex items-center gap-3 p-4 transition hover:shadow-md">
                    <x-cover-buku :buku="$item" size="sm" />
                    <div class="min-w-0">
                        <h3 class="line-clamp-2 text-sm font-semibold text-slate-800">{{ $item->judul }}</h3>
                        <p class="mt-1 line-clamp-1 text-xs text-slate-500">{{ $item->penulis }}</p>
                        <p class="mt-1 text-xs font-medium text-primary-600">{{ $item->peminjaman_count }}x dipinjam</p>
                    </div>
                </a>
            @empty
                <x-empty-state class="sm:col-span-2 lg:col-span-4" title="Belum ada data" description="Belum ada riwayat peminjaman." />
            @endforelse
        </div>
    </section>

    <section class="bg-primary-600">
        <div class="mx-auto flex max-w-6xl flex-col items-center gap-4 px-4 py-10 text-center text-white sm:flex-row sm:justify-between sm:text-left">
            <div>
                <h2 class="text-lg font-bold">Ingin meminjam buku?</h2>
                <p class="text-sm text-primary-100">Cek ketersediaan buku lalu datang ke perpustakaan dengan kartu anggota.</p>
            </div>
            <a href="{{ route('katalog') }}" class="btn bg-white px-6 text-primary-700 hover:bg-primary-50">Jelajahi Katalog</a>
        </div>
    </section>
@endsection
