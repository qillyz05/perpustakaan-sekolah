@extends('layouts.siswa')

@section('title', 'Riwayat Peminjaman')

@section('content')
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-slate-900">Riwayat Peminjaman</h1>
        <p class="mt-1 text-sm text-slate-500">Seluruh riwayat peminjaman buku Anda</p>
    </div>

    <x-alert />

    @if ($riwayat->isEmpty())
        <x-empty-state title="Belum ada riwayat" description="Anda belum pernah meminjam buku dari perpustakaan.">
            <x-slot:action><a href="{{ route('katalog') }}" class="btn-primary btn-sm">Jelajahi Katalog</a></x-slot:action>
        </x-empty-state>
    @else
        <div class="card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="table-th">Buku</th>
                            <th class="table-th">Kode</th>
                            <th class="table-th">Pinjam</th>
                            <th class="table-th">Tenggat</th>
                            <th class="table-th">Dikembalikan</th>
                            <th class="table-th">Status</th>
                            <th class="table-th text-right">Denda</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($riwayat as $item)
                            <tr class="hover:bg-slate-50">
                                <td class="table-td">
                                    <p class="line-clamp-1 font-medium text-slate-800">{{ $item->buku->judul }}</p>
                                    <p class="text-xs text-slate-500">{{ $item->buku->penulis }}</p>
                                </td>
                                <td class="table-td font-mono text-xs text-slate-500">{{ $item->kode_pinjam }}</td>
                                <td class="table-td whitespace-nowrap text-xs text-slate-500">{{ $item->tanggal_pinjam->format('d/m/Y') }}</td>
                                <td class="table-td whitespace-nowrap text-xs text-slate-500">{{ $item->tanggal_kembali->format('d/m/Y') }}</td>
                                <td class="table-td whitespace-nowrap text-xs text-slate-500">{{ $item->tanggal_dikembalikan?->format('d/m/Y') ?? '-' }}</td>
                                <td class="table-td">
                                    @if ($item->isDipinjam() && $item->tanggal_kembali->isPast())
                                        <x-badge-status status="terlambat" />
                                    @else
                                        <x-badge-status :status="$item->status" />
                                    @endif
                                </td>
                                <td class="table-td text-right">
                                    @if ($item->denda > 0)
                                        <span class="font-medium text-slate-800"><x-rupiah :value="$item->denda" /></span>
                                        <div class="mt-0.5"><x-badge-status :status="$item->status_bayar" /></div>
                                    @else
                                        <span class="text-slate-400">-</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <x-pagination :items="$riwayat" />
    @endif
@endsection
