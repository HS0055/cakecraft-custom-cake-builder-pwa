<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class OrderItemFactory extends Factory
{
    public function definition(): array
    {
        return [
            'final_price' => 100,
            'quantity' => 1,
            'base_price' => 100,
            'extra_price' => 0,
            'topping_price' => 0,
        ];
    }
}
