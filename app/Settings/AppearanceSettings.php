<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class AppearanceSettings extends Settings
{
    public ?string $primary_color = '#ff70a2';
    public string $admin_sidebar_color = '#2D2D2D';

    public static function group(): string
    {
        return 'appearance';
    }
}
