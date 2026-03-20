<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class OrderSettings extends Settings
{
    public array $sources = [
        'call_center' => 'Call Center',
        'web' => 'Web',
        'mobile' => 'Mobile',
    ];

    public array $statuses = [
        'pending' => 'Pending',
        'confirmed' => 'Confirmed',
        'paid' => 'Paid',
        'in_progress' => 'In Progress',
        'completed' => 'Completed',
        'cancelled' => 'Cancelled',
    ];

    public float $tax_percentage = 0.0;
    public float $delivery_fee = 0.0;

    public static function group(): string
    {
        return 'order';
    }
}
