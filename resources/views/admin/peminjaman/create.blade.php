@extends('layouts.admin')

@section('title', 'Catat Peminjaman')
@section('heading', 'Catat Peminjaman')
@section('subheading', 'Formulir peminjaman buku baru')

@section('content')
    <x-alert />

    <form method="POST" action="{{ route('admin.peminjaman.store') }}" class="card max-w-3xl space-y-5 p-5"
          x-data="{ pinjam: '{{ now()->toDateString() }}', lama: {{ $lamaPinjam }} }">
        @csrf

        <div class="rounded-xl bg-primary-50 px-4 py-3 text-sm text-primary-800">
            Lama pinjam standar <strong>{{ $lamaPinjam }} hari</strong> &middot; maksimal <strong>{{ $batasPinjam }} buku</strong> per siswa.
        </div>

        <div>
            <label for="user_id" class="label">Siswa <span class="text-rose-500">*</span></label>
            <select id="user_id" name="user_id" required class="input">
                <option value="">Pilih siswa</option>
                @foreach ($siswa as $item)
                    <option value="{{ $item->id }}" @selected(old('user_id') == $item->id)>
                        {{ $item->name }}{{ $item->nis ? ' · '.$item->nis : '' }}{{ $item->kelas ? ' · '.$item->kelas : '' }}
                    </option>
                @endforeach
            </select>
            @error('user_id')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="buku_id" class="label">Buku <span class="text-rose-500">*</span></label>
            <select id="buku_id" name="buku_id" required class="input">
                <option value="">Pilih buku</option>
                @foreach ($buku as $item)
                    <option value="{{ $item->id }}" @selected(old('buku_id', $pilihBuku) == $item->id)>
                        {{ $item->judul }} — {{ $item->penulis }} (stok: {{ $item->tersedia }})
                    </option>
                @endforeach
            </select>
            @error('buku_id')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
        </div>

        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <label for="tanggal_pinjam" class="label">Tanggal Pinjam <span class="text-rose-500">*</span></label>
                <input type="date" id="tanggal_pinjam" name="tanggal_pinjam" required
                       value="{{ old('tanggal_pinjam', now()->toDateString()) }}" class="input" x-model="pinjam">
                @error('tanggal_pinjam')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="tanggal_kembali" class="label">Tenggat Pengembalian <span class="text-rose-500">*</span></label>
                <input type="date" id="tanggal_kembali" name="tanggal_kembali" required class="input"
                       x-init="$el.value = '{{ old('tanggal_kembali', now()->addDays($lamaPinjam)->toDateString()) }}'"
                       @change="'{{ '' }}'">
                @error('tanggal_kembali')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                <p class="mt-1 text-xs text-slate-400">Default: {{ $lamaPinjam }} hari dari tanggal pinjam.</p>
            </div>
        </div>

        <div>
            <label for="catatan" class="label">Catatan</label>
            <textarea id="catatan" name="catatan" rows="2" class="input" placeholder="Opsional">{{ old('catatan') }}</textarea>
        </div>

        <div class="flex gap-2">
            <button class="btn-primary">Simpan Peminjaman</button>
            <a href="{{ route('admin.peminjaman.index') }}" class="btn-secondary">Batal</a>
        </div>
    </form>
@endsection
