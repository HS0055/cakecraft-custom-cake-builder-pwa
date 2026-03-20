<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class SystemSettings extends Settings
{
    public bool $maintenance_mode = false;

    public static function group(): string
    {
        return 'system';
    }
}
