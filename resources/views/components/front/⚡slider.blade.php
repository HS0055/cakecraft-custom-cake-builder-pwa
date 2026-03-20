<?php

use Livewire\Component;
use App\Models\Slider;

new class extends Component {
    public function with(): array
    {
        return [
            'sliders' => Slider::where('is_active', true)
                ->orderBy('sort_order')
                ->with('media')
                ->get(),
        ];
    }
};
?>
<div>
    @if($sliders->count())
        <section class="pt-8 pb-4 md:pb-6">
            <div class="front-container">
                <div x-data="{
                                                    current: 0,
                                                    count: {{ $sliders->count() }},
                                                    autoplay: null,
                                                    startX: 0,
                                                    endX: 0,
                                                    startAutoplay() {
                                                        if (this.count > 1 && !this.autoplay) {
                                                            this.autoplay = setInterval(() => this.next(), 5000);
                                                        }
                                                    },
                                                    stopAutoplay() {
                                                        if (this.autoplay) {
                                                            clearInterval(this.autoplay);
                                                            this.autoplay = null;
                                                        }
                                                    },
                                                    init() {
                                                        this.startAutoplay();
                                                    },
                                                    destroy() {
                                                        this.stopAutoplay();
                                                    },
                                                    next() { this.current = (this.current + 1) % this.count },
                                                    prev() { this.current = (this.current - 1 + this.count) % this.count },
                                                    goTo(i) { this.current = i },
                                                    handleTouchStart(e) { this.startX = e.touches[0].clientX; },
                                                    handleTouchEnd(e) {
                                                        this.endX = e.changedTouches[0].clientX;
                                                        const diff = this.startX - this.endX;
                                                        // Require a minimum swipe distance to trigger
                                                        if (Math.abs(diff) > 50) {
                                                            if (diff > 0) { this.next(); } // Swiped left, go to next
                                                            else { this.prev(); } // Swiped right, go to previous
                                                        }
                                                    }
                                                }" @mouseenter="stopAutoplay" @mouseleave="startAutoplay"
                    @keydown.right.prevent="next" @keydown.left.prevent="prev" tabindex="0"
                    class="relative overflow-hidden rounded-3xl group outline-none focus-visible:ring-2 focus-visible:ring-pink"
                    role="region" aria-label="Featured slides">
                    <div class="relative w-full aspect-[21/9] xl:aspect-[5/2] bg-white transition-all duration-300"
                        @touchstart="handleTouchStart" @touchend="handleTouchEnd">
                        @foreach($sliders as $index => $slider)
                            <?php        $slideKey = 'slide-' . ($slider->id ?? $index); ?>
                            <div x-show="current === {{ $index }}" x-transition:enter="transition ease-out duration-500"
                                x-transition:enter-start="opacity-0 scale-105" x-transition:enter-end="opacity-100 scale-100"
                                x-transition:leave="transition ease-in duration-300"
                                x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                                class="absolute inset-0" role="group" aria-roledescription="slide"
                                aria-label="Slide {{ $index + 1 }} of {{ $sliders->count() }}" wire:key="{{ $slideKey }}">

                                @php
                                    $sliderLink = match ($slider->action_type) {
                                        'ready_cake' => $slider->ready_cake_id ? route('front.ready-cake.show', ['readyCake' => $slider->ready_cake_id]) : route('front.shop'),
                                        default => route('front.cake-builder'),
                                    };
                                @endphp

                                <a href="{{ $sliderLink }}" wire:navigate
                                    class="block w-full h-full relative cursor-pointer group/link">
                                    @if($slider->getFirstMediaUrl('image'))
                                        <img src="{{ $slider->getFirstMediaUrl('image') }}"
                                            alt="{{ $slider->title ?? 'Featured promotional offer' }}"
                                            class="w-full h-full object-cover">

                                        {{-- Subtle hover overlay effect --}}
                                        <div
                                            class="absolute inset-0 bg-black/5 opacity-0 group-hover/link:opacity-100 transition-opacity duration-300">
                                        </div>
                                    @endif
                                </a>
                            </div>
                        @endforeach

                        {{-- Slider Dots --}}
                        @if($sliders->count() > 1)
                            <div class="absolute bottom-4 start-1/2 -translate-x-1/2 flex items-center gap-2 z-10">
                                @foreach($sliders as $index => $slider)
                                    <button @click="goTo({{ $index }})"
                                        :class="current === {{ $index }} ? 'bg-white w-8' : 'bg-white/50 w-2.5 hover:bg-white/80'"
                                        class="h-2.5 rounded-full transition-all duration-300 cursor-pointer shadow-sm"></button>
                                @endforeach
                            </div>

                            {{-- Arrows --}}
                            <button @click="prev()" aria-label="Previous slide"
                                class="absolute start-4 top-1/2 -translate-y-1/2 w-10 h-10 bg-white/80 backdrop-blur rounded-full flex items-center justify-center text-espresso hover:bg-white hover:text-pink transition-all shadow-soft cursor-pointer opacity-0 group-hover:opacity-100 z-10">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" />
                                </svg>
                            </button>
                            <button @click="next()" aria-label="Next slide"
                                class="absolute end-4 top-1/2 -translate-y-1/2 w-10 h-10 bg-white/80 backdrop-blur rounded-full flex items-center justify-center text-espresso hover:bg-white hover:text-pink transition-all shadow-soft cursor-pointer opacity-0 group-hover:opacity-100 z-10">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                                </svg>
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </section>
    @endif
</div>