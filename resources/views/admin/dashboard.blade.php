@extends('layouts.admin')

@section('title', 'Dashboard')
@section('heading', 'Dashboard')
@section('subheading', 'Ringkasan aktivitas perpustakaan')

@section('content')
    @php
        $cards = [
            ['label' => 'Judul Buku', 'value' => number_format($statistik['total_buku']), 'sub' => $statistik['total_eksemplar'].' eksemplar', 'color' => 'bg-primary-50 text-primary-600', 'icon' => 'M4 5h16v14H4zM8 5v14'],
            ['label' => 'Anggota Siswa', 'value' => number_format($statistik['total_anggota']), 'sub' => 'siswa terdaftar', 'color' => 'bg-indigo-50 text-indigo-600', 'icon' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z'],
            ['label' => 'Sedang Dipinjam', 'value' => number_format($statistik['sedang_dipinjam']), 'sub' => 'peminjaman aktif', 'color' => 'bg-blue-50 text-blue-600', 'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5l2 4h5a2 2 0 012 2v12a2 2 0 01-2 2z'],
            ['label' => 'Terlambat', 'value' => number_format($statistik['terlambat']), 'sub' => 'perlu ditagih', 'color' => 'bg-rose-50 text-rose-600', 'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'],
        ];
        $maxChart = max(1, $chart->max('value'));
    @endphp

    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        @foreach ($cards as $card)
            <div class="card p-5">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-sm text-slate-500">{{ $card['label'] }}</p>
                        <p class="mt-1 text-2xl font-extrabold text-slate-900">{{ $card['value'] }}</p>
                        <p class="mt-0.5 text-xs text-slate-400">{{ $card['sub'] }}</p>
                    </div>
                    <span class="grid h-10 w-10 place-items-center rounded-xl {{ $card['color'] }}">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $card['icon'] }}"/></svg>
                    </span>
                </div>
            </div>
        @endforeach
    </div>

    @if ($statistik['denda_belum_bayar'] > 0)
        <div class="mt-4 flex items-center gap-3 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">
            <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01M12 3a9 9 0 100 18 9 9 0 000-18z"/></svg>
            Terdapat denda belum dibayar sebesar <strong class="mx-1"><x-rupiah :value="$statistik['denda_belum_bayar']" /></strong>.
            <a href="{{ route('admin.peminjaman.index', ['status' => 'denda']) }}" class="ml-auto font-semibold underline">Lihat</a>
        </div>
    @endif

    <div class="mt-6 grid gap-6 lg:grid-cols-3">
        <div class="card p-5 lg:col-span-2">
            <div class="mb-4 flex items-center justify-between">
                <div>
                    <h2 class="font-bold text-slate-900">Peminjaman 14 Hari Terakhir</h2>
                    <p class="text-xs text-slate-500">Jumlah transaksi peminjaman per hari</p>
                </div>
            </div>
            <div class="flex h-48 items-end gap-1.5">
                @foreach ($chart as $point)
                    <div class="group flex flex-1 flex-col items-center justify-end gap-1">
                        <span class="text-[10px] font-semibold text-slate-400 opacity-0 transition group-hover:opacity-100">{{ $point['value'] }}</span>
                        <div class="w-full rounded-t bg-primary-500 transition hover:bg-primary-600" style="height: {{ max(4, ($point['value'] / $maxChart) * 100) }}%"></div>
                        <span class="text-[10px] text-slate-400">{{ $point['label'] }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="card p-5">
            <h2 class="mb-4 font-bold text-slate-900">Buku Terpopuler</h2>
            <ol class="space-y-3">
                @forelse ($bukuPopuler as $i => $buku)
                    <li class="flex items-center gap-3">
                        <span class="grid h-7 w-7 shrink-0 place-items-center rounded-lg bg-slate-100 text-xs font-bold text-slate-600">{{ $i + 1 }}</span>
                        <div class="min-w-0 flex-1">
                            <a href="{{ route('katalog.show', $buku) }}" class="line-clamp-1 text-sm font-medium text-slate-800 hover:text-primary-600">{{ $buku->judul }}</a>
                            <p class="text-xs text-slate-500">{{ $buku->peminjaman_count }}x dipinjam</p>
                        </div>
                    </li>
                @empty
                    <li class="text-sm text-slate-500">Belum ada data.</li>
                @endforelse
            </ol>
        </div>
    </div>

    <div class="card mt-6 overflow-hidden">
        <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4">
            <h2 class="font-bold text-slate-900">Peminjaman Terbaru</h2>
            <a href="{{ route('admin.peminjaman.index') }}" class="text-sm font-semibold text-primary-600 hover:text-primary-700">Lihat semua</a>
        </div>
        @if ($terbaru->isEmpty())
            <x-empty-state class="p-6" title="Belum ada peminjaman" description="Catat peminjaman pertama dari menu Peminjaman." />
        @else
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="table-th">Kode</th>
                            <th class="table-th">Siswa</th>
                            <th class="table-th">Buku</th>
                            <th class="table-th">Tenggat</th>
                            <th class="table-th">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($terbaru as $item)
                            <tr class="hover:bg-slate-50">
                                <td class="table-td font-mono text-xs">{{ $item->kode_pinjam }}</td>
                                <td class="table-td">{{ $item->siswa->name }}</td>
                                <td class="table-td">{{ $item->buku->judul }}</td>
                                <td class="table-td">{{ $item->tanggal_kembali->translatedFormat('d M Y') }}</td>
                                <td class="table-td">
                                    @if ($item->isDipinjam() && $item->tanggal_kembali->isPast())
                                        <x-badge-status status="terlambat" />
                                    @else
                                        <x-badge-status :status="$item->status" />
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
@endsection
