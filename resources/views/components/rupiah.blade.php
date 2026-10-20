@props(['value' => null, 'label' => null])

<span {{ $attributes->merge(['class' => 'whitespace-nowrap']) }}>
    Rp {{ number_format((float) $value, 0, ',', '.') }}
</span>
