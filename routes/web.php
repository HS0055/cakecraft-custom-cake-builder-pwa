<?php

use App\Models\Language;
use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| Storefront Routes (public)
|--------------------------------------------------------------------------
*/
Route::get('/home', fn() => redirect()->route('front.home'));
Route::livewire('/', 'front::home')->name('front.home');
Route::livewire('/install', 'installer::installer')->name('install');
Route::livewire('/shop', 'front::shop')->name('front.shop');
Route::livewire('/cake-builder', 'front::cake-builder')->name('front.cake-builder');
Route::livewire('/cart', 'front::cart')->name('front.cart');
Route::livewire('/checkout', 'front::checkout')->name('front.checkout');
Route::livewire('/ready-cakes/{readyCake}', 'front::ready-cake-details')->name('front.ready-cake.show');

Route::livewire('/pages/{page}', 'front::pages')->name('front.pages.show');
Route::livewire('/faqs', 'front::faqs')->name('front.faqs');

Route::livewire('/login', 'auth::login')->middleware('guest')->name('login');

/*
|--------------------------------------------------------------------------
| Admin Routes (authenticated)
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->middleware('auth')->group(function () {

    Route::get('/', fn() => redirect()->route('admin.dashboard'));

    Route::livewire('/dashboard', 'admin::dashboard')->name('admin.dashboard');
    Route::livewire('/cake-shapes', 'admin::cake-shapes')->name('admin.cake-shapes');
    Route::livewire('/cake-flavors', 'admin::cake-flavors')->name('admin.cake-flavors');
    Route::livewire('/cake-toppings', 'admin::cake-toppings')->name('admin.cake-toppings');
    Route::livewire('/topping-categories', 'admin::topping-categories')->name('admin.topping-categories');
    Route::livewire('/cake-colors', 'admin::cake-colors')->name('admin.cake-colors');
    Route::livewire('/shape-flavors', 'admin::shape-flavors')->name('admin.shape-flavors');
    Route::livewire('/shape-toppings', 'admin::shape-toppings')->name('admin.shape-toppings');
    Route::livewire('/ready-cakes', 'admin::ready-cakes')->name('admin.ready-cakes');
    Route::livewire('/ready-cakes/create', 'admin::ready-cake-wizard')->name('admin.ready-cakes.create');
    Route::livewire('/ready-cakes/{readyCake}/edit', 'admin::ready-cake-wizard')->name('admin.ready-cakes.edit');
    Route::livewire('/orders', 'admin::orders')->name('admin.orders');
    Route::livewire('/orders/create', 'admin::order-form')->name('admin.orders.create');
    Route::livewire('/orders/{order}', 'admin::order-details')->name('admin.orders.show');
    Route::livewire('/orders/{order}/edit', 'admin::order-form')->name('admin.orders.edit');
    Route::livewire('/settings', 'admin::settings')->name('admin.settings');
    Route::livewire('/profile', 'admin::profile')->name('admin.profile');
    Route::livewire('/users', 'admin::users')->name('admin.users');
    Route::livewire('/roles', 'admin::roles')->name('admin.roles');
    Route::livewire('/sliders', 'admin::sliders')->name('admin.sliders');
    Route::livewire('/audit-logs', 'admin::audit-log')->name('admin.audit-logs');
    Route::livewire('/assets-importer', 'admin::assets-importer')->name('admin.assets-importer');
    Route::livewire('/languages', 'admin::languages')->name('admin.languages');
    Route::livewire('/languages/{language:code}/translations', 'admin::language-translations')->name('admin.languages.translations');

    Route::livewire('/pages', 'admin::pages')->name('admin.pages');
    Route::livewire('/faqs', 'admin::faqs')->name('admin.faqs');
    Route::livewire('/newsletter-subscribers', 'admin::newsletter-subscribers')->name('admin.newsletter-subscribers');
});

Route::get('/lang/{locale}', function ($locale) {
    if (in_array($locale, Language::pluck('code')->toArray())) {
        session()->put('locale', $locale);
    }
    return redirect()->back();
})->name('set-locale');