@props([
    'shape' => null,
    'flavorLayer' => null,
    'toppingLayers' => [],
    'color' => null,
    'label' => null,
    'mode' => 'final',
])

<div {{ $attributes->merge(['class' => 'card-base p-4']) }}>
    @if ($label)
        <p class="mb-3 text-sm font-medium text-foreground">{{ $label }}</p>
    @endif
    <div
        class="relative mx-auto h-64 w-full max-w-[280px] overflow-hidden rounded-xl border border-border bg-surface-alt/40">
        <x-cake-visual
            class="h-full w-full"
            :shape="$shape"
            :flavorLayer="$flavorLayer"
            :toppingLayers="$toppingLayers"
            :color="$color"
            :mode="$mode"
        />
    </div>
</div>
