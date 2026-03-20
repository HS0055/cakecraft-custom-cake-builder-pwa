<?php

namespace App\Livewire\Traits;

use App\Models\ReadyCake;

trait WithCartActions
{
    public function addToCart(int $readyCakeId): void
    {
        try {
            $cake = ReadyCake::with(['cakeShape', 'cakeFlavor', 'cakeColor'])->findOrFail($readyCakeId);

            $cart = session('cart', []);

            $foundIndex = null;
            foreach ($cart as $index => $item) {
                if (($item['type'] ?? '') === 'ready' && ($item['ready_cake_id'] ?? null) === $cake->id) {
                    $foundIndex = $index;
                    break;
                }
            }

            if ($foundIndex !== null) {
                $cart[$foundIndex]['quantity'] += 1;
            } else {
                $cart[] = [
                    'type' => 'ready',
                    'ready_cake_id' => $cake->id,
                    'name' => $cake->name,
                    'price' => (float) $cake->price,
                    'quantity' => 1,
                    'details' => [
                        'shape' => $cake->cakeShape?->name,
                        'flavor' => $cake->cakeFlavor?->name,
                        'color' => $cake->cakeColor?->name,
                    ],
                    'image' => $cake->getFirstMediaUrl('preview'),
                    'cake_shape_id' => $cake->cake_shape_id,
                    'cake_topping_id' => $cake->cake_topping_id,
                ];
            }

            session(['cart' => $cart]);

            $this->dispatch('cart-updated');
            $this->dispatch('toast', message: __('front.cart.added_success'), type: 'success');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException) {
            $this->dispatch('toast', message: __('front.cart.no_longer_available'), type: 'error');
        } catch (\Throwable $e) {
            report($e);
            $this->dispatch('toast', message: __('front.cart.error_generic'), type: 'error');
        }
    }
}
