@extends('layouts.admin')

@section('title', 'Kategori')
@section('heading', 'Kategori Buku')
@section('subheading', 'Kelompokkan koleksi perpustakaan')

@section('content')
    <x-alert />

    <div class="mb-5 flex justify-end">
        <a href="{{ route('admin.kategori.create') }}" class="btn-primary btn-sm">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" d="M12 5v14M5 12h14"/></svg>
            Tambah Kategori
        </a>
    </div>

    <div class="card overflow-hidden">
        @if ($kategori->isEmpty())
            <x-empty-state class="p-6" title="Belum ada kategori" description="Buat kategori untuk mengelompokkan buku.">
                <x-slot:action><a href="{{ route('admin.kategori.create') }}" class="btn-primary btn-sm">Tambah Kategori</a></x-slot:action>
            </x-empty-state>
        @else
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="table-th">Nama</th>
                            <th class="table-th">Deskripsi</th>
                            <th class="table-th">Jumlah Buku</th>
                            <th class="table-th text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($kategori as $item)
                            <tr class="hover:bg-slate-50">
                                <td class="table-td font-medium text-slate-800">{{ $item->nama }}</td>
                                <td class="table-td text-slate-500">{{ Str::limit($item->deskripsi, 60) ?: '-' }}</td>
                                <td class="table-td"><span class="badge bg-slate-100 text-slate-700">{{ $item->buku_count }} buku</span></td>
                                <td class="table-td">
                                    <div class="flex justify-end gap-2">
                                        <a href="{{ route('admin.kategori.edit', $item) }}" class="btn-secondary btn-sm">Edit</a>
                                        <x-confirm-modal
                                            judul="Hapus Kategori"
                                            :pesan="'Yakin ingin menghapus kategori \''.$item->nama.'\'?'"
                                            :aksi="route('admin.kategori.destroy', $item)"
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

    <x-pagination :items="$kategori" />
@endsection
