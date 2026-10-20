@extends('layouts.siswa')

@section('title', 'Dashboard Siswa')

@section('content')
    <div class="mb-6 flex flex-wrap items-end justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Halo, {{ auth()->user()->name }}!</h1>
            <p class="mt-1 text-sm text-slate-500">
                {{ auth()->user()->nis ? 'NIS '.auth()->user()->nis : '' }}{{ auth()->user()->kelas ? ' · '.auth()->user()->kelas : '' }}
            </p>
        </div>
        <div class="flex items-center gap-3">
            <span class="badge bg-slate-100 text-slate-700">Kuota tersisa: {{ $sisaKuota }}/{{ $batas }}</span>
            <a href="{{ route('katalog') }}" class="btn-primary btn-sm">Pinjam Buku</a>
        </div>
    </div>

    <x-alert />

    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        @foreach ([
            ['label' => 'Sedang Dipinjam', 'value' => $statistik['aktif'], 'color' => 'text-blue-600', 'bg' => 'bg-blue-50'],
            ['label' => 'Menunggu Persetujuan', 'value' => $statistik['pengajuan'], 'color' => 'text-indigo-600', 'bg' => 'bg-indigo-50'],
            ['label' => 'Terlambat', 'value' => $statistik['terlambat'], 'color' => 'text-rose-600', 'bg' => 'bg-rose-50'],
            ['label' => 'Denda Belum Bayar', 'value' => null, 'denda' => $statistik['denda'], 'color' => 'text-amber-600', 'bg' => 'bg-amber-50'],
        ] as $card)
            <div class="card p-5">
                <span class="grid h-10 w-10 place-items-center rounded-xl {{ $card['bg'] }} {{ $card['color'] }}">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5l2 4h5a2 2 0 012 2v12a2 2 0 01-2 2z"/></svg>
                </span>
                <p class="mt-3 text-sm text-slate-500">{{ $card['label'] }}</p>
                <p class="mt-0.5 text-2xl font-extrabold {{ $card['color'] }}">
                    @if (array_key_exists('denda', $card))
                        <x-rupiah :value="$card['denda']" />
                    @else
                        {{ $card['value'] }}
                    @endif
                </p>
            </div>
        @endforeach
    </div>

    @if ($pengajuan->isNotEmpty())
        <div class="mt-8">
            <h2 class="mb-4 text-lg font-bold text-slate-900">Pengajuan Menunggu Persetujuan</h2>
            <div class="space-y-4">
                @foreach ($pengajuan as $item)
                    <div class="card flex flex-col gap-4 border-l-4 border-indigo-400 p-4 sm:flex-row sm:items-center">
                        <x-cover-buku :buku="$item->buku" size="sm" />
                        <div class="min-w-0 flex-1">
                            <span class="badge bg-indigo-50 text-indigo-700">Menunggu</span>
                            <h3 class="mt-1 line-clamp-1 font-semibold text-slate-800">{{ $item->buku->judul }}</h3>
                            <p class="text-sm text-slate-500">{{ $item->buku->penulis }}</p>
                            <p class="mt-1 font-mono text-xs text-slate-400">{{ $item->kode_pinjam }}</p>
                        </div>
                        <div class="flex items-center gap-3 sm:flex-col sm:items-end">
                            <p class="text-xs text-slate-400">Diajukan {{ $item->created_at->translatedFormat('d M Y') }}</p>
                            <form method="POST" action="{{ route('siswa.peminjaman.batalkan', $item) }}">
                                @csrf
                                @method('DELETE')
                                <button class="btn-secondary btn-sm" onclick="return confirm('Batalkan pengajuan ini?')">Batalkan</button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <div class="mt-8">
        <h2 class="mb-4 text-lg font-bold text-slate-900">Buku yang Sedang Dipinjam</h2>

        @if ($aktif->isEmpty())
            <x-empty-state title="Tidak ada pinjaman aktif" description="Anda tidak sedang meminjam buku. Kunjungi katalog untuk melihat koleksi.">
                <x-slot:action><a href="{{ route('katalog') }}" class="btn-primary btn-sm">Jelajahi Katalog</a></x-slot:action>
            </x-empty-state>
        @else
            <div class="space-y-4">
                @foreach ($aktif as $item)
                    @php $telat = $item->tanggal_kembali->isPast(); @endphp
                    <div class="card flex flex-col gap-4 p-4 sm:flex-row sm:items-center">
                        <x-cover-buku :buku="$item->buku" size="sm" />
                        <div class="min-w-0 flex-1">
                            <span class="badge bg-primary-50 text-primary-700">{{ $item->buku->kategori->nama }}</span>
                            <h3 class="mt-1 line-clamp-1 font-semibold text-slate-800">{{ $item->buku->judul }}</h3>
                            <p class="text-sm text-slate-500">{{ $item->buku->penulis }}</p>
                            <p class="mt-1 font-mono text-xs text-slate-400">{{ $item->kode_pinjam }}</p>
                        </div>
                        <div class="text-sm sm:text-right">
                            <p class="text-xs text-slate-400">Tenggat</p>
                            <p class="{{ $telat ? 'font-bold text-rose-600' : 'font-semibold text-slate-800' }}">{{ $item->tanggal_kembali->translatedFormat('d M Y') }}</p>
                            @if ($telat)
                                <x-badge-status status="terlambat" class="mt-1" />
                            @else
                                <p class="mt-1 text-xs text-emerald-600">{{ max(0, now()->startOfDay()->diffInDays($item->tanggal_kembali, false)) }} hari lagi</p>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <div class="mt-8 flex justify-center">
        <a href="{{ route('siswa.riwayat') }}" class="btn-secondary">Lihat Riwayat Lengkap</a>
    </div>
@endsection
