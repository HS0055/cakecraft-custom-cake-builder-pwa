<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class SocialMediaSettings extends Settings
{
    public ?string $facebook_url = null;
    public ?string $instagram_url = null;
    public ?string $twitter_url = null;
    public ?string $tiktok_url = null;
    public ?string $whatsapp_number = null;

    public static function group(): string
    {
        return 'social_media';
    }
}
