<?php

use Livewire\Component;
use App\Models\CakeShape;

new class extends Component {
    public function with(): array
    {
        return [
            'shapes' => CakeShape::orderBy('name')
                ->with('media')
                ->get(),
        ];
    }
};
?>
<div>
    @if($shapes->count())
        <section class="py-10 md:py-14">
            <div class="front-container">

                {{-- Section Header --}}
                <div class="flex items-center justify-between pb-4 mb-6 border-b border-gray-200">
                    <h2 class="font-display text-xl md:text-2xl font-bold text-gray-900">
                        {{ __('front.shop.shop_by_shape') }}
                    </h2>
                    <a href="{{ route('front.shop') }}" wire:navigate
                        class="text-sm font-medium text-primary hover:text-primary-hover transition-colors duration-150">
                        {{ __('front.shop.shop_by_shape_subtitle') }}
                    </a>
                </div>

                {{-- Mobile: Swiper slider --}}
                <div class="md:hidden" x-data="{ 
                        swiper: null,
                        init() {
                            this.swiper = new Swiper($el.querySelector('.shape-swiper'), {
                                modules: [window.SwiperModules.FreeMode],
                                slidesPerView: 3.5,
                                spaceBetween: 10,
                                freeMode: true,
                                touchEventsTarget: 'container',
                                breakpoints: {
                                    480: {
                                        slidesPerView: 4.5,
                                        spaceBetween: 12,
                                    },
                                },
                            });
                        }
                    }">
                    <div class="swiper shape-swiper">
                        <div class="swiper-wrapper">
                            @foreach($shapes as $shape)
                                <div class="swiper-slide" style="width: auto;" wire:key="shape-mobile-{{ $shape->id }}">
                                    <a href="{{ route('front.shop', ['shapeFilter' => $shape->id]) }}" wire:navigate
                                        class="group relative flex flex-col items-center rounded-xl p-2 transition-all duration-200 overflow-hidden cursor-pointer select-none">

                                        {{-- Card Background --}}
                                        <div
                                            class="absolute inset-0 bg-primary/5 rounded-xl border border-primary/10 transition-all duration-200 group-hover:bg-primary/10 group-hover:border-primary/20">
                                        </div>

                                        {{-- Image --}}
                                        <div
                                            class="relative w-full aspect-square flex items-center justify-center z-10 mb-1 transition-transform duration-300 group-hover:scale-105">
                                            @php
                                                $thumbnail = $shape->getFirstMediaUrl('thumbnail');
                                                $baseImage = $shape->getFirstMediaUrl('base_image');
                                                $imageUrl = $thumbnail ?: $baseImage;
                                            @endphp
                                            @if($imageUrl)
                                                <img src="{{ $imageUrl }}" alt="{{ $shape->name }}"
                                                    class="w-full h-full object-contain pointer-events-none" loading="lazy"
                                                    draggable="false">
                                            @else
                                                <svg class="w-10 h-10 text-gray-300 pointer-events-none" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M21 7.5l-2.25-1.313M21 7.5v2.25m0-2.25l-2.25 1.313M3 7.5l2.25-1.313M3 7.5l2.25 1.313M3 7.5v2.25m9 3l2.25-1.313M12 12.75l-2.25-1.313M12 12.75V15m0 6.75l2.25-1.313M12 21.75V19.5m0 2.25l-2.25-1.313m0-16.875L12 2.25l2.25 1.313M21 14.25v2.25l-2.25 1.313m-13.5 0L3 16.5v-2.25" />
                                                </svg>
                                            @endif
                                        </div>

                                        {{-- Label --}}
                                        <div class="relative z-10 w-full text-center pointer-events-none">
                                            <span
                                                class="text-xs font-semibold text-gray-700 tracking-wide group-hover:text-primary transition-colors duration-200">
                                                {{ $shape->name }}
                                            </span>
                                        </div>
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- Desktop: CSS Grid --}}
                <div class="hidden md:grid md:grid-cols-6 md:gap-4">
                    @foreach($shapes as $shape)
                        <a href="{{ route('front.shop', ['shapeFilter' => $shape->id]) }}" wire:navigate
                            wire:key="shape-desktop-{{ $shape->id }}"
                            class="group relative flex flex-col items-center rounded-xl p-4 transition-all duration-200 overflow-hidden cursor-pointer">

                            {{-- Card Background --}}
                            <div
                                class="absolute inset-0 bg-primary/5 rounded-xl border border-primary/10 transition-all duration-200 group-hover:bg-primary/10 group-hover:border-primary/20">
                            </div>

                            {{-- Image --}}
                            <div
                                class="relative w-full aspect-square flex items-center justify-center z-10 mb-3 transition-transform duration-300 group-hover:scale-105">
                                @php
                                    $thumbnail = $shape->getFirstMediaUrl('thumbnail');
                                    $baseImage = $shape->getFirstMediaUrl('base_image');
                                    $imageUrl = $thumbnail ?: $baseImage;
                                @endphp
                                @if($imageUrl)
                                    <img src="{{ $imageUrl }}" alt="{{ $shape->name }}" class="w-full h-full object-contain"
                                        loading="lazy">
                                @else
                                    <svg class="w-10 h-10 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                        stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M21 7.5l-2.25-1.313M21 7.5v2.25m0-2.25l-2.25 1.313M3 7.5l2.25-1.313M3 7.5l2.25 1.313M3 7.5v2.25m9 3l2.25-1.313M12 12.75l-2.25-1.313M12 12.75V15m0 6.75l2.25-1.313M12 21.75V19.5m0 2.25l-2.25-1.313m0-16.875L12 2.25l2.25 1.313M21 14.25v2.25l-2.25 1.313m-13.5 0L3 16.5v-2.25" />
                                    </svg>
                                @endif
                            </div>

                            {{-- Label --}}
                            <div class="relative z-10 w-full text-center">
                                <span
                                    class="text-sm font-semibold text-gray-700 tracking-wide group-hover:text-primary transition-colors duration-200">
                                    {{ $shape->name }}
                                </span>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif
</div>