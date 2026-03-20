<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    public function definition(): array
    {
        return [
            'customer_name' => $this->faker->name,
            'customer_phone' => $this->faker->phoneNumber,
            'customer_email' => $this->faker->email,
            'scheduled_at' => now()->addDay(),
            'order_source' => 'web',
            'fulfillment_type' => 'pickup',
            'payment_method' => 'cash',
            'status' => 'pending',
            'total_price' => 100,
        ];
    }
}
