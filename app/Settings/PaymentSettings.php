<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class PaymentSettings extends Settings
{
    public bool $enable_stripe = false;
    public bool $enable_paypal = false;
    public bool $enable_cash = true;

    public array $methods = [
        'cash' => 'Cash',
        'card' => 'Card',
        'online' => 'Online',
    ];

    public ?string $stripe_public_key = null;
    public ?string $stripe_secret_key = null;
    public ?string $stripe_webhook_secret = null;

    public ?string $paypal_client_id = null;
    public ?string $paypal_secret = null;
    public string $paypal_mode = 'sandbox'; // sandbox | live

    public static function group(): string
    {
        return 'payment';
    }
}
