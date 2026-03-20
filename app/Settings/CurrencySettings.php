<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class CurrencySettings extends Settings
{
    public string $currency_code = 'USD';
    public string $currency_symbol = '$';

    public static function group(): string
    {
        return 'currency';
    }
}
