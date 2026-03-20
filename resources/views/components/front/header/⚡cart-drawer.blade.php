<?php

use Livewire\Component;
use Livewire\Attributes\On;
use App\Livewire\Traits\WithVisualData;

new class extends Component {
    use WithVisualData;

    public array $cart = [];

    public function mount()
    {
        $this->cart = session('cart', []);
    }

    #[On('cart-updated')]
    public function refreshCart()
    {
        $this->cart = session('cart', []);
    }

    public function removeFromCart($index)
    {
        $cart = session('cart', []);
        if (isset($cart[$index])) {
            unset($cart[$index]);
            $cart = array_values($cart);
            session(['cart' => $cart]);
            $this->cart = $cart;
            $this->dispatch('cart-updated');
            $this->dispatch('toast', message: __('front.cart_page.deleted_success'), type: 'success');
        }
    }

    public function getSubtotalProperty()
    {
        return collect($this->cart)->sum(fn($item) => $item['price'] * $item['quantity']);
    }

    public function with(): array
    {
        return [
            'cartCount' => count($this->cart),
            'subtotal' => $this->subtotal,
        ];
    }
};
?>
<div x-data="{
        get isOpen() { return $store.cartDrawer?.open ?? false },
        close() { $store.cartDrawer.hide() }
     }" x-show="isOpen" x-cloak class="relative z-[999]">

    {{-- Backdrop --}}
    <div x-show="isOpen" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" @click="close()"
        class="fixed inset-0 bg-black/20 backdrop-blur-sm">
    </div>

    {{-- Drawer Panel --}}
    <div class="fixed inset-y-0 right-0 w-full max-w-sm flex">
        <div x-show="isOpen" x-trap.noscroll="isOpen" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0"
            x-transition:leave="transition ease-in duration-200" x-transition:leave-start="translate-x-0"
            x-transition:leave-end="translate-x-full" class="w-full bg-white border-l border-gray-200 flex flex-col">

            {{-- Header --}}
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">{{ __('front.cart_drawer.your_cart') }}</h2>
                <button @click="close()"
                    class="p-2 -me-2 text-gray-400 hover:text-gray-600 transition-colors rounded-lg hover:bg-primary/5">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            {{-- Body --}}
            <div class="flex-1 overflow-y-auto p-4 bg-gray-50/50">
                @if($cartCount > 0)
                    <div class="space-y-3">
                        @foreach($cart as $index => $item)
                            <div class="flex gap-3 bg-white border border-gray-200 rounded-xl p-3">
                                <div class="w-18 h-18 rounded-lg border border-gray-200 overflow-hidden bg-gray-50 shrink-0">
                                    @php $visual = $this->getVisualDataForItem($item); @endphp
                                    @if($visual && $visual['shape'])
                                        <div class="w-full h-full flex items-center justify-center p-1">
                                            <x-cake-visual class="w-full h-full object-contain" :shape="$visual['shape']" :color="$visual['color']"
                                                :flavorLayer="$visual['flavorLayer']" :toppingLayers="$visual['toppingLayers']"
                                                mode="final" />
                                        </div>
                                    @elseif(!empty($item['image']))
                                        <img src="{{ $item['image'] }}" alt="{{ $item['name'] }}" class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center text-gray-200">
                                            <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                                            </svg>
                                        </div>
                                    @endif
                                </div>
                                <div class="flex-1 flex flex-col justify-center min-w-0">
                                    <h3 class="font-medium text-gray-900 text-sm truncate">{{ $item['name'] }}</h3>
                                    @if(isset($item['details']) && !empty($item['details']))
                                        <p class="text-xs text-gray-400 mt-0.5 truncate">
                                            {{ collect($item['details'])->filter()->implode(', ') }}
                                        </p>
                                    @endif
                                    <div class="flex items-center justify-between mt-2">
                                        <x-front.price :amount="$item['price']" class="text-gray-900 font-semibold text-sm" />
                                        <div class="flex items-center gap-3">
                                            <span class="text-xs font-medium text-gray-400">{{ __('front.cart_drawer.qty') }}:
                                                {{ $item['quantity'] }}</span>
                                            <div x-data="{ showDrawerConfirmModal: false }">
                                                <button type="button" @click="showDrawerConfirmModal = true" aria-label="{{ __('front.cart_drawer.remove_item') }}"
                                                    class="p-1 text-gray-300 hover:text-red-500 transition-colors rounded-md hover:bg-red-50">
                                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                                    </svg>
                                                </button>

                                                {{-- Drawer Item Confirm Modal --}}
                                                <div x-show="showDrawerConfirmModal" x-cloak class="relative z-[1000]">
                                                    <div x-show="showDrawerConfirmModal" x-transition.opacity duration.300ms
                                                        class="fixed inset-0 bg-black/40 backdrop-blur-[2px]"></div>

                                                    <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                                                        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                                                            <div x-show="showDrawerConfirmModal" 
                                                                x-transition:enter="ease-out duration-300"
                                                                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                                                                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                                                                x-transition:leave="ease-in duration-200"
                                                                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                                                                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                                                                @click.away="showDrawerConfirmModal = false"
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
                                                                        <button type="button" @click="$wire.removeFromCart({{ $index }}); showDrawerConfirmModal = false"
                                                                            class="flex-1 inline-flex justify-center items-center rounded-xl bg-red-600 px-4 py-3 text-sm font-semibold text-white hover:bg-red-500 transition-all focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
                                                                            {{ __('front.cart_page.delete_confirm_btn') }}
                                                                        </button>
                                                                        <button type="button" @click="showDrawerConfirmModal = false"
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
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="h-full flex flex-col items-center justify-center text-center space-y-4 px-4">
                        <div class="w-20 h-20 rounded-full bg-primary/10 flex items-center justify-center text-primary">
                            <svg class="w-9 h-9" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007ZM8.625 10.5a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm7.5 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-base font-semibold text-gray-900">{{ __('front.cart_drawer.cart_empty') }}</p>
                            <p class="text-sm text-gray-400 mt-1">{{ __('front.cart_drawer.cart_empty_hint') }}</p>
                        </div>
                        <div class="pt-1 w-full max-w-[200px]">
                            <x-front.btn @click="close()" href="{{ route('front.shop') }}" variant="primary" class="w-full text-sm" wire:navigate>
                                {{ __('front.cart_drawer.keep_shopping') }}
                            </x-front.btn>
                        </div>
                    </div>
                @endif
            </div>

            {{-- Footer --}}
            @if($cartCount > 0)
                <div class="px-5 py-4 border-t border-gray-200 bg-white">
                    <div class="flex items-center justify-between mb-4">
                        <span class="text-sm text-gray-500">{{ __('front.cart_drawer.subtotal') }}</span>
                        <x-front.price :amount="$subtotal" class="font-semibold text-lg text-gray-900" />
                    </div>
                    <div class="space-y-2.5">
                        <x-front.btn href="{{ route('front.checkout') }}" variant="primary" class="w-full text-sm" wire:navigate>
                            {{ __('front.cart_drawer.checkout') }}
                        </x-front.btn>

                        <x-front.btn href="{{ route('front.cart') }}" variant="secondary" class="w-full text-sm" wire:navigate>
                            {{ __('front.cart_drawer.view_cart_page') }}
                        </x-front.btn>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>