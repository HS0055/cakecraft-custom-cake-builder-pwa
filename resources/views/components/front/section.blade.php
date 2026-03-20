@props(['title' => '', 'subtitle' => '', 'handwritten' => '', 'centered' => true, 'divider' => false])

<section {{ $attributes->merge(['class' => 'front-section']) }}>
    <div class="front-container">
        @if($title)
            <div class="{{ $centered ? 'text-center' : '' }} mb-12">
                @if($handwritten)
                    <span class="font-handwritten text-2xl md:text-3xl text-pink mb-3 block transform -rotate-2">
                        {{ $handwritten }}
                    </span>
                @endif
                <h2 class="font-display text-3xl md:text-4xl lg:text-5xl font-bold text-espresso tracking-tight">
                    {{ $title }}
                </h2>
                @if($subtitle)
                    <p class="mt-3 text-foreground-muted text-lg max-w-2xl {{ $centered ? 'mx-auto' : '' }}">
                        {{ $subtitle }}
                    </p>
                @endif
                @if($divider)
                    <div class="mt-4 mx-auto w-16 h-1.5 rounded-full bg-pink/40"></div>
                @endif
            </div>
        @endif

        {{ $slot }}
    </div>
</section>