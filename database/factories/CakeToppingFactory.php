<?php

namespace Database\Factories;

use App\Models\ToppingCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CakeTopping>
 */
class CakeToppingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->word(),
            'topping_category_id' => ToppingCategory::factory(),
        ];
    }
}
