<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class FulfillmentSettings extends Settings
{
    public bool $enable_pickup = true;
    public bool $enable_delivery = false;
    public int $default_preparation_time = 0; // in minutes

    public array $types = [
        'pickup' => 'Pickup',
        'delivery' => 'Delivery',
    ];

    public static function group(): string
    {
        return 'fulfillment';
    }
}
