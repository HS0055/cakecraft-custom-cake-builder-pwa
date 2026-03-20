<?php

namespace Database\Factories;

use App\Models\CakeColor;
use App\Models\CakeFlavor;
use App\Models\CakeShape;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReadyCakeFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->words(3, true),
            'cake_shape_id' => CakeShape::factory(),
            'cake_flavor_id' => CakeFlavor::factory(),
            'cake_color_id' => CakeColor::factory(),
            'price' => $this->faker->randomFloat(2, 10, 100),
            'is_active' => true,
            'is_customizable' => true,
        ];
    }
}
