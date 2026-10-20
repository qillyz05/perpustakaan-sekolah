@extends('layouts.admin')

@section('title', 'Anggota Siswa')
@section('heading', 'Anggota Siswa')
@section('subheading', 'Kelola data anggota perpustakaan')

@section('content')
    <x-alert />

    <div class="mb-5 flex flex-wrap items-end gap-3">
        <form method="GET" class="flex flex-1 items-end gap-2">
            <div class="flex-1">
                <label class="label" for="q">Pencarian</label>
                <input type="search" id="q" name="q" value="{{ request('q') }}" placeholder="Nama, NIS, kelas, email..." class="input">
            </div>
            <button class="btn-primary btn-sm">Cari</button>
            @if (request('q'))
                <a href="{{ route('admin.siswa.index') }}" class="btn-secondary btn-sm">Reset</a>
            @endif
        </form>
        <a href="{{ route('admin.siswa.create') }}" class="btn-primary btn-sm">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" d="M12 5v14M5 12h14"/></svg>
            Tambah Siswa
        </a>
    </div>

    <div class="card overflow-hidden">
        @if ($siswa->isEmpty())
            <x-empty-state class="p-6" title="Belum ada anggota" description="Tambahkan data siswa sebagai anggota perpustakaan.">
                <x-slot:action><a href="{{ route('admin.siswa.create') }}" class="btn-primary btn-sm">Tambah Siswa</a></x-slot:action>
            </x-empty-state>
        @else
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="table-th">Nama</th>
                            <th class="table-th">NIS</th>
                            <th class="table-th">Kelas</th>
                            <th class="table-th">Kontak</th>
                            <th class="table-th">Pinjaman</th>
                            <th class="table-th text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($siswa as $item)
                            <tr class="hover:bg-slate-50">
                                <td class="table-td">
                                    <div class="flex items-center gap-3">
                                        <span class="grid h-9 w-9 shrink-0 place-items-center rounded-full bg-primary-100 text-sm font-semibold text-primary-700">{{ strtoupper(substr($item->name, 0, 1)) }}</span>
                                        <div class="min-w-0">
                                            <p class="font-medium text-slate-800">{{ $item->name }}</p>
                                            <p class="text-xs text-slate-500">{{ $item->email }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="table-td font-mono text-xs">{{ $item->nis ?: '-' }}</td>
                                <td class="table-td">{{ $item->kelas ?: '-' }}</td>
                                <td class="table-td text-slate-500">{{ $item->telepon ?: '-' }}</td>
                                <td class="table-td">
                                    <span class="badge bg-blue-100 text-blue-700">{{ $item->sedang_dipinjam_count }} aktif</span>
                                    <span class="ml-1 text-xs text-slate-500">/ {{ $item->total_pinjam_count }} total</span>
                                </td>
                                <td class="table-td">
                                    <div class="flex justify-end gap-2">
                                        <a href="{{ route('admin.siswa.edit', $item) }}" class="btn-secondary btn-sm">Edit</a>
                                        <x-confirm-modal
                                            judul="Hapus Anggota"
                                            :pesan="'Yakin ingin menghapus siswa \''.$item->name.'\'?'"
                                            :aksi="route('admin.siswa.destroy', $item)"
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

    <x-pagination :items="$siswa" />
@endsection
