@extends('layouts.app')

@section('title', 'Katalog Buku')

@section('content')
    <div class="border-b border-slate-200 bg-white">
        <div class="mx-auto max-w-6xl px-4 py-8">
            <nav class="mb-2 text-sm text-slate-500">
                <a href="{{ route('home') }}" class="hover:text-primary-600">Beranda</a>
                <span class="mx-1">/</span>
                <span class="text-slate-700">Katalog</span>
            </nav>
            <h1 class="text-2xl font-bold text-slate-900">Katalog Buku</h1>
            <p class="mt-1 text-sm text-slate-500">Total {{ $buku->total() }} buku ditemukan</p>
        </div>
    </div>

    <div class="mx-auto max-w-6xl px-4 py-8">
        <form method="GET" class="card mb-6 grid gap-3 p-4 sm:grid-cols-2 lg:grid-cols-4">
            <div class="sm:col-span-2">
                <label class="label" for="q">Pencarian</label>
                <div class="relative">
                    <svg class="pointer-events-none absolute left-3.5 top-1/2 h-5 w-5 -translate-y-1/2 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.3-4.3M17 11a6 6 0 11-12 0 6 6 0 0112 0z"/></svg>
                    <input type="search" id="q" name="q" value="{{ request('q') }}" placeholder="Judul, penulis, penerbit, ISBN..." class="input pl-11">
                </div>
            </div>
            <div>
                <label class="label" for="kategori">Kategori</label>
                <select id="kategori" name="kategori" class="input">
                    <option value="">Semua kategori</option>
                    @foreach ($kategori as $kat)
                        <option value="{{ $kat->slug }}" @selected(request('kategori') === $kat->slug)>{{ $kat->nama }} ({{ $kat->buku_count }})</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="label" for="sort">Urutkan</label>
                <select id="sort" name="sort" class="input">
                    <option value="baru" @selected(request('sort') === 'baru')>Terbaru</option>
                    <option value="terlama" @selected(request('sort') === 'terlama')>Terlama</option>
                    <option value="judul" @selected(request('sort') === 'judul')>Judul A-Z</option>
                </select>
            </div>
            <div class="flex items-center gap-3 sm:col-span-2 lg:col-span-4">
                <label class="inline-flex cursor-pointer items-center gap-2 text-sm text-slate-600">
                    <input type="checkbox" name="tersedia" value="1" @checked(request('tersedia') === '1') class="rounded border-slate-300 text-primary-600 focus:ring-primary-500">
                    Hanya tampilkan yang tersedia
                </label>
                <div class="ml-auto flex gap-2">
                    @if (request()->hasAny(['q', 'kategori', 'sort', 'tersedia']))
                        <a href="{{ route('katalog') }}" class="btn-secondary btn-sm">Reset</a>
                    @endif
                    <button class="btn-primary btn-sm">Terapkan</button>
                </div>
            </div>
        </form>

        <x-alert />

        @if ($buku->isEmpty())
            <x-empty-state title="Buku tidak ditemukan" description="Coba ubah kata kunci pencarian atau filter kategori Anda.">
                <x-slot:action>
                    <a href="{{ route('katalog') }}" class="btn-secondary btn-sm">Lihat semua buku</a>
                </x-slot:action>
            </x-empty-state>
        @else
            <div class="grid grid-cols-2 gap-5 sm:grid-cols-3 lg:grid-cols-4">
                @foreach ($buku as $item)
                    <a href="{{ route('katalog.show', $item) }}" class="card group flex flex-col overflow-hidden p-4 transition hover:-translate-y-1 hover:shadow-md">
                        <div class="flex justify-center">
                            <x-cover-buku :buku="$item" />
                        </div>
                        <div class="mt-3 flex flex-1 flex-col">
                            <span class="badge w-fit bg-primary-50 text-primary-700">{{ $item->kategori->nama }}</span>
                            <h2 class="mt-2 line-clamp-2 text-sm font-semibold text-slate-800 group-hover:text-primary-700">{{ $item->judul }}</h2>
                            <p class="mt-1 line-clamp-1 text-xs text-slate-500">{{ $item->penulis }}</p>
                            <div class="mt-auto pt-3">
                                <x-badge-status :status="$item->tersedia > 0 ? 'tersedia' : 'habis'" />
                                <span class="ml-1 text-xs text-slate-500">{{ $item->tersedia }}/{{ $item->jumlah_eksemplar }}</span>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>

            <x-pagination :items="$buku" />
        @endif
    </div>
@endsection
