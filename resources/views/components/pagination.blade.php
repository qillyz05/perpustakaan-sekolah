@if ($items->hasPages())
    <div {{ $attributes->merge(['class' => 'mt-6']) }}>
        {{ $items->withQueryString()->links() }}
    </div>
@endif
