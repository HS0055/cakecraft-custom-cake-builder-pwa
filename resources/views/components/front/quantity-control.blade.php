@props(['value' => 1, 'min' => 1, 'max' => 99, 'wireIncrement' => null, 'wireDecrement' => null, 'size' => 'md'])

@php
    $btnClass = $size === 'lg' ? 'w-11 h-11' : 'w-8 h-8';
    $valClass = $size === 'lg' ? 'w-12 text-lg' : 'w-8 text-sm';
    $iconClass = $size === 'lg' ? 'w-4 h-4' : 'w-3.5 h-3.5';
@endphp

<div {{ $attributes->merge(['class' => 'inline-flex items-center gap-1 bg-primary/5 rounded-full p-1 border-2 border-transparent']) }}>
    {{-- Decrement --}}
    <button @if($wireDecrement) wire:click="{{ $wireDecrement }}" wire:loading.attr="disabled" @endif @if($value <= $min)
    disabled @endif
        class="{{ $btnClass }} flex items-center justify-center rounded-full bg-white text-espresso hover:bg-pink hover:text-white disabled:opacity-30 disabled:hover:bg-white disabled:hover:text-espresso transition-all duration-200 cursor-pointer shadow-sm">
        <svg class="{{ $iconClass }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14" />
        </svg>
    </button>

    {{-- Value --}}
    <span class="{{ $valClass }} text-center font-bold text-espresso tabular-nums">
        {{ $value }}
    </span>

    {{-- Increment --}}
    <button @if($wireIncrement) wire:click="{{ $wireIncrement }}" wire:loading.attr="disabled" @endif @if($value >= $max)
    disabled @endif
        class="{{ $btnClass }} flex items-center justify-center rounded-full bg-white text-espresso hover:bg-pink hover:text-white disabled:opacity-30 disabled:hover:bg-white disabled:hover:text-espresso transition-all duration-200 cursor-pointer shadow-sm">
        <svg class="{{ $iconClass }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
        </svg>
    </button>
</div>