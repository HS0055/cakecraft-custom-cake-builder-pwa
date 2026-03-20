{{-- ═══════════════════════════════════════════════════════
CUSTOM CAKE CTA BANNER
═══════════════════════════════════════════════════════ --}}
<section class="py-10 md:py-14">
    <div class="front-container">
        <div
            class="relative overflow-hidden rounded-2xl bg-primary/5 border border-primary/10 px-6 py-8 md:py-10 md:px-12">

            {{-- Subtle decorative pattern --}}
            <div class="absolute inset-0 pointer-events-none opacity-[0.03]">
                <svg class="w-full h-full" preserveAspectRatio="none" viewBox="0 0 400 200"
                    xmlns="http://www.w3.org/2000/svg">
                    <circle cx="350" cy="30" r="80" fill="currentColor" class="text-primary" />
                    <circle cx="50" cy="170" r="60" fill="currentColor" class="text-primary" />
                </svg>
            </div>

            {{-- Content --}}
            <div class="relative z-10 flex flex-col md:flex-row items-center gap-6 md:gap-12">
                {{-- Left: Icon + Text --}}
                <div class="flex-1 text-center md:text-start">
                    <div class="inline-flex items-center gap-2 mb-3 px-3 py-1 rounded-full bg-primary/10 text-primary">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09ZM18.259 8.715 18 9.75l-.259-1.035a3.375 3.375 0 0 0-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 0 0 2.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 0 0 2.455 2.456L21.75 6l-1.036.259a3.375 3.375 0 0 0-2.455 2.456Z" />
                        </svg>
                        <span
                            class="text-xs font-semibold uppercase tracking-wider">{{ __('front.home.get_creative') ?? 'Get creative!' }}</span>
                    </div>
                    <h2 class="font-display text-2xl md:text-3xl font-bold text-gray-900 mb-2 leading-tight">
                        {{ __('front.home.design_dream_cake') }}
                    </h2>
                    <p class="text-gray-500 text-sm md:text-base leading-relaxed max-w-lg">
                        {{ __('front.home.dream_cake_subtitle') }}
                    </p>
                </div>

                {{-- Right: Button --}}
                <div class="shrink-0">
                    <a href="{{ route('front.cake-builder') }}" wire:navigate
                        class="group inline-flex items-center justify-center px-8 py-3.5 rounded-xl bg-primary text-white font-semibold text-sm hover:bg-primary-hover transition-all duration-200 active:scale-[0.97] shadow-sm">
                        {{ __('front.home.start_building') }}
                        <svg class="w-4 h-4 ms-2 transition-transform duration-200 group-hover:translate-x-0.5 rtl:rotate-180 rtl:group-hover:-translate-x-0.5"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>