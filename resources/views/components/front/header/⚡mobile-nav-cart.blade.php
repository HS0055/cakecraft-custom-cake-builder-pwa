<?php

use Livewire\Component;
use Livewire\Attributes\On;

new class extends Component {
    public int $cartCount = 0;

    public function mount()
    {
        $this->cartCount = count(session('cart', []));
    }

    #[On('cart-updated')]
    public function refreshCartCount()
    {
        $this->cartCount = count(session('cart', []));
    }
};
?>
<button @click="$store.cartDrawer.show()"
    class="flex flex-col items-center justify-center w-16 h-12 group relative rounded-xl transition-colors duration-150 hover:bg-primary/5">
    <div class="text-gray-500 group-hover:text-gray-900 transition-colors duration-150 relative">
        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round"
                d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007ZM8.625 10.5a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm7.5 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
        </svg>
        @if($cartCount > 0)
            <span
                class="absolute -top-1 -end-1 flex h-[14px] min-w-[14px] items-center justify-center rounded-full bg-primary px-1 text-[8px] font-bold text-white ring-2 ring-white animate-scale-in">
                {{ $cartCount }}
            </span>
        @endif
    </div>
    <span
        class="text-[9px] font-semibold mt-0.5 text-gray-500 transition-colors">{{ __('front.cart_drawer.cart') }}</span>
</button>