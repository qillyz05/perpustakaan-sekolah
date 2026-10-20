@props(['judul' => 'Konfirmasi', 'pesan' => 'Apakah Anda yakin?', 'aksi' => '#', 'metode' => 'POST', 'tombol' => 'Hapus', 'warna' => 'bg-rose-600 hover:bg-rose-700 focus:ring-rose-500'])

<div {{ $attributes->merge(['class' => 'inline-block']) }} x-data="{ open: false }">
    <button type="button" @click="open = true" {{ $attributes->only('class')->merge(['class' => 'btn-danger btn-sm']) }}>
        {{ $slot }}
    </button>

    <div x-show="open" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="fixed inset-0 bg-slate-900/50" @click="open = false"></div>
        <div class="relative w-full max-w-md rounded-2xl bg-white p-6 shadow-xl">
            <h3 class="text-lg font-bold text-slate-900">{{ $judul }}</h3>
            <p class="mt-2 text-sm text-slate-600">{{ $pesan }}</p>
            <div class="mt-6 flex justify-end gap-2">
                <button type="button" class="btn-secondary btn-sm" @click="open = false">Batal</button>
                <form method="POST" action="{{ $aksi }}">
                    @csrf
                    @if (strtoupper($metode) !== 'POST')
                        @method($metode)
                    @endif
                    <button class="btn btn-sm {{ $warna }} text-white">{{ $tombol }}</button>
                </form>
            </div>
        </div>
    </div>
</div>
