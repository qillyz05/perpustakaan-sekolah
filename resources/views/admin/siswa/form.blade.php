@extends('layouts.admin')

@section('title', $siswa->exists ? 'Edit Siswa' : 'Tambah Siswa')
@section('heading', $siswa->exists ? 'Edit Siswa' : 'Tambah Siswa')
@section('subheading', 'Data anggota perpustakaan')

@section('content')
    <x-alert />

    <form method="POST" action="{{ $siswa->exists ? route('admin.siswa.update', $siswa) : route('admin.siswa.store') }}" class="card max-w-2xl space-y-4 p-5">
        @csrf
        @if ($siswa->exists)
            @method('PUT')
        @endif

        <div class="grid gap-4 sm:grid-cols-2">
            <div class="sm:col-span-2">
                <label for="name" class="label">Nama Lengkap <span class="text-rose-500">*</span></label>
                <input type="text" id="name" name="name" value="{{ old('name', $siswa->name) }}" required class="input">
                @error('name')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="nis" class="label">NIS</label>
                <input type="text" id="nis" name="nis" value="{{ old('nis', $siswa->nis) }}" class="input">
                @error('nis')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="kelas" class="label">Kelas</label>
                <input type="text" id="kelas" name="kelas" value="{{ old('kelas', $siswa->kelas) }}" placeholder="mis. XI IPA 1" class="input">
            </div>
            <div>
                <label for="email" class="label">Email <span class="text-rose-500">*</span></label>
                <input type="email" id="email" name="email" value="{{ old('email', $siswa->email) }}" required class="input">
                @error('email')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="telepon" class="label">Telepon</label>
                <input type="text" id="telepon" name="telepon" value="{{ old('telepon', $siswa->telepon) }}" class="input">
            </div>
            <div class="sm:col-span-2">
                <label for="alamat" class="label">Alamat</label>
                <textarea id="alamat" name="alamat" rows="2" class="input">{{ old('alamat', $siswa->alamat) }}</textarea>
            </div>
            <div class="sm:col-span-2">
                <label for="password" class="label">
                    Password
                    @if (! $siswa->exists)
                        <span class="text-rose-500">*</span>
                    @else
                        <span class="font-normal text-slate-400">(kosongkan bila tidak diubah)</span>
                    @endif
                </label>
                <input type="password" id="password" name="password" class="input" placeholder="Minimal 6 karakter">
                @error('password')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
            </div>
        </div>

        <div class="flex gap-2">
            <button class="btn-primary">{{ $siswa->exists ? 'Simpan Perubahan' : 'Simpan' }}</button>
            <a href="{{ route('admin.siswa.index') }}" class="btn-secondary">Batal</a>
        </div>
    </form>
@endsection
