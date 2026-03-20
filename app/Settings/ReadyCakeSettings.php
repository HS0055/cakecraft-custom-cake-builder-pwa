<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class ReadyCakeSettings extends Settings
{
    public bool $default_is_active = true;
    public bool $default_is_customizable = true;

    public static function group(): string
    {
        return 'ready_cake';
    }
}
