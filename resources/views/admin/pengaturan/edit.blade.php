@extends('layouts.admin')

@section('title', 'Pengaturan')
@section('heading', 'Pengaturan Sistem')
@section('subheading', 'Konfigurasi aturan peminjaman')

@section('content')
    <x-alert />

    <form method="POST" action="{{ route('admin.pengaturan.update') }}" class="card max-w-xl space-y-5 p-5">
        @csrf
        @method('PUT')

        <div>
            <label for="nama_perpustakaan" class="label">Nama Perpustakaan <span class="text-rose-500">*</span></label>
            <input type="text" id="nama_perpustakaan" name="nama_perpustakaan" value="{{ old('nama_perpustakaan', $pengaturan['nama_perpustakaan']) }}" required class="input">
            @error('nama_perpustakaan')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
        </div>

        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <label for="lama_pinjam_hari" class="label">Lama Pinjam (hari) <span class="text-rose-500">*</span></label>
                <input type="number" id="lama_pinjam_hari" name="lama_pinjam_hari" value="{{ old('lama_pinjam_hari', $pengaturan['lama_pinjam_hari']) }}" min="1" max="90" required class="input">
                @error('lama_pinjam_hari')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="batas_pinjam_maks" class="label">Batas Pinjam per Siswa <span class="text-rose-500">*</span></label>
                <input type="number" id="batas_pinjam_maks" name="batas_pinjam_maks" value="{{ old('batas_pinjam_maks', $pengaturan['batas_pinjam_maks']) }}" min="1" max="20" required class="input">
                @error('batas_pinjam_maks')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
            </div>
        </div>

        <div>
            <label for="denda_per_hari" class="label">Denda Keterlambatan (Rp/hari) <span class="text-rose-500">*</span></label>
            <input type="number" id="denda_per_hari" name="denda_per_hari" value="{{ old('denda_per_hari', $pengaturan['denda_per_hari']) }}" min="0" step="100" required class="input">
            @error('denda_per_hari')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
            <p class="mt-1 text-xs text-slate-400">Denda dihitung otomatis saat proses pengembalian.</p>
        </div>

        <button class="btn-primary">Simpan Pengaturan</button>
    </form>
@endsection
