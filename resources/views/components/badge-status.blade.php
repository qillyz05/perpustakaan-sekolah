@props(['status'])

@php
    $map = [
        'diajukan' => ['label' => 'Menunggu Persetujuan', 'class' => 'bg-indigo-100 text-indigo-700'],
        'dipinjam' => ['label' => 'Dipinjam', 'class' => 'bg-blue-100 text-blue-700'],
        'dikembalikan' => ['label' => 'Dikembalikan', 'class' => 'bg-emerald-100 text-emerald-700'],
        'terlambat' => ['label' => 'Terlambat', 'class' => 'bg-rose-100 text-rose-700'],
        'sudah_dibayar' => ['label' => 'Lunas', 'class' => 'bg-emerald-100 text-emerald-700'],
        'belum_dibayar' => ['label' => 'Belum Bayar', 'class' => 'bg-amber-100 text-amber-700'],
        'tersedia' => ['label' => 'Tersedia', 'class' => 'bg-emerald-100 text-emerald-700'],
        'habis' => ['label' => 'Habis', 'class' => 'bg-rose-100 text-rose-700'],
    ];
    $item = $map[$status] ?? ['label' => ucfirst($status), 'class' => 'bg-slate-100 text-slate-700'];
@endphp

<span {{ $attributes->merge(['class' => 'badge '.$item['class']]) }}>{{ $item['label'] }}</span>
