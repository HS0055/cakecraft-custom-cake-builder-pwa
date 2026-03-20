<?php

use App\Models\ReadyCake;
use App\Models\ShapeTopping;
use App\Livewire\Traits\WithCartActions;
use Livewire\Attributes\{Layout, Title, Computed};

new
    #[Layout('layouts::front')]
    class extends \Livewire\Component {
    use WithCartActions;

    public ReadyCake $readyCake;
    public int $quantity = 1;

    public function incrementQuantity(): void
    {
        $this->quantity++;
    }

    public function decrementQuantity(): void
    {
        if ($this->quantity > 1) {
            $this->quantity--;
        }
    }

    public function mount(ReadyCake $readyCake)
    {
        $this->readyCake = $readyCake->load(['cakeShape.media', 'cakeFlavor.media', 'cakeColor', 'cakeTopping.media']);

        if (!$this->readyCake->is_active) {
            abort(404);
        }
    }

    public function title(): string
    {
        return $this->readyCake->name . ' — Cake Details';
    }

    #[Computed]
    public function shapeToppingLayers()
    {
        return $this->readyCake->cake_topping_id
            ? ShapeTopping::with('media')
                ->where('cake_shape_id', $this->readyCake->cake_shape_id)
                ->where('cake_topping_id', $this->readyCake->cake_topping_id)
                ->get()
            : collect();
    }

    public function addToCart(): void
    {
        $cart = session('cart', []);

        // Check if item already exists in cart
        $existingIndex = null;
        foreach ($cart as $index => $item) {
            if (isset($item['ready_cake_id']) && $item['ready_cake_id'] === $this->readyCake->id) {
                $existingIndex = $index;
                break;
            }
        }

        if ($existingIndex !== null) {
            $cart[$existingIndex]['quantity'] += $this->quantity;
        } else {
            $cart[] = [
                'type' => 'ready',
                'ready_cake_id' => $this->readyCake->id,
                'name' => $this->readyCake->name,
                'price' => (float) $this->readyCake->price,
                'quantity' => $this->quantity,
                'image' => $this->readyCake->getFirstMediaUrl('preview')
                    ?: $this->readyCake->cakeShape?->getFirstMediaUrl('thumbnail'),
                'details' => [
                    'shape' => $this->readyCake->cakeShape?->name,
                    'flavor' => $this->readyCake->cakeFlavor?->name,
                ]
            ];
        }

        session(['cart' => $cart]);

        $this->redirect(route('front.cart'), navigate: true);
    }
};
