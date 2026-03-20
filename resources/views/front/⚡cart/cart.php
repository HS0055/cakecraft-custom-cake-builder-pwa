<?php

use App\Models\{CakeShape, CakeColor, ReadyCake, ShapeFlavor, ShapeTopping};
use App\Settings\{OrderSettings, CurrencySettings, FulfillmentSettings};
use App\Livewire\Traits\WithVisualData;
use Livewire\Attributes\{Layout, Title, Computed};

new
    #[Layout('layouts::front')]
    #[Title('Cart')]
    class extends \Livewire\Component {
    use WithVisualData;

    public function getItemsProperty(): array
    {
        return session('cart', []);
    }

    #[Computed]
    public function subtotal(): float
    {
        return collect($this->items)->sum(fn($item) => ($item['price'] ?? 0) * ($item['quantity'] ?? 1));
    }



    #[Computed]
    public function taxPercentage(): float
    {
        $orderSettings = settings(OrderSettings::class);
        return (float) ($orderSettings->tax_percentage ?? 0);
    }

    #[Computed]
    public function taxAmount(): float
    {
        return $this->subtotal * ($this->taxPercentage / 100);
    }

    #[Computed]
    public function total(): float
    {
        return $this->subtotal + $this->taxAmount;
    }

    public function updateQuantity(int $index, int $quantity): void
    {
        $cart = session('cart', []);

        if (isset($cart[$index])) {
            if ($quantity < 1) {
                $this->removeItem($index);
                return;
            }
            $cart[$index]['quantity'] = min($quantity, 99);
            session(['cart' => $cart]);
        }

        $this->dispatch('cart-updated');
    }

    public function removeItem(int $index): void
    {
        $cart = session('cart', []);
        unset($cart[$index]);
        session(['cart' => array_values($cart)]);
        $this->dispatch('cart-updated');
        $this->dispatch('toast', message: __('front.cart_page.deleted_success'), type: 'success');
    }

    public function clearCart(): void
    {
        session()->forget('cart');
        $this->dispatch('cart-updated');
        $this->dispatch('toast', message: __('front.cart_page.cleared_success'), type: 'success');
    }
};
