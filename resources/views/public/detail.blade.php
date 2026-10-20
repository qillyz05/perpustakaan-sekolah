@extends('layouts.app')

@section('title', $buku->judul)

@section('content')
    <div class="mx-auto max-w-6xl px-4 py-8">
        <nav class="mb-5 text-sm text-slate-500">
            <a href="{{ route('home') }}" class="hover:text-primary-600">Beranda</a>
            <span class="mx-1">/</span>
            <a href="{{ route('katalog') }}" class="hover:text-primary-600">Katalog</a>
            <span class="mx-1">/</span>
            <span class="text-slate-700">{{ $buku->judul }}</span>
        </nav>

        <div class="card p-6 lg:p-8">
            <div class="grid gap-8 lg:grid-cols-[240px_1fr]">
                <div class="flex justify-center lg:justify-start">
                    <x-cover-buku :buku="$buku" />
                </div>

                <div>
                    <div class="flex flex-wrap items-center gap-2">
                        <a href="{{ route('katalog', ['kategori' => $buku->kategori->slug]) }}" class="badge bg-primary-50 text-primary-700">{{ $buku->kategori->nama }}</a>
                        <x-badge-status :status="$buku->tersedia > 0 ? 'tersedia' : 'habis'" />
                    </div>

                    <h1 class="mt-3 text-2xl font-bold text-slate-900 lg:text-3xl">{{ $buku->judul }}</h1>
                    <p class="mt-1 text-slate-600">oleh <span class="font-medium text-slate-800">{{ $buku->penulis }}</span></p>

                    <dl class="mt-6 grid grid-cols-2 gap-x-6 gap-y-4 sm:grid-cols-3">
                        @foreach ([
                            ['Kode Buku', $buku->kode_buku],
                            ['Penerbit', $buku->penerbit ?: '-'],
                            ['Tahun Terbit', $buku->tahun_terbit ?: '-'],
                            ['ISBN', $buku->isbn ?: '-'],
                            ['Lokasi Rak', $buku->rak ?: '-'],
                            ['Eksemplar', $buku->tersedia.' tersedia / '.$buku->jumlah_eksemplar],
                        ] as [$label, $value])
                            <div>
                                <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">{{ $label }}</dt>
                                <dd class="mt-0.5 text-sm font-medium text-slate-800">{{ $value }}</dd>
                            </div>
                        @endforeach
                    </dl>

                    <div class="mt-6 rounded-xl bg-slate-50 p-4">
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Status Peminjaman</p>
                        @if ($buku->tersedia > 0)
                            <p class="mt-1 text-sm text-emerald-700">Tersedia untuk dipinjam. Saat ini {{ $sedangDipinjam }} eksemplar sedang dipinjam.</p>
                        @else
                            <p class="mt-1 text-sm text-rose-700">Semua eksemplar sedang dipinjam. Silakan cek kembali nanti.</p>
                        @endif
                    </div>

                    @auth
                        @if (auth()->user()->isAdmin())
                            <div class="mt-6 flex flex-wrap gap-2">
                                <a href="{{ route('admin.buku.edit', $buku) }}" class="btn-secondary btn-sm">Edit Buku</a>
                                <a href="{{ route('admin.peminjaman.create') }}" class="btn-primary btn-sm">Catat Peminjaman</a>
                            </div>
                        @else
                            @php
                                $sudahAktif = auth()->user()->peminjaman()
                                    ->where('buku_id', $buku->id)
                                    ->aktif()
                                    ->exists();
                            @endphp
                            <div class="mt-6">
                                @if ($sudahAktif)
                                    <button class="btn-secondary" disabled>Sudah Diajukan / Dipinjam</button>
                                    <p class="mt-2 text-xs text-slate-500">Lihat status di <a href="{{ route('siswa.dashboard') }}" class="font-semibold text-primary-600 hover:underline">dashboard Anda</a>.</p>
                                @elseif ($buku->isTersedia())
                                    <form method="POST" action="{{ route('siswa.peminjaman.ajukan', $buku) }}">
                                        @csrf
                                        <button class="btn-primary">
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                                            Ajukan Peminjaman
                                        </button>
                                    </form>
                                    <p class="mt-2 text-xs text-slate-500">Pengajuan akan diproses oleh pustakawan.</p>
                                @else
                                    <button class="btn-secondary" disabled>Stok Habis</button>
                                @endif
                            </div>
                        @endif
                    @else
                        <div class="mt-6">
                            <a href="{{ route('login') }}" class="btn-primary">Masuk untuk Meminjam</a>
                            <p class="mt-2 text-xs text-slate-500">Login sebagai siswa untuk mengajukan peminjaman.</p>
                        </div>
                    @endauth
                </div>
            </div>

            @if ($buku->sinopsis)
                <div class="mt-8 border-t border-slate-100 pt-6">
                    <h2 class="text-sm font-bold uppercase tracking-wide text-slate-500">Sinopsis</h2>
                    <p class="mt-2 leading-relaxed text-slate-700">{{ $buku->sinopsis }}</p>
                </div>
            @endif
        </div>

        @if ($terkait->isNotEmpty())
            <div class="mt-10">
                <h2 class="mb-4 text-lg font-bold text-slate-900">Buku Terkait</h2>
                <div class="grid grid-cols-2 gap-5 sm:grid-cols-3 lg:grid-cols-4">
                    @foreach ($terkait as $item)
                        <a href="{{ route('katalog.show', $item) }}" class="card group p-4 transition hover:-translate-y-1 hover:shadow-md">
                            <div class="flex justify-center">
                                <x-cover-buku :buku="$item" />
                            </div>
                            <h3 class="mt-3 line-clamp-2 text-sm font-semibold text-slate-800 group-hover:text-primary-700">{{ $item->judul }}</h3>
                            <p class="mt-1 line-clamp-1 text-xs text-slate-500">{{ $item->penulis }}</p>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
@endsection
