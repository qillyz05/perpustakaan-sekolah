@props(['title' => 'Tidak ada data', 'description' => 'Belum ada data untuk ditampilkan.', 'icon' => null])

<div {{ $attributes->merge(['class' => 'empty-state']) }}>
    <span class="grid h-14 w-14 place-items-center rounded-2xl bg-slate-100 text-slate-400">
        <svg class="h-7 w-7" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $icon ?? 'M9 13h6m-6 4h4M9 3h6l4 4v14H5V3h4z' }}" />
        </svg>
    </span>
    <p class="text-base font-semibold text-slate-700">{{ $title }}</p>
    <p class="max-w-sm text-sm text-slate-500">{{ $description }}</p>
    @isset($action)
        <div class="mt-2">{{ $action }}</div>
    @endisset
</div>
