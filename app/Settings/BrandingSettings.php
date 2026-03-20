<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class BrandingSettings extends Settings
{
    public ?string $logo_url = null;
    public ?string $favicon_url = null;

    public static function group(): string
    {
        return 'branding';
    }
}
