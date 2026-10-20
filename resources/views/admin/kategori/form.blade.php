@extends('layouts.admin')

@section('title', $kategori->exists ? 'Edit Kategori' : 'Tambah Kategori')
@section('heading', $kategori->exists ? 'Edit Kategori' : 'Tambah Kategori')
@section('subheading', 'Informasi kategori buku')

@section('content')
    <x-alert />

    <form method="POST" action="{{ $kategori->exists ? route('admin.kategori.update', $kategori) : route('admin.kategori.store') }}" class="card max-w-xl space-y-4 p-5">
        @csrf
        @if ($kategori->exists)
            @method('PUT')
        @endif

        <div>
            <label for="nama" class="label">Nama Kategori <span class="text-rose-500">*</span></label>
            <input type="text" id="nama" name="nama" value="{{ old('nama', $kategori->nama) }}" required class="input">
            @error('nama')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="deskripsi" class="label">Deskripsi</label>
            <textarea id="deskripsi" name="deskripsi" rows="3" class="input">{{ old('deskripsi', $kategori->deskripsi) }}</textarea>
        </div>

        <div class="flex gap-2">
            <button class="btn-primary">{{ $kategori->exists ? 'Simpan Perubahan' : 'Simpan' }}</button>
            <a href="{{ route('admin.kategori.index') }}" class="btn-secondary">Batal</a>
        </div>
    </form>
@endsection
