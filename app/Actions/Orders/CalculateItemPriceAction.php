<?php

namespace App\Actions\Orders;

use App\Models\CakeShape;
use App\Models\ReadyCake;
use Illuminate\Validation\ValidationException;

class CalculateItemPriceAction
{
    /**
     * Calculate the base, extra, topping, and final prices for a cart item.
     * Optionally validates the required UI fields.
     *
     * @param array $itemData
     * @param bool $validate
     * @return array{base_price: float, extra_price: float, topping_price: float, final_price: float}
     * @throws ValidationException
     */
    public function execute(array $itemData, bool $validate = false): array
    {
        if ($validate) {
            $this->validateItem($itemData);
        }

        $base_price = 0.0;
        $extra_price = 0.0;
        $topping_price = 0.0;
        $final_price = 0.0;

        if (($itemData['type'] ?? 'ready') === 'ready') {
            if (!empty($itemData['ready_cake_id'])) {
                $cake = ReadyCake::find($itemData['ready_cake_id']);
                $final_price = $cake ? (float) $cake->price : 0.0;
            }
        } else {
            if (!empty($itemData['cake_shape_id'])) {
                $shape = CakeShape::find($itemData['cake_shape_id']);
                if ($shape) {
                    $base_price = (float) $shape->base_price;

                    if (!empty($itemData['cake_flavor_id'])) {
                        $flavor = $shape->flavors()->where('cake_flavor_id', $itemData['cake_flavor_id'])->first();
                        if ($flavor) {
                            $extra_price = (float) $flavor->pivot->extra_price;
                        }
                    }

                    if (!empty($itemData['cake_topping_id'])) {
                        $topping = $shape->toppings()->where('cake_topping_id', $itemData['cake_topping_id'])->first();
                        if ($topping) {
                            $topping_price = (float) $topping->pivot->price;
                        }
                    }
                }
            }
            $final_price = $base_price + $extra_price + $topping_price;
        }

        return [
            'base_price' => $base_price,
            'extra_price' => $extra_price,
            'topping_price' => $topping_price,
            'final_price' => $final_price,
        ];
    }

    /**
     * Form validations abstracting identical checks away from the Livewire UI layer.
     *
     * @param array $itemData
     * @throws ValidationException
     */
    protected function validateItem(array $itemData): void
    {
        if (($itemData['type'] ?? 'ready') === 'ready' && empty($itemData['ready_cake_id'])) {
            throw ValidationException::withMessages(['tempItem.ready_cake_id' => 'Select a ready cake']);
        }

        if (($itemData['type'] ?? 'ready') === 'custom') {
            if (empty($itemData['cake_shape_id'])) {
                throw ValidationException::withMessages(['tempItem.cake_shape_id' => 'Select a shape']);
            }
            if (empty($itemData['cake_flavor_id'])) {
                throw ValidationException::withMessages(['tempItem.cake_flavor_id' => 'Select a flavor']);
            }
            if (empty($itemData['cake_color_id'])) {
                throw ValidationException::withMessages(['tempItem.cake_color_id' => 'Select a color']);
            }
        }

        if (($itemData['quantity'] ?? 0) < 1) {
            throw ValidationException::withMessages(['tempItem.quantity' => 'Quantity must be at least 1']);
        }
    }
}
