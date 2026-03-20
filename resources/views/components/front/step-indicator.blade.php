@props(['steps' => [], 'current' => 1])

<div {{ $attributes->merge(['class' => 'flex items-center justify-center gap-2']) }}>
    @foreach($steps as $index => $label)
        @php
            $stepNum = $index + 1;
            $isActive = $stepNum === $current;
            $isCompleted = $stepNum < $current;
        @endphp

        {{-- Step dot + label --}}
        <div class="!flex items-center gap-2">
            <div class="flex flex-col items-center gap-1.5">
                <div @class([
                    'w-9 h-9 rounded-full flex items-center justify-center text-sm font-bold transition-all duration-200',
                    'bg-primary text-white scale-110' => $isActive,
                    'bg-primary/80 text-white' => $isCompleted,
                    'bg-gray-100 text-gray-400 border border-gray-200' => !$isActive && !$isCompleted,
                ])>
                    @if($isCompleted)
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                        </svg>
                    @else
                        {{ $stepNum }}
                    @endif
                </div>
                <span @class([
                    'text-xs font-medium transition-colors duration-200 hidden sm:block',
                    'text-primary' => $isActive,
                    'text-gray-900' => $isCompleted,
                    'text-gray-400' => !$isActive && !$isCompleted,
                ])>
                    {{ $label }}
                </span>
            </div>

            {{-- Connector line --}}
            @if(!$loop->last)
                <div @class([
                    'w-4 md:w-12 h-0.5 rounded-full transition-colors duration-200 mb-1 sm:mb-6',
                    'bg-primary/60' => $isCompleted,
                    'bg-gray-200' => !$isCompleted,
                ])></div>
            @endif
        </div>
    @endforeach
</div>