@extends('layouts.admin')

@section('title', 'Laporan')
@section('heading', 'Laporan Peminjaman')
@section('subheading', 'Rekap transaksi per periode')

@section('content')
    <x-alert />

    <div class="card mb-5 p-4">
        <form method="GET" class="flex flex-wrap items-end gap-3">
            <div>
                <label class="label" for="mulai">Dari Tanggal</label>
                <input type="date" id="mulai" name="mulai" value="{{ $mulai->toDateString() }}" class="input">
            </div>
            <div>
                <label class="label" for="sampai">Sampai Tanggal</label>
                <input type="date" id="sampai" name="sampai" value="{{ $sampai->toDateString() }}" class="input">
            </div>
            <button class="btn-primary btn-sm">Tampilkan</button>
            <div class="ml-auto flex gap-2">
                <a href="{{ route('admin.laporan.cetak', request()->only(['mulai', 'sampai'])) }}" target="_blank" class="btn-secondary btn-sm">Cetak</a>
                <a href="{{ route('admin.laporan.export', request()->only(['mulai', 'sampai'])) }}" class="btn-secondary btn-sm">Export CSV</a>
            </div>
        </form>
    </div>

    <div class="mb-6 grid gap-4 sm:grid-cols-3 lg:grid-cols-6">
        @foreach ([
            ['Total', $ringkasan['total'], 'text-slate-900'],
            ['Dipinjam', $ringkasan['dipinjam'], 'text-blue-600'],
            ['Dikembalikan', $ringkasan['dikembalikan'], 'text-emerald-600'],
            ['Terlambat', $ringkasan['terlambat'], 'text-rose-600'],
        ] as [$label, $value, $color])
            <div class="card p-4">
                <p class="text-xs text-slate-500">{{ $label }}</p>
                <p class="mt-1 text-xl font-extrabold {{ $color }}">{{ number_format($value) }}</p>
            </div>
        @endforeach
        <div class="card p-4">
            <p class="text-xs text-slate-500">Total Denda</p>
            <p class="mt-1 text-xl font-extrabold text-amber-600"><x-rupiah :value="$ringkasan['total_denda']" /></p>
        </div>
        <div class="card p-4">
            <p class="text-xs text-slate-500">Belum Dibayar</p>
            <p class="mt-1 text-xl font-extrabold {{ $ringkasan['denda_belum'] > 0 ? 'text-rose-600' : 'text-emerald-600' }}"><x-rupiah :value="$ringkasan['denda_belum']" /></p>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="card overflow-hidden lg:col-span-2">
            <div class="border-b border-slate-100 px-5 py-4">
                <h2 class="font-bold text-slate-900">Daftar Transaksi</h2>
                <p class="text-xs text-slate-500">{{ $mulai->translatedFormat('d M Y') }} &ndash; {{ $sampai->translatedFormat('d M Y') }}</p>
            </div>
            @if ($peminjaman->isEmpty())
                <x-empty-state class="p-6" title="Tidak ada data" description="Tidak ada transaksi pada periode ini." />
            @else
                <div class="max-h-[32rem] overflow-auto">
                    <table class="w-full">
                        <thead class="sticky top-0 bg-slate-50">
                            <tr>
                                <th class="table-th">Tanggal</th>
                                <th class="table-th">Siswa</th>
                                <th class="table-th">Buku</th>
                                <th class="table-th">Status</th>
                                <th class="table-th text-right">Denda</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach ($peminjaman as $item)
                                <tr class="hover:bg-slate-50">
                                    <td class="table-td text-xs text-slate-500">{{ $item->tanggal_pinjam->format('d/m/Y') }}</td>
                                    <td class="table-td">{{ $item->siswa->name }}</td>
                                    <td class="table-td"><span class="line-clamp-1">{{ $item->buku->judul }}</span></td>
                                    <td class="table-td">
                                        @if ($item->isDipinjam() && $item->tanggal_kembali->isPast())
                                            <x-badge-status status="terlambat" />
                                        @else
                                            <x-badge-status :status="$item->status" />
                                        @endif
                                    </td>
                                    <td class="table-td text-right">
                                        {{ $item->denda > 0 ? 'Rp '.number_format((float) $item->denda, 0, ',', '.') : '-' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        <div class="card p-5">
            <h2 class="mb-4 font-bold text-slate-900">Buku Terpopuler</h2>
            <ol class="space-y-3">
                @forelse ($populer as $i => $buku)
                    <li class="flex items-center gap-3">
                        <span class="grid h-7 w-7 shrink-0 place-items-center rounded-lg bg-slate-100 text-xs font-bold text-slate-600">{{ $i + 1 }}</span>
                        <div class="min-w-0 flex-1">
                            <p class="line-clamp-1 text-sm font-medium text-slate-800">{{ $buku->judul }}</p>
                            <p class="text-xs text-slate-500">{{ $buku->peminjaman_count }}x dipinjam</p>
                        </div>
                    </li>
                @empty
                    <li class="text-sm text-slate-500">Belum ada data.</li>
                @endforelse
            </ol>
        </div>
    </div>
@endsection
