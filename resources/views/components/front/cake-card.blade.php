@props(['cake', 'wire' => null])

@php
    $previewUrl = $cake->getFirstMediaUrl('preview');
    $shapeName = $cake->cakeShape?->name ?? 'Cake';
    $flavorName = $cake->cakeFlavor?->name ?? '';
@endphp

<div {{ $attributes->merge(['class' => 'group bg-white rounded-3xl border border-frosting overflow-hidden transition-all duration-300 hover:-translate-y-1 hover:border-pink/20']) }}>
    {{-- Flex row on mobile, stacked on md+ --}}
    <div class="flex flex-row md:flex-col">
        {{-- Image Container --}}
        <div
            class="relative w-[40%] md:w-full aspect-square bg-gradient-to-br from-frosting to-blush/50 overflow-hidden shrink-0">
            <a href="{{ route('front.ready-cake.show', $cake->id) }}" class="block w-full h-full">
                @if($previewUrl)
                    <img src="{{ $previewUrl }}" alt="{{ $cake->name }}"
                        class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105"
                        loading="lazy">
                @else
                    @php
                        $shapeToppingLayers = $cake->cake_topping_id
                            ? $cake->shapeToppings->where('cake_topping_id', $cake->cake_topping_id)->values()
                            : collect();
                    @endphp
                    <div class="w-full h-full bg-surface-alt/40 flex items-center justify-center p-4">
                        <x-cake-visual class="w-full h-full transition-transform duration-500 group-hover:scale-105"
                            :shape="$cake->cakeShape" :color="$cake->cakeColor" :toppingLayers="$shapeToppingLayers"
                            mode="final" />
                    </div>
                @endif
            </a>

            {{-- Subtle hover overlay --}}
            <div
                class="absolute inset-0 bg-espresso/0 group-hover:bg-espresso/5 transition-colors duration-300 pointer-events-none">
            </div>

            {{-- Quick add button (visible on hover, desktop only) --}}
            @if($wire)
                <button wire:click.prevent="{{ $wire }}" wire:loading.attr="disabled" wire:target="{{ $wire }}"
                    class="hidden md:flex absolute bottom-3 end-3 p-2.5 bg-white rounded-full opacity-0 group-hover:opacity-100 transition-all duration-300 translate-y-2 group-hover:translate-y-0 hover:bg-pink hover:text-white text-espresso cursor-pointer z-10 border border-frosting hover:border-pink items-center justify-center">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    <span wire:loading wire:target="{{ $wire }}" class="absolute inset-0 flex items-center justify-center">
                        <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                            </circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z">
                            </path>
                        </svg>
                    </span>
                </button>
            @endif
        </div>

        {{-- Content --}}
        <div class="flex-1 p-3 md:p-5 flex flex-col justify-center min-w-0">
            {{-- Shape & Flavor badges --}}
            <div class="flex items-center gap-2 mb-1.5 md:mb-2 flex-nowrap min-w-0 overflow-hidden">
                <x-front.badge class="shrink-0" :label="$shapeName" color="peach" />
                @if($flavorName)
                    <span class="text-xs text-foreground-muted truncate min-w-0">{{ $flavorName }}</span>
                @endif
            </div>

            {{-- Name --}}
            <h3
                class="font-display text-sm md:text-lg font-semibold text-espresso mb-2 md:mb-3 line-clamp-1 group-hover:text-pink transition-colors duration-200">
                <a href="{{ route('front.ready-cake.show', $cake->id) }}">
                    {{ $cake->name }}
                </a>
            </h3>

            {{-- Price & Add to Cart --}}
            <div class="flex items-center justify-between gap-2">
                <x-front.price :amount="$cake->price" class="text-base md:text-lg text-pink font-bold" />

                @if($wire)
                    <button wire:click.prevent="{{ $wire }}" wire:loading.class="opacity-50 pointer-events-none"
                        wire:target="{{ $wire }}" aria-label="Add {{ $cake->name }} to cart"
                        class="px-3 py-1.5 text-xs font-semibold text-pink bg-pink/10 rounded-full hover:bg-pink hover:text-white transition-all duration-200 cursor-pointer whitespace-nowrap shrink-0">
                        {{ __('front.shop.add_to_cart') }}
                    </button>
                @endif
            </div>
        </div>
    </div>
</div>