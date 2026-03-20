<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

beforeEach(function () {
    \Spatie\Permission\Models\Permission::firstOrCreate(['name' => 'view settings', 'guard_name' => 'web']);
    \Spatie\Permission\Models\Permission::firstOrCreate(['name' => 'update settings', 'guard_name' => 'web']);
});

test('settings page can be rendered', function () {
    $admin = User::factory()->create();
    $role = Role::firstOrCreate(['name' => 'admin']);
    $admin->assignRole($role);
    $admin->givePermissionTo('view settings');

    $this->actingAs($admin)
        ->get(route('admin.settings'))
        ->assertOk();
});

test('settings can be saved', function () {
    $admin = User::factory()->create();
    $role = Role::firstOrCreate(['name' => 'admin']);
    $admin->assignRole($role);
    $admin->givePermissionTo(['view settings', 'update settings']);

    Livewire::actingAs($admin)
        ->test('admin::settings')
        ->set('store_name', 'My Awesome Bakery')
        ->set('store_email', 'admin@example.com')
        ->set('currency_code', 'USD')
        ->set('currency_symbol', '$')
        ->set('primary_color', '#FF0000')
        ->set('admin_sidebar_color', '#000000')
        ->call('save')
        ->assertHasNoErrors();

    $general = app(\App\Settings\GeneralSettings::class);
    expect($general->store_name)->toBe('My Awesome Bakery');
});

test('maintenance mode middleware blocks access', function () {
    $settings = app(\App\Settings\SystemSettings::class);
    $settings->maintenance_mode = true;
    $settings->save();

    // Guest should be blocked (actually they might be redirected to login, but let's check a protected route or login page itself if strict)
    // Our middleware allows login routes.
    $this->get(route('login'))->assertOk();

    // Non-admin user accessing helpful page
    $user = User::factory()->create();
    $this->actingAs($user)->get('/')->assertStatus(503);

    // Admin should be allowed
    $admin = User::factory()->create();
    $role = Role::firstOrCreate(['name' => 'admin']);
    $admin->assignRole($role);
    $admin->givePermissionTo('view settings');

    // We need to define a route to test locally if '/' is redirecting
    // But admin routes are protected.
    $this->actingAs($admin)->get(route('admin.settings'))->assertOk();

    // Cleanup
    $settings->maintenance_mode = false;
    $settings->save();
});
