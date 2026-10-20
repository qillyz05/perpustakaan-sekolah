@extends('layouts.admin')

@section('title', 'Detail Peminjaman')
@section('heading', 'Detail Peminjaman')
@section('subheading', $peminjaman->kode_pinjam)

@section('content')
    <x-alert />

    <div class="mb-5">
        <a href="{{ route('admin.peminjaman.index') }}" class="text-sm font-medium text-slate-500 hover:text-primary-600">&larr; Kembali ke daftar peminjaman</a>
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="card space-y-5 p-5 lg:col-span-2">
            <div class="flex items-start justify-between">
                <div>
                    <p class="font-mono text-xs text-slate-400">{{ $peminjaman->kode_pinjam }}</p>
                    <h2 class="mt-1 text-lg font-bold text-slate-900">{{ $peminjaman->buku->judul }}</h2>
                    <p class="text-sm text-slate-500">{{ $peminjaman->buku->penulis }} &middot; {{ $peminjaman->buku->kategori->nama }}</p>
                </div>
                @if ($peminjaman->isDipinjam() && $peminjaman->tanggal_kembali->isPast())
                    <x-badge-status status="terlambat" />
                @else
                    <x-badge-status :status="$peminjaman->status" />
                @endif
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div class="rounded-xl bg-slate-50 p-4">
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Peminjam</p>
                    <p class="mt-1 font-medium text-slate-800">{{ $peminjaman->siswa->name }}</p>
                    <p class="text-sm text-slate-500">{{ $peminjaman->siswa->nis ?: '-' }} &middot; {{ $peminjaman->siswa->kelas ?: '-' }}</p>
                </div>
                <div class="rounded-xl bg-slate-50 p-4">
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Petugas</p>
                    <p class="mt-1 font-medium text-slate-800">{{ $peminjaman->petugas->name ?? 'Sistem' }}</p>
                    <p class="text-sm text-slate-500">Dicatat {{ $peminjaman->created_at->format('d/m/Y H:i') }}</p>
                </div>
            </div>

            <div class="grid grid-cols-3 gap-4">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Tanggal Pinjam</p>
                    <p class="mt-1 text-sm font-medium text-slate-800">{{ $peminjaman->tanggal_pinjam->translatedFormat('d M Y') }}</p>
                </div>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Tenggat</p>
                    <p class="mt-1 text-sm font-medium text-slate-800">{{ $peminjaman->tanggal_kembali->translatedFormat('d M Y') }}</p>
                </div>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Dikembalikan</p>
                    <p class="mt-1 text-sm font-medium text-slate-800">{{ $peminjaman->tanggal_dikembalikan?->translatedFormat('d M Y') ?? '-' }}</p>
                </div>
            </div>

            @if ($peminjaman->catatan)
                <div class="rounded-xl bg-slate-50 p-4">
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Catatan</p>
                    <p class="mt-1 text-sm text-slate-700">{{ $peminjaman->catatan }}</p>
                </div>
            @endif
        </div>

        <div class="space-y-6">
            <div class="card p-5">
                <h2 class="font-bold text-slate-900">Denda</h2>
                @php $denda = $peminjaman->isDipinjam() ? $dendaSaatIni : (float) $peminjaman->denda; @endphp
                <p class="mt-2 text-2xl font-extrabold {{ $denda > 0 ? 'text-rose-600' : 'text-emerald-600' }}">
                    <x-rupiah :value="$denda" />
                </p>
                @if ($peminjaman->isDipinjam() && $denda > 0)
                    <p class="mt-1 text-xs text-slate-500">Estimasi berdasarkan {{ $peminjaman->hariTerlambat() }} hari keterlambatan.</p>
                @elseif ($peminjaman->status_bayar === 'belum_dibayar' && $denda > 0)
                    <p class="mt-1 text-xs text-amber-600">Belum dibayar</p>
                @else
                    <p class="mt-1 text-xs text-slate-500">{{ $denda > 0 ? 'Sudah dibayar' : 'Tidak ada denda' }}</p>
                @endif

                @if ($denda > 0 && $peminjaman->status_bayar === 'belum_dibayar')
                    <form method="POST" action="{{ route('admin.peminjaman.bayar', $peminjaman) }}" class="mt-3" onsubmit="return confirm('Tandai denda sebagai lunas?')">
                        @csrf @method('PATCH')
                        <button class="btn-primary w-full btn-sm">Tandai Lunas</button>
                    </form>
                @endif
            </div>

            <div class="card space-y-2 p-5">
                <h2 class="font-bold text-slate-900">Aksi</h2>
                @if ($peminjaman->isDiajukan())
                    <form method="POST" action="{{ route('admin.peminjaman.setujui', $peminjaman) }}" onsubmit="return confirm('Setujui pengajuan ini dan serahkan buku?')">
                        @csrf @method('PATCH')
                        <button class="btn-primary w-full btn-sm">Setujui Pengajuan</button>
                    </form>
                    <form method="POST" action="{{ route('admin.peminjaman.tolak', $peminjaman) }}" onsubmit="return confirm('Tolak pengajuan ini?')">
                        @csrf @method('PATCH')
                        <button class="btn-secondary w-full btn-sm text-rose-600">Tolak Pengajuan</button>
                    </form>
                @endif
                @if ($peminjaman->isDipinjam())
                    <form method="POST" action="{{ route('admin.peminjaman.kembalikan', $peminjaman) }}" onsubmit="return confirm('Proses pengembalian buku ini?')">
                        @csrf @method('PATCH')
                        <button class="btn-primary w-full btn-sm">Proses Pengembalian</button>
                    </form>
                    <form method="POST" action="{{ route('admin.peminjaman.perpanjang', $peminjaman) }}">
                        @csrf @method('PATCH')
                        <button class="btn-secondary w-full btn-sm">Perpanjang Tenggat</button>
                    </form>
                @endif
                <a href="{{ route('katalog.show', $peminjaman->buku) }}" target="_blank" class="btn-secondary w-full btn-sm">Lihat Buku</a>
                <x-confirm-modal
                    class="w-full"
                    judul="Hapus Peminjaman"
                    pesan="Data peminjaman ini akan dihapus permanen. Lanjutkan?"
                    :aksi="route('admin.peminjaman.destroy', $peminjaman)"
                    metode="DELETE"
                    tombol="Hapus">
                    Hapus Data
                </x-confirm-modal>
            </div>
        </div>
    </div>
@endsection
