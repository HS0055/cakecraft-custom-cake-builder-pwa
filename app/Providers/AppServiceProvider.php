<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\URL;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (app()->environment('production')) {
            URL::forceScheme('https');
        }
        \Illuminate\Database\Eloquent\Model::preventLazyLoading(!app()->isProduction());

        // Share active languages with the frontend header component
        \Illuminate\Support\Facades\View::composer('components.front.header.⚡header', function ($view) {
            $languages = Cache::rememberForever('active_languages', fn() => \App\Models\Language::where('is_active', true)->get());
            $view->with('activeLanguages', $languages);
        });
    }
}
