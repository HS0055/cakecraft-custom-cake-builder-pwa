@props(['label' => '', 'color' => 'pink'])

@php
    $colors = [
        'pink' => 'bg-pink/10 text-pink-dark',
        'green' => 'bg-success-bg text-success',
        'blue' => 'bg-info-bg text-info',
        'yellow' => 'bg-warning-bg text-warning',
        'peach' => 'bg-peach-light text-chocolate',
    ];
    $colorClass = $colors[$color] ?? $colors['pink'];
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold tracking-wide whitespace-nowrap $colorClass"]) }}>
    {{ $label ?: $slot }}
</span>