@extends('layouts.admin')

@section('title', 'Peminjaman')
@section('heading', 'Transaksi Peminjaman')
@section('subheading', 'Catat peminjaman dan pengembalian buku')

@section('content')
    <x-alert />

    <div class="mb-5 grid gap-4 sm:grid-cols-4">
        <div class="card p-4">
            <p class="text-sm text-slate-500">Menunggu Persetujuan</p>
            <p class="mt-1 text-2xl font-extrabold text-indigo-600">{{ $ringkasan['diajukan'] }}</p>
        </div>
        <div class="card p-4">
            <p class="text-sm text-slate-500">Sedang Dipinjam</p>
            <p class="mt-1 text-2xl font-extrabold text-blue-600">{{ $ringkasan['dipinjam'] }}</p>
        </div>
        <div class="card p-4">
            <p class="text-sm text-slate-500">Terlambat</p>
            <p class="mt-1 text-2xl font-extrabold text-rose-600">{{ $ringkasan['terlambat'] }}</p>
        </div>
        <div class="card p-4">
            <p class="text-sm text-slate-500">Denda Belum Dibayar</p>
            <p class="mt-1 text-2xl font-extrabold text-amber-600"><x-rupiah :value="$ringkasan['denda']" /></p>
        </div>
    </div>

    <div class="mb-5 flex flex-wrap items-end gap-3">
        <form method="GET" class="flex flex-1 flex-wrap items-end gap-2">
            <div class="min-w-[200px] flex-1">
                <label class="label" for="q">Pencarian</label>
                <input type="search" id="q" name="q" value="{{ request('q') }}" placeholder="Kode, nama siswa, atau judul buku..." class="input">
            </div>
            <div>
                <label class="label" for="status">Status</label>
                <select id="status" name="status" class="input">
                    <option value="">Semua</option>
                    <option value="diajukan" @selected(request('status') === 'diajukan')>Menunggu Persetujuan</option>
                    <option value="dipinjam" @selected(request('status') === 'dipinjam')>Dipinjam</option>
                    <option value="dikembalikan" @selected(request('status') === 'dikembalikan')>Dikembalikan</option>
                    <option value="terlambat" @selected(request('status') === 'terlambat')>Terlambat</option>
                    <option value="denda" @selected(request('status') === 'denda')>Ada Denda</option>
                </select>
            </div>
            <button class="btn-primary btn-sm">Filter</button>
            @if (request()->hasAny(['q', 'status']))
                <a href="{{ route('admin.peminjaman.index') }}" class="btn-secondary btn-sm">Reset</a>
            @endif
        </form>
        <a href="{{ route('admin.peminjaman.create') }}" class="btn-primary btn-sm">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" d="M12 5v14M5 12h14"/></svg>
            Catat Peminjaman
        </a>
    </div>

    <div class="card overflow-hidden">
        @if ($peminjaman->isEmpty())
            <x-empty-state class="p-6" title="Belum ada transaksi" description="Catat peminjaman buku pertama.">
                <x-slot:action><a href="{{ route('admin.peminjaman.create') }}" class="btn-primary btn-sm">Catat Peminjaman</a></x-slot:action>
            </x-empty-state>
        @else
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="table-th">Kode</th>
                            <th class="table-th">Siswa</th>
                            <th class="table-th">Buku</th>
                            <th class="table-th">Pinjam</th>
                            <th class="table-th">Tenggat</th>
                            <th class="table-th">Status</th>
                            <th class="table-th text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($peminjaman as $item)
                            <tr class="hover:bg-slate-50">
                                <td class="table-td">
                                    <span class="font-mono text-xs text-slate-600">{{ $item->kode_pinjam }}</span>
                                </td>
                                <td class="table-td">
                                    <p class="font-medium text-slate-800">{{ $item->siswa->name }}</p>
                                    <p class="text-xs text-slate-500">{{ $item->siswa->nis ?: $item->siswa->kelas }}</p>
                                </td>
                                <td class="table-td">
                                    <p class="line-clamp-1 text-slate-800">{{ $item->buku->judul }}</p>
                                </td>
                                <td class="table-td text-xs text-slate-500">{{ $item->tanggal_pinjam->format('d/m/Y') }}</td>
                                <td class="table-td text-xs text-slate-500">{{ $item->tanggal_kembali->format('d/m/Y') }}</td>
                                <td class="table-td">
                                    @if ($item->isDipinjam() && $item->tanggal_kembali->isPast())
                                        <x-badge-status status="terlambat" />
                                    @else
                                        <x-badge-status :status="$item->status" />
                                    @endif
                                    @if ($item->denda > 0)
                                        <div class="mt-1">
                                            <x-badge-status :status="$item->status_bayar" />
                                            <span class="ml-1 text-xs text-slate-500"><x-rupiah :value="$item->denda" /></span>
                                        </div>
                                    @endif
                                </td>
                                <td class="table-td">
                                    <div class="flex justify-end gap-2">
                                        <a href="{{ route('admin.peminjaman.show', $item) }}" class="btn-secondary btn-sm">Detail</a>
                                        @if ($item->isDiajukan())
                                            <form method="POST" action="{{ route('admin.peminjaman.setujui', $item) }}" onsubmit="return confirm('Setujui pengajuan ini dan serahkan buku?')">
                                                @csrf
                                                @method('PATCH')
                                                <button class="btn-primary btn-sm">Setujui</button>
                                            </form>
                                            <form method="POST" action="{{ route('admin.peminjaman.tolak', $item) }}" onsubmit="return confirm('Tolak pengajuan ini?')">
                                                @csrf
                                                @method('PATCH')
                                                <button class="btn-secondary btn-sm text-rose-600">Tolak</button>
                                            </form>
                                        @elseif ($item->isDipinjam())
                                            <form method="POST" action="{{ route('admin.peminjaman.kembalikan', $item) }}" onsubmit="return confirm('Proses pengembalian buku ini?')">
                                                @csrf
                                                @method('PATCH')
                                                <button class="btn-primary btn-sm">Kembalikan</button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <x-pagination :items="$peminjaman" />
@endsection
