<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class CakeShapeFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->word(),
            'base_price' => $this->faker->randomFloat(2, 5, 20),
            'thumbnail' => 'placeholder.png',
            'base_image' => 'placeholder.png',
            'base_cut_image' => 'placeholder.png',
        ];
    }
}
