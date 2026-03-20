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
<button @click="$store.cartDrawer.show()" aria-label="{{ __('front.cart_drawer.shopping_cart') }}"
    class="relative p-2.5 rounded-lg hover:bg-primary/5 transition-colors duration-150 group">
    <svg class="w-5 h-5 text-gray-600 group-hover:text-gray-900 transition-colors" fill="none" viewBox="0 0 24 24"
        stroke="currentColor" stroke-width="1.5">
        <path stroke-linecap="round" stroke-linejoin="round"
            d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007ZM8.625 10.5a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm7.5 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
    </svg>
    @if($cartCount > 0)
        <span
            class="absolute -top-0.5 -end-0.5 flex h-5 w-5 items-center justify-center rounded-full bg-primary text-[10px] font-bold text-white ring-2 ring-white animate-scale-in">
            {{ $cartCount }}
        </span>
    @endif
</button>