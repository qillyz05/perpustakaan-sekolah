@extends('layouts.admin')

@section('title', 'Data Buku')
@section('heading', 'Data Buku')
@section('subheading', 'Kelola koleksi perpustakaan')

@section('content')
    <x-alert />

    <div class="card mb-5 grid gap-3 p-4 sm:grid-cols-2 lg:grid-cols-4">
        <form method="GET" class="contents">
            <div class="lg:col-span-2">
                <label class="label" for="q">Pencarian</label>
                <input type="search" id="q" name="q" value="{{ request('q') }}" placeholder="Judul, penulis, kode buku, ISBN..." class="input">
            </div>
            <div>
                <label class="label" for="kategori">Kategori</label>
                <select id="kategori" name="kategori" class="input">
                    <option value="">Semua kategori</option>
                    @foreach ($kategori as $kat)
                        <option value="{{ $kat->slug }}" @selected(request('kategori') === $kat->slug)>{{ $kat->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="label" for="stok">Stok</label>
                <select id="stok" name="stok" class="input">
                    <option value="">Semua stok</option>
                    <option value="tersedia" @selected(request('stok') === 'tersedia')>Tersedia</option>
                    <option value="habis" @selected(request('stok') === 'habis')>Habis</option>
                </select>
            </div>
            <div class="flex gap-2 sm:col-span-2 lg:col-span-4">
                <button class="btn-primary btn-sm">Filter</button>
                @if (request()->hasAny(['q', 'kategori', 'stok']))
                    <a href="{{ route('admin.buku.index') }}" class="btn-secondary btn-sm">Reset</a>
                @endif
                <a href="{{ route('admin.buku.create') }}" class="btn-primary btn-sm ml-auto">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" d="M12 5v14M5 12h14"/></svg>
                    Tambah Buku
                </a>
            </div>
        </form>
    </div>

    <div class="card overflow-hidden">
        @if ($buku->isEmpty())
            <x-empty-state class="p-6" title="Belum ada buku" description="Tambahkan koleksi buku pertama Anda.">
                <x-slot:action><a href="{{ route('admin.buku.create') }}" class="btn-primary btn-sm">Tambah Buku</a></x-slot:action>
            </x-empty-state>
        @else
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="table-th">Buku</th>
                            <th class="table-th">Kategori</th>
                            <th class="table-th">Kode</th>
                            <th class="table-th">Stok</th>
                            <th class="table-th text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($buku as $item)
                            <tr class="hover:bg-slate-50">
                                <td class="table-td">
                                    <div class="flex items-center gap-3">
                                        <x-cover-buku :buku="$item" size="sm" />
                                        <div class="min-w-0">
                                            <p class="line-clamp-1 font-medium text-slate-800">{{ $item->judul }}</p>
                                            <p class="text-xs text-slate-500">{{ $item->penulis }}{{ $item->tahun_terbit ? ' · '.$item->tahun_terbit : '' }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="table-td">{{ $item->kategori->nama }}</td>
                                <td class="table-td font-mono text-xs">{{ $item->kode_buku }}</td>
                                <td class="table-td">
                                    <x-badge-status :status="$item->tersedia > 0 ? 'tersedia' : 'habis'" />
                                    <span class="ml-1 text-xs text-slate-500">{{ $item->tersedia }}/{{ $item->jumlah_eksemplar }}</span>
                                </td>
                                <td class="table-td">
                                    <div class="flex justify-end gap-2">
                                        <a href="{{ route('katalog.show', $item) }}" target="_blank" class="btn-secondary btn-sm">Lihat</a>
                                        <a href="{{ route('admin.buku.edit', $item) }}" class="btn-secondary btn-sm">Edit</a>
                                        <x-confirm-modal
                                            judul="Hapus Buku"
                                            :pesan="'Yakin ingin menghapus buku \''.$item->judul.'\'? Tindakan ini tidak bisa dibatalkan.'"
                                            :aksi="route('admin.buku.destroy', $item)"
                                            metode="DELETE"
                                            tombol="Hapus">
                                            Hapus
                                        </x-confirm-modal>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <x-pagination :items="$buku" />
@endsection
