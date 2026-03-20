<div>
    {{-- Page Header --}}
    <section class="border-b border-gray-200">
        <div class="front-container py-6 md:py-8">
            <div class="flex items-center gap-2 text-sm text-gray-400 mb-2">
                <a href="{{ route('front.home') }}" wire:navigate
                    class="hover:text-primary transition-colors">{{ __('front.breadcrumb.home') }}</a>
                <svg class="w-3.5 h-3.5 rtl:rotate-180" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                    stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                </svg>
                <span class="text-gray-600">{{ __('front.breadcrumb.ready_cakes') }}</span>
            </div>
            <h1 class="font-display text-2xl md:text-3xl font-bold text-gray-900">{{ __('front.shop.our_ready_cakes') }}
            </h1>
            <p class="text-gray-500 text-sm mt-1">{{ __('front.shop.shop_subtitle') }}</p>
        </div>
    </section>

    <div class="front-container py-8 md:py-10 pb-12 md:pb-20" x-data="{ filtersOpen: false }">

        {{-- ═══ Horizontal Filter Bar ═══ --}}
        <div class="mb-8 space-y-4">

            {{-- Row 1: Search + Sort --}}
            <div class="flex flex-col sm:flex-row gap-3">
                {{-- Search --}}
                <div class="relative flex-1">
                    <svg class="absolute start-4 top-1/2 -translate-y-1/2 w-4.5 h-4.5 text-gray-400 pointer-events-none"
                        fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                    </svg>
                    <input type="text" wire:model.live.debounce.300ms="search"
                        placeholder="{{ __('front.shop.find_a_cake') }}"
                        class="w-full ps-11 pe-10 py-3 rounded-xl border border-gray-200 bg-white text-sm text-gray-900 placeholder:text-gray-400 focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/10 transition-all duration-200">
                    @if($search)
                        <button wire:click="$set('search', '')" aria-label="Clear search"
                            class="absolute end-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition-colors cursor-pointer">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                            </svg>
                        </button>
                    @endif
                </div>

                {{-- Sort Dropdown --}}
                <div class="relative shrink-0">
                    <select wire:model.live="sort"
                        class="w-full sm:w-auto ps-4 pe-10 py-3 rounded-xl border border-gray-200 bg-white text-sm font-medium text-gray-700 focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/10 transition-all duration-200 cursor-pointer appearance-none bg-no-repeat bg-[url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20width%3D%2220%22%20height%3D%2220%22%20viewBox%3D%220%200%2020%2020%20%22%20fill%3D%22none%22%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%3E%3Cpath%20d%3D%22M5%207.5L10%2012.5L15%207.5%22%20stroke%3D%22%239CA3AF%22%20stroke-width%3D%221.5%22%20stroke-linecap%3D%22round%22%20stroke-linejoin%3D%22round%22%2F%3E%3C%2Fsvg%3E')] bg-[position:right_0.75rem_center] rtl:bg-[position:left_0.75rem_center]">
                        <option value="newest">{{ __('front.shop.newest_first') }}</option>
                        <option value="price_asc">{{ __('front.shop.price_low_high') }}</option>
                        <option value="price_desc">{{ __('front.shop.price_high_low') }}</option>
                    </select>
                </div>
            </div>

            {{-- Row 2: Shape Filter Pills --}}
            {{-- Mobile: Dropdown --}}
            <div class="md:hidden">
                <select wire:model.live="shapeFilter"
                    class="w-full ps-4 pe-10 py-3 rounded-xl border border-gray-200 bg-white text-sm font-medium text-gray-700 focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/10 transition-all duration-200 cursor-pointer appearance-none bg-no-repeat bg-[url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20width%3D%2220%22%20height%3D%2220%22%20viewBox%3D%220%200%2020%2020%20%22%20fill%3D%22none%22%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%3E%3Cpath%20d%3D%22M5%207.5L10%2012.5L15%207.5%22%20stroke%3D%22%239CA3AF%22%20stroke-width%3D%221.5%22%20stroke-linecap%3D%22round%22%20stroke-linejoin%3D%22round%22%2F%3E%3C%2Fsvg%3E')] bg-[position:right_0.75rem_center] rtl:bg-[position:left_0.75rem_center]">
                    <option value="">{{ __('front.shop.all_shapes') }}</option>
                    @foreach($shapes as $shape)
                        <option value="{{ $shape->id }}">{{ $shape->name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Desktop: Flex wrap --}}
            <div class="hidden md:flex items-center gap-2 flex-wrap">
                <button wire:click="filterByShape(null)" @class([
                    'px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 cursor-pointer border',
                    'bg-primary text-white border-primary' => !$shapeFilter,
                    'bg-white text-gray-600 border-gray-200 hover:border-primary/30 hover:text-primary' => $shapeFilter,
                ])>
                    {{ __('front.shop.all') }}
                </button>
                @foreach($shapes as $shape)
                    <button wire:click="filterByShape({{ $shape->id }})" wire:key="shape-filter-{{ $shape->id }}" @class([
                        'px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 cursor-pointer border',
                        'bg-primary text-white border-primary' => $shapeFilter === $shape->id,
                        'bg-white text-gray-600 border-gray-200 hover:border-primary/30 hover:text-primary' => $shapeFilter !== $shape->id,
                    ])>
                        {{ $shape->name }}
                    </button>
                @endforeach
            </div>

            {{-- Results Count --}}
            <div wire:loading.remove wire:target="search, shapeFilter, sort, filterByShape"
                class="flex items-center justify-between">
                <p class="text-sm text-gray-400">
                    {{ trans_choice('front.shop.showing_cakes', $cakes->total(), ['count' => $cakes->total()]) }}
                </p>
                @if($search || $shapeFilter)
                    <a href="{{ route('front.shop') }}" wire:navigate
                        class="text-xs font-medium text-primary hover:text-primary-hover transition-colors">
                        {{ __('front.shop.clear_all_filters') }}
                    </a>
                @endif
            </div>
        </div>

        {{-- ═══ Loading State ═══ --}}
        <div wire:loading.delay wire:target="search, shapeFilter, sort, filterByShape"
            class="flex justify-center py-16">
            <div class="flex flex-col items-center gap-4 text-gray-400">
                <svg class="animate-spin w-8 h-8 text-primary" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                </svg>
                <span class="text-sm font-medium">{{ __('front.shop.loading') }}</span>
            </div>
        </div>

        {{-- ═══ Cakes Grid ═══ --}}
        <div wire:loading.remove wire:target="search, shapeFilter, sort, filterByShape">
            @if($cakes->count())
                <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-4 md:gap-6">
                    @foreach($cakes as $cake)
                        <x-front.cake-card :cake="$cake" wire="addToCart({{ $cake->id }})"
                            wire:key="shop-cake-{{ $cake->id }}" />
                    @endforeach
                </div>

                {{-- Pagination --}}
                <div class="mt-12 w-full">
                    {{ $cakes->links() }}
                </div>
            @else
                <x-front.empty-state :title="__('front.shop.no_cakes_found')" :message="__('front.shop.no_cakes_message')"
                    :actionLabel="__('front.shop.clear_filters')" actionHref="{{ route('front.shop') }}" />
            @endif
        </div>
    </div>
</div>