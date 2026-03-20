<?php

if (!function_exists('settings')) {
    /**
     * Get a setting value or the settings object.
     *
     * @template T of \Spatie\LaravelSettings\Settings
     * @param class-string<T> $class
     * @return T
     */
    function settings(string $class)
    {
        return app($class);
    }
}
