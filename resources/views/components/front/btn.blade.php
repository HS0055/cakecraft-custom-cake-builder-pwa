@props(['variant' => 'primary', 'size' => 'md', 'href' => null, 'icon' => false])

@php
    $baseClasses = 'inline-flex items-center justify-center gap-2 font-medium transition-all duration-200 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-pink/50 focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 cursor-pointer';

    $variants = [
        'primary' => 'bg-pink text-white rounded-full hover:bg-pink-dark hover:shadow-glow-pink active:scale-[0.97]',
        'secondary' => 'bg-white text-espresso border border-border rounded-full hover:bg-frosting hover:border-pink/30 active:scale-[0.97]',
        'ghost' => 'text-espresso rounded-full hover:bg-frosting active:scale-[0.97]',
        'outline' => 'border-2 border-pink text-pink rounded-full hover:bg-pink hover:text-white active:scale-[0.97]',
        'accent' => 'bg-gradient-to-r from-pink to-primary-warm text-white rounded-full hover:shadow-glow-pink active:scale-[0.97]',
    ];

    $sizes = [
        'sm' => 'px-4 py-2 text-sm',
        'md' => 'px-6 py-3 text-sm',
        'lg' => 'px-8 py-3.5 text-base',
        'xl' => 'px-10 py-4 text-lg',
        'icon' => 'p-2.5',
    ];

    $classes = $baseClasses . ' ' . ($variants[$variant] ?? $variants['primary']) . ' ' . ($sizes[$size] ?? $sizes['md']);
@endphp

@if($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </a>
@else
    <button {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </button>
@endif