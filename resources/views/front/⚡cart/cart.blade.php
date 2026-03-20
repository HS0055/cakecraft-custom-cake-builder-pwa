<div>
    {{-- Header --}}
    <section class="border-b border-gray-200">
        <div class="front-container py-6 md:py-8">
            <div class="flex items-center gap-2 text-sm text-gray-400 mb-2">
                <a href="{{ route('front.home') }}" wire:navigate class="hover:text-primary transition-colors">{{ __('front.breadcrumb.home') }}</a>
                <svg class="w-3.5 h-3.5 rtl:rotate-180" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5"/></svg>
                <span class="text-gray-600">{{ __('front.breadcrumb.cart') }}</span>
            </div>
            <h1 class="font-display text-2xl md:text-3xl font-bold text-gray-900">{{ __('front.cart_page.your_cart') }}</h1>
        </div>
    </section>

    <div class="front-container py-8 md:py-12">
        @if(count($this->items))
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                {{-- Cart Items --}}
                <div class="lg:col-span-2 space-y-4">
                        <div x-data="{ showConfirmModal: false }">
                            <button type="button" @click="showConfirmModal = true"
                                class="text-xs text-gray-400 hover:text-red-500 transition-colors cursor-pointer">
                                {{ __('front.cart_page.clear_all') }}
                            </button>

                            {{-- Custom Confirm Modal --}}
                            <div x-show="showConfirmModal" x-cloak class="relative z-50">
                                {{-- Backdrop --}}
                                <div x-show="showConfirmModal" x-transition.opacity duration.300ms
                                    class="fixed inset-0 bg-black/40 backdrop-blur-[2px]"></div>

                                {{-- Dialog --}}
                                <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                                    <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                                        <div x-show="showConfirmModal" 
                                            x-transition:enter="ease-out duration-300"
                                            x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                                            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                                            x-transition:leave="ease-in duration-200"
                                            x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                                            x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                                            @click.away="showConfirmModal = false"
                                            class="relative transform overflow-hidden rounded-2xl bg-white shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-sm border border-gray-100">
                                            
                                            <div class="p-6 md:p-8 text-center">
                                                <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-red-50 mb-5">
                                                    <svg class="h-8 w-8 text-red-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                                    </svg>
                                                </div>
                                                <h3 class="text-xl font-bold text-gray-900 mb-2">
                                                    {{ __('front.cart_page.clear_all') }}
                                                </h3>
                                                <p class="text-sm text-gray-500 mb-6">
                                                    {{ __('front.cart_page.clear_confirm') }}
                                                </p>
                                                <div class="flex flex-col sm:flex-row gap-3">
                                                    <button type="button" @click="$wire.clearCart(); showConfirmModal = false"
                                                        class="flex-1 inline-flex justify-center items-center rounded-xl bg-red-600 px-4 py-3 text-sm font-semibold text-white hover:bg-red-500 transition-all focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
                                                        {{ __('front.cart_page.clear_confirm_btn') }}
                                                    </button>
                                                    <button type="button" @click="showConfirmModal = false"
                                                        class="flex-1 inline-flex justify-center items-center rounded-xl bg-white px-4 py-3 text-sm font-semibold text-gray-700 ring-1 ring-inset ring-gray-200 hover:bg-gray-50 transition-all focus:outline-none">
                                                        {{ __('front.cart_page.clear_cancel') }}
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    @foreach($this->items as $index => $item)
                        <div wire:key="cart-item-{{ $index }}"
                            class="bg-white rounded-xl border border-gray-200 overflow-hidden flex flex-row items-stretch animate-fade-in relative transition-all duration-200 hover:border-primary/30">
                            
                            {{-- Image --}}
                            <div class="relative w-[35%] md:w-32 bg-gray-50 flex-shrink-0">
                                @php $visual = $this->getVisualDataForItem($item); @endphp
                                @if($visual && $visual['shape'])
                                    <div class="absolute inset-0 p-2 sm:p-0">
                                        <x-cake-visual class="w-full h-full object-contain" :shape="$visual['shape']" :color="$visual['color']"
                                            :flavorLayer="$visual['flavorLayer']" :toppingLayers="$visual['toppingLayers']"
                                            mode="final" />
                                    </div>
                                @elseif(!empty($item['image']))
                                    <img src="{{ $item['image'] }}" alt="{{ $item['name'] }}" class="absolute inset-0 w-full h-full object-cover">
                                @else
                                    <div class="absolute inset-0 flex items-center justify-center">
                                        @if($item['type'] === 'custom')
                                            <svg class="w-8 h-8 md:w-10 md:h-10 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M9.53 16.122a3 3 0 0 0-5.78 1.128 2.25 2.25 0 0 1-2.4 2.245 4.5 4.5 0 0 0 8.4-2.245c0-.399-.078-.78-.22-1.128Zm0 0a15.998 15.998 0 0 0 3.388-1.62m-5.043-.025a15.994 15.994 0 0 1 1.622-3.395m3.42 3.42a15.995 15.995 0 0 0 4.764-4.648l3.876-5.814a1.151 1.151 0 0 0-1.597-1.597L14.146 6.32a15.996 15.996 0 0 0-4.649 4.763m3.42 3.42a6.776 6.776 0 0 0-3.42-3.42" />
                                            </svg>
                                        @else
                                            <svg class="w-8 h-8 md:w-10 md:h-10 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 7.5l-2.25-1.313M21 7.5v2.25m0-2.25l-2.25 1.313M3 7.5l2.25-1.313M3 7.5l2.25 1.313M3 7.5v2.25m9 3l2.25-1.313M12 12.75l-2.25-1.313M12 12.75V15m0 6.75l2.25-1.313M12 21.75V19.5m0 2.25l-2.25-1.313m0-16.875L12 2.25l2.25 1.313M21 14.25v2.25l-2.25 1.313m-13.5 0L3 16.5v-2.25" />
                                            </svg>
                                        @endif
                                    </div>
                                @endif
                            </div>

                            {{-- Details --}}
                            <div class="flex-1 p-3 md:p-5 flex flex-col justify-center min-w-0">
                                <div class="flex items-start justify-between gap-2 mb-1 md:mb-2">
                                    <div class="flex items-center gap-2 flex-nowrap min-w-0 overflow-hidden">
                                        <x-front.badge class="shrink-0" :label="$item['type'] === 'custom' ? __('front.cart_page.custom') : __('front.cart_page.ready')"
                                            :color="$item['type'] === 'custom' ? 'blue' : 'peach'" />
                                        @if(!empty($item['details']))
                                            <span class="text-xs text-gray-400 truncate min-w-0">
                                                {{ collect([
                                                    $item['details']['shape'] ?? null,
                                                    $item['details']['flavor'] ?? null,
                                                    $item['details']['color'] ?? null,
                                                    $item['details']['topping'] ?? null,
                                                ])->filter()->values()->implode(' · ') }}
                                            </span>
                                        @endif
                                    </div>
                                    
                                    <div x-data="{ showItemConfirmModal: false }">
                                        <button type="button" @click="showItemConfirmModal = true"
                                            aria-label="Remove {{ $item['name'] }} from cart"
                                            class="p-1.5 rounded-lg text-gray-400 hover:text-red-500 hover:bg-red-50 transition-colors cursor-pointer shrink-0">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>

                                        {{-- Item Confirm Modal --}}
                                        <div x-show="showItemConfirmModal" x-cloak class="relative z-50">
                                            <div x-show="showItemConfirmModal" x-transition.opacity duration.300ms
                                                class="fixed inset-0 bg-black/40 backdrop-blur-[2px]"></div>

                                            <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                                                <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                                                    <div x-show="showItemConfirmModal" 
                                                        x-transition:enter="ease-out duration-300"
                                                        x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                                                        x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                                                        x-transition:leave="ease-in duration-200"
                                                        x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                                                        x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                                                        @click.away="showItemConfirmModal = false"
                                                        class="relative transform overflow-hidden rounded-2xl bg-white shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-sm border border-gray-100">
                                                        
                                                        <div class="p-6 md:p-8 text-center">
                                                            <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-red-50 mb-5">
                                                                <svg class="h-8 w-8 text-red-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                                                </svg>
                                                            </div>
                                                            <h3 class="text-xl font-bold text-gray-900 mb-2 line-clamp-2">
                                                                {{ $item['name'] }}
                                                            </h3>
                                                            <p class="text-sm text-gray-500 mb-6">
                                                                {{ __('front.cart_page.delete_item_confirm') }}
                                                            </p>
                                                            <div class="flex flex-col sm:flex-row gap-3">
                                                                <button type="button" @click="$wire.removeItem({{ $index }}); showItemConfirmModal = false"
                                                                    class="flex-1 inline-flex justify-center items-center rounded-xl bg-red-600 px-4 py-3 text-sm font-semibold text-white hover:bg-red-500 transition-all focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
                                                                    {{ __('front.cart_page.delete_confirm_btn') }}
                                                                </button>
                                                                <button type="button" @click="showItemConfirmModal = false"
                                                                    class="flex-1 inline-flex justify-center items-center rounded-xl bg-white px-4 py-3 text-sm font-semibold text-gray-700 ring-1 ring-inset ring-gray-200 hover:bg-gray-50 transition-all focus:outline-none">
                                                                    {{ __('front.cart_page.clear_cancel') }}
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <h3 class="font-semibold text-sm md:text-lg text-gray-900 mb-3 md:pe-0 line-clamp-1">
                                    {{ $item['name'] }}
                                </h3>

                                <div class="flex items-center justify-between gap-2 mt-auto">
                                    <x-front.quantity-control :value="$item['quantity']"
                                        wireIncrement="updateQuantity({{ $index }}, {{ $item['quantity'] + 1 }})"
                                        wireDecrement="updateQuantity({{ $index }}, {{ $item['quantity'] - 1 }})" />
                                        
                                    <x-front.price :amount="$item['price'] * $item['quantity']" class="text-base md:text-lg text-primary font-bold" />
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Order Summary --}}
                <div>
                    <div class="rounded-xl border border-gray-200 p-6 sticky top-24">
                        <h3 class="text-lg font-semibold text-gray-900 mb-5">{{ __('front.cart_page.order_summary') }}</h3>

                        <div class="space-y-3 mb-5">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-400">{{ __('front.cart_page.subtotal') }}</span>
                                <x-front.price :amount="$this->subtotal" class="text-gray-900" />
                            </div>

                            @if($this->taxPercentage > 0)
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-400">{{ __('front.cart_page.tax', ['percentage' => $this->taxPercentage]) }}</span>
                                    <x-front.price :amount="$this->taxAmount" class="text-gray-900" />
                                </div>
                            @endif
                        </div>

                        <div class="pt-4 border-t border-gray-200">
                            <div class="flex justify-between items-center">
                                <span class="font-semibold text-gray-900">{{ __('front.cart_page.total') }}</span>
                                <x-front.price :amount="$this->total" class="text-2xl text-primary" />
                            </div>
                        </div>

                        <div class="mt-6">
                            <x-front.btn href="{{ route('front.checkout') }}" variant="primary" size="lg" class="w-full"
                                wire:navigate>
                                {{ __('front.cart_page.proceed_to_checkout') }}
                                <svg class="w-4 h-4 rtl:rotate-180" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                                </svg>
                            </x-front.btn>
                        </div>

                        <a href="{{ route('front.shop') }}" wire:navigate
                            class="block text-center text-sm text-gray-400 hover:text-primary transition-colors mt-4">
                            {{ __('front.cart_page.continue_shopping') }}
                        </a>
                    </div>
                </div>
            </div>
        @else
            <x-front.empty-state :title="__('front.cart_page.empty_title')"
                :message="__('front.cart_page.empty_message')"
                :actionLabel="__('front.cart_page.start_shopping')" actionHref="{{ route('front.shop') }}" />
        @endif
    </div>
</div>