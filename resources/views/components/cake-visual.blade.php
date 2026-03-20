@props([
    'shape' => null,
    'flavorLayer' => null,
    'toppingLayers' => [],
    'color' => null,
    'mode' => 'final',
])

@php
    $baseImage = $shape?->getFirstMediaUrl('base_image');
    $baseCutImage = $shape?->getFirstMediaUrl('base_cut_image');
    $flavorImage = $flavorLayer?->getFirstMediaUrl('full_image');
    $flavorCutImage = $flavorLayer?->getFirstMediaUrl('cut_image');
    $toppingLayers = collect($toppingLayers);

    $shapeMaskStyles = $color && $baseImage
        ? [
            "background-color: {$color->hex_code}",
            "mask-image: url('{$baseImage}')",
            "-webkit-mask-image: url('{$baseImage}')",
            "mask-repeat: no-repeat",
            "mask-position: center",
            "mask-size: contain",
            "transform: translateZ(0)",
            "will-change: transform",
            "mix-blend-mode: multiply",
        ]
        : [];

    $cutMaskStyles = $color && $baseCutImage
        ? [
            "background-color: {$color->hex_code}",
            "mask-image: url('{$baseCutImage}')",
            "-webkit-mask-image: url('{$baseCutImage}')",
            "mask-repeat: no-repeat",
            "mask-position: center",
            "mask-size: contain",
            "transform: translateZ(0)",
            "will-change: transform",
            "mix-blend-mode: multiply",
        ]
        : [];

    $colorOverlayStyles = $color && $baseImage
        ? [
            "background-color: {$color->hex_code}",
            "mix-blend-mode: multiply",
        ]
        : [];
@endphp

<div {{ $attributes->merge(['class' => 'relative overflow-hidden']) }}>
    @if ($shape)
        <div class="relative h-full w-full">
            @if ($mode === 'shape')
                @if ($baseImage)
                    <img src="{{ $baseImage }}" alt="{{ $shape->name }}" crossorigin="anonymous" class="absolute inset-0 h-full w-full object-contain z-10" />
                @else
                    <div class="flex h-full w-full items-center justify-center text-foreground-subtle">
                        <svg class="h-1/2 w-1/2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909M18 13.5V18a2.25 2.25 0 0 1-2.25 2.25H5.25A2.25 2.25 0 0 1 3 18v-4.5" />
                        </svg>
                    </div>
                @endif
            @elseif ($mode === 'flavor')
                @if ($baseImage)
                    <img src="{{ $baseImage }}" alt="{{ $shape->name }}" crossorigin="anonymous" class="absolute inset-0 h-full w-full object-contain z-10" />
                @endif
                @if ($flavorImage)
                    <img src="{{ $flavorImage }}" alt="Flavor layer" crossorigin="anonymous" class="absolute inset-0 h-full w-full object-contain z-30" />
                @endif
            @elseif ($mode === 'color')
                @if ($flavorCutImage)
                    <img src="{{ $flavorCutImage }}" alt="Flavor cut layer" crossorigin="anonymous" class="absolute inset-0 h-full w-full object-contain z-10" />
                @elseif ($flavorImage)
                    <img src="{{ $flavorImage }}" alt="Flavor layer" crossorigin="anonymous" class="absolute inset-0 h-full w-full object-contain z-10" />
                @endif
                <div x-data x-init="$nextTick(() => { $el.style.display = 'none'; $el.offsetHeight; $el.style.display = 'block'; })" class="absolute inset-0 z-20"
                    wire:key="cut-mask-{{ $shape?->id }}-{{ $color->id ?? $color?->hex_code }}" @style($cutMaskStyles)></div>

            @elseif ($mode === 'toppings')
                @if ($baseImage)
                    <img src="{{ $baseImage }}" alt="{{ $shape->name }}" crossorigin="anonymous" class="absolute inset-0 h-full w-full object-contain z-10" />
                    @if ($shapeMaskStyles)
                        <div x-data x-init="$nextTick(() => { $el.style.display = 'none'; $el.offsetHeight; $el.style.display = 'block'; })" class="absolute inset-0 z-20"
                            wire:key="shape-mask-{{ $shape?->id }}-{{ $color->id ?? $color?->hex_code }}" @style($shapeMaskStyles)></div>
                    @endif
                @endif

                @foreach ($toppingLayers as $layer)
                    @php
                        $layerUrl = $layer->getFirstMediaUrl('image_layer');
                    @endphp
                    @if ($layerUrl)
                        <img src="{{ $layerUrl }}" alt="Topping layer" crossorigin="anonymous" class="absolute inset-0 h-full w-full object-contain z-40" />
                    @endif
                @endforeach
            @else
                {{-- Final Mode (Default) --}}
                @if ($flavorImage)
                    <img src="{{ $flavorImage }}" alt="Flavor layer" crossorigin="anonymous" class="absolute inset-0 h-full w-full object-contain z-0" />
                @endif

                @if ($baseImage)
                    <img src="{{ $baseImage }}" alt="{{ $shape->name }}" crossorigin="anonymous" class="absolute inset-0 h-full w-full object-contain z-10" />
                    @if ($color)
                        <div class="absolute inset-0 z-20" @style($shapeMaskStyles)></div>
                    @endif
                @else
                    <div class="flex h-full w-full items-center justify-center text-foreground-subtle">
                        <svg class="h-1/2 w-1/2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909M18 13.5V18a2.25 2.25 0 0 1-2.25 2.25H5.25A2.25 2.25 0 0 1 3 18v-4.5" />
                        </svg>
                    </div>
                @endif

                @foreach ($toppingLayers as $layer)
                    @php
                        $layerUrl = $layer->getFirstMediaUrl('image_layer');
                    @endphp
                    @if ($layerUrl)
                        <img src="{{ $layerUrl }}" alt="Topping layer" crossorigin="anonymous" class="absolute inset-0 h-full w-full object-contain z-40" />
                    @endif
                @endforeach
            @endif
        </div>
    @else
        <div class="flex h-full w-full flex-col items-center justify-center gap-2 text-foreground-subtle">
            <svg class="h-1/3 w-1/3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M12 6.75c-3.642 0-6.75 1.678-6.75 3.75s3.108 3.75 6.75 3.75 6.75-1.678 6.75-3.75S15.642 6.75 12 6.75Zm0 0V5.25m0 1.5c2.946 0 5.25-1.007 5.25-2.25S14.946 2.25 12 2.25 6.75 3.257 6.75 4.5 9.054 6.75 12 6.75Zm-6.75 7.5c0 2.072 3.108 3.75 6.75 3.75s6.75-1.678 6.75-3.75" />
            </svg>
            <p class="text-[10px] sm:text-xs text-foreground-muted text-center px-2">Select a shape to preview</p>
        </div>
    @endif
</div>
