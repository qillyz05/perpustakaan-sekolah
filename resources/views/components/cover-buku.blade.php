@props(['buku', 'size' => 'md'])

@php
    $sizes = [
        'sm' => 'h-20 w-16 text-lg',
        'md' => 'h-48 w-36 text-3xl',
    ];
    $cls = $sizes[$size] ?? $sizes['md'];
    $colors = ['bg-primary-600', 'bg-emerald-600', 'bg-amber-600', 'bg-rose-600', 'bg-indigo-600', 'bg-cyan-600'];
    $color = $colors[$buku->id % count($colors)];
@endphp

@if ($buku->cover)
    <img src="{{ Storage::url($buku->cover) }}" alt="{{ $buku->judul }}" class="{{ $cls }} rounded-lg object-cover shadow">
@else
    <div class="flex {{ $cls }} flex-col justify-between rounded-lg p-2 font-semibold leading-tight text-white shadow {{ $color }}">
        <span class="text-[10px] uppercase opacity-80">{{ $buku->kategori->nama ?? 'Buku' }}</span>
        <span class="line-clamp-3 text-xs">{{ $buku->judul }}</span>
        <span class="text-[10px] opacity-80">{{ $buku->penulis }}</span>
    </div>
@endif
