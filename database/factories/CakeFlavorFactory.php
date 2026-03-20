<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class CakeFlavorFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->word(),
            'thumbnail' => 'placeholder.png',
        ];
    }
}
