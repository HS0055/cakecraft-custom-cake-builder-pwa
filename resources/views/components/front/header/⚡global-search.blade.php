<?php

use Livewire\Component;
use App\Models\ReadyCake;

new class extends Component {
    public string $search = '';

    public function with(): array
    {
        return [
            'results' => strlen($this->search) >= 2
                ? ReadyCake::where('is_active', true)
                    ->where(function ($q) {
                        $q->where('name', 'like', '%' . $this->search . '%');
                    })
                    ->with(['media', 'cakeShape.media', 'cakeColor', 'cakeFlavor'])
                    ->take(5)
                    ->get()
                : []
        ];
    }
};
?>
<div class="relative" x-data="{ searchOpen: false }" @click.outside="searchOpen = false"
    @keydown.escape="searchOpen = false; $wire.set('search', '')">

    {{-- Search Icon Button --}}
    <button @click="searchOpen = !searchOpen; $nextTick(() => $refs.searchInput?.focus())"
        class="p-2.5 rounded-lg hover:bg-primary/5 transition-colors duration-150 group"
        aria-label="{{ __('front.search.search') }}" :aria-expanded="searchOpen">
        <svg class="w-5 h-5 text-gray-600 group-hover:text-gray-900 transition-colors" fill="none" viewBox="0 0 24 24"
            stroke="currentColor" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round"
                d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
        </svg>
    </button>

    {{-- Expandable Search Panel --}}
    <div x-show="searchOpen" x-cloak x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="absolute end-0 top-full mt-2 w-80 bg-white rounded-xl border border-gray-200 shadow-lg overflow-hidden z-[60] origin-top-right">

        {{-- Input --}}
        <div class="relative p-3 border-b border-gray-100">
            <input wire:model.live.debounce.300ms="search" x-ref="searchInput" type="text"
                placeholder="{{ __('front.search.search_cakes') }}"
                class="w-full ps-9 pe-9 py-2 bg-gray-100 rounded-lg text-sm font-medium text-gray-900 placeholder:text-gray-400 outline-none border border-transparent focus:border-gray-300 focus:bg-white transition-colors duration-150"
                autocomplete="off">

            <svg class="absolute start-6 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none" fill="none"
                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
            </svg>

            {{-- Loading --}}
            <div wire:loading wire:target="search" class="absolute end-6 top-1/2 -translate-y-1/2">
                <svg class="w-4 h-4 text-gray-400 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3"></circle>
                    <path class="opacity-75" fill="currentColor"
                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                    </path>
                </svg>
            </div>

            {{-- Clear --}}
            @if(strlen($search) > 0)
                <button wire:click="$set('search', '')" wire:loading.remove wire:target="search"
                    class="absolute end-5 top-1/2 -translate-y-1/2 p-0.5 text-gray-400 hover:text-gray-600 transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            @endif
        </div>

        {{-- Results --}}
        @if(strlen($search) >= 2)
            @if(count($results) > 0)
                <div class="py-1 max-h-72 overflow-y-auto">
                    @foreach($results as $cake)
                        <a href="{{ route('front.ready-cake.show', ['readyCake' => $cake->id]) }}" wire:navigate
                            class="flex items-center gap-3 px-4 py-2.5 hover:bg-primary/5 transition-colors duration-150 group">
                            <div class="w-10 h-10 rounded-lg overflow-hidden shrink-0 bg-gray-100 border border-gray-200">
                                @if($cake->getFirstMediaUrl('preview'))
                                    <img src="{{ $cake->getFirstMediaUrl('preview') }}" alt="{{ $cake->name }}"
                                        class="w-full h-full object-cover">
                                @else
                                    @php
                                        $shapeToppingLayers = $cake->cake_topping_id
                                            ? \App\Models\ShapeTopping::with('media')
                                                ->where('cake_shape_id', $cake->cake_shape_id)
                                                ->where('cake_topping_id', $cake->cake_topping_id)
                                                ->get()
                                            : collect();
                                    @endphp
                                    <div class="w-full h-full flex items-center justify-center p-0.5">
                                        <x-cake-visual class="w-full h-full" :shape="$cake->cakeShape" :color="$cake->cakeColor"
                                            :toppingLayers="$shapeToppingLayers" mode="final" />
                                    </div>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 truncate">
                                    {{ $cake->name }}
                                </p>
                                <x-front.price :amount="$cake->price" class="text-xs text-gray-400 mt-0.5 block" />
                            </div>
                        </a>
                    @endforeach
                </div>

                <a href="{{ route('front.shop', ['search' => $search]) }}" wire:navigate
                    class="block px-4 py-2.5 text-center text-xs font-medium text-gray-500 border-t border-gray-100 hover:bg-primary/5 hover:text-gray-700 transition-colors duration-150">
                    {{ __('front.search.view_all_results') }}
                </a>
            @else
                <div class="px-4 py-8 text-center">
                    <p class="text-sm text-gray-400">{{ __('front.search.no_results', ['query' => $search]) }}</p>
                </div>
            @endif
        @endif
    </div>
</div>