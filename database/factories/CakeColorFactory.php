<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class CakeColorFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->colorName(),
            'hex_code' => $this->faker->hexcolor(),
        ];
    }
}
