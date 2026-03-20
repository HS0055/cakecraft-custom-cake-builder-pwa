<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class GeneralSettings extends Settings
{
    public string $store_name = 'CakeCraft';
    public string $store_email = 'admin@cakecraft.test';
    public string $store_phone = '';
    public string $store_address = '';
    public int $pagination_limit = 15;

    public static function group(): string
    {
        return 'general';
    }
}
