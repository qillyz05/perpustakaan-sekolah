@extends('layouts.admin')

@section('title', $buku->exists ? 'Edit Buku' : 'Tambah Buku')
@section('heading', $buku->exists ? 'Edit Buku' : 'Tambah Buku')
@section('subheading', $buku->exists ? $buku->judul : 'Tambahkan koleksi baru')

@section('content')
    <x-alert />

    <form method="POST" action="{{ $buku->exists ? route('admin.buku.update', $buku) : route('admin.buku.store') }}" enctype="multipart/form-data" class="grid gap-6 lg:grid-cols-3">
        @csrf
        @if ($buku->exists)
            @method('PUT')
        @endif

        <div class="card space-y-4 p-5 lg:col-span-2">
            <h2 class="font-bold text-slate-900">Informasi Buku</h2>

            <div>
                <label for="judul" class="label">Judul <span class="text-rose-500">*</span></label>
                <input type="text" id="judul" name="judul" value="{{ old('judul', $buku->judul) }}" required class="input">
                @error('judul')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label for="penulis" class="label">Penulis <span class="text-rose-500">*</span></label>
                    <input type="text" id="penulis" name="penulis" value="{{ old('penulis', $buku->penulis) }}" required class="input">
                    @error('penulis')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="penerbit" class="label">Penerbit</label>
                    <input type="text" id="penerbit" name="penerbit" value="{{ old('penerbit', $buku->penerbit) }}" class="input">
                </div>
                <div>
                    <label for="tahun_terbit" class="label">Tahun Terbit</label>
                    <input type="number" id="tahun_terbit" name="tahun_terbit" value="{{ old('tahun_terbit', $buku->tahun_terbit) }}" min="1900" max="{{ date('Y') + 1 }}" class="input">
                    @error('tahun_terbit')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="isbn" class="label">ISBN</label>
                    <input type="text" id="isbn" name="isbn" value="{{ old('isbn', $buku->isbn) }}" class="input">
                </div>
                <div>
                    <label for="kategori_id" class="label">Kategori <span class="text-rose-500">*</span></label>
                    <select id="kategori_id" name="kategori_id" required class="input">
                        <option value="">Pilih kategori</option>
                        @foreach ($kategori as $kat)
                            <option value="{{ $kat->id }}" @selected(old('kategori_id', $buku->kategori_id) == $kat->id)>{{ $kat->nama }}</option>
                        @endforeach
                    </select>
                    @error('kategori_id')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="rak" class="label">Lokasi Rak</label>
                    <input type="text" id="rak" name="rak" value="{{ old('rak', $buku->rak) }}" placeholder="mis. Rak A1" class="input">
                </div>
            </div>

            <div>
                <label for="sinopsis" class="label">Sinopsis</label>
                <textarea id="sinopsis" name="sinopsis" rows="4" class="input">{{ old('sinopsis', $buku->sinopsis) }}</textarea>
            </div>
        </div>

        <div class="space-y-6">
            <div class="card space-y-4 p-5">
                <h2 class="font-bold text-slate-900">Inventaris</h2>

                <div>
                    <label for="kode_buku" class="label">Kode Buku</label>
                    <input type="text" id="kode_buku" name="kode_buku" value="{{ old('kode_buku', $buku->kode_buku) }}" placeholder="Otomatis jika kosong" class="input">
                    @error('kode_buku')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="jumlah_eksemplar" class="label">Jumlah Eksemplar <span class="text-rose-500">*</span></label>
                    <input type="number" id="jumlah_eksemplar" name="jumlah_eksemplar" value="{{ old('jumlah_eksemplar', $buku->jumlah_eksemplar) }}" min="1" required class="input">
                    @error('jumlah_eksemplar')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                </div>

                @if ($buku->exists)
                    <p class="rounded-lg bg-slate-50 px-3 py-2 text-xs text-slate-500">Stok tersedia saat ini: <strong>{{ $buku->tersedia }}</strong></p>
                @endif
            </div>

            <div class="card p-5" x-data="{ preview: null }">
                <h2 class="mb-3 font-bold text-slate-900">Cover Buku</h2>
                <template x-if="preview">
                    <img :src="preview" class="mb-3 h-48 w-full rounded-lg object-cover">
                </template>
                @if ($buku->cover)
                    <img src="{{ Storage::url($buku->cover) }}" class="mb-3 h-48 w-full rounded-lg object-cover" x-show="!preview">
                @endif
                <input type="file" name="cover" accept="image/*" class="input text-xs"
                       @change="const f = $event.target.files[0]; if (f) { const r = new FileReader(); r.onload = e => preview = e.target.result; r.readAsDataURL(f); }">
                <p class="mt-2 text-xs text-slate-400">JPG, PNG, WEBP maks 2MB.</p>
                @error('cover')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
            </div>

            <div class="flex gap-2">
                <button class="btn-primary flex-1">{{ $buku->exists ? 'Simpan Perubahan' : 'Simpan Buku' }}</button>
                <a href="{{ route('admin.buku.index') }}" class="btn-secondary">Batal</a>
            </div>
        </div>
    </form>
@endsection
