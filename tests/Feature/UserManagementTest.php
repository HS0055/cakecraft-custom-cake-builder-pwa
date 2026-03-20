<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

beforeEach(function () {
    app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
    // Seed necessary permissions
    $permissions = ['view users', 'create users', 'update users', 'delete users'];
    foreach ($permissions as $p) {
        Permission::create(['name' => $p, 'guard_name' => 'web']);
    }
    Role::create(['name' => 'admin', 'guard_name' => 'web'])->givePermissionTo($permissions);
});

test('admin can see users list', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $this->actingAs($admin)
        ->get(route('admin.users'))
        ->assertOk();
});

test('admin can create user', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    Livewire::actingAs($admin)
        ->test('admin::users')
        ->set('name', 'New User')
        ->set('email', 'new@example.com')
        ->set('password', 'password123')
        ->set('selectedRoles', ['admin'])
        ->call('save')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('users', ['email' => 'new@example.com']);
    // $admin->refresh(); // Just in case
    // assertSessionHas might be tricky in some Livewire 4 scenarios, checking DB first.
});

test('admin can block and unblock user', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');
    $user = User::factory()->create();

    Livewire::actingAs($admin)
        ->test('admin::users')
        ->call('toggleBlock', $user->id);

    expect($user->fresh()->is_blocked)->toBeTrue();

    Livewire::actingAs($admin)
        ->test('admin::users')
        ->call('toggleBlock', $user->id);

    expect($user->fresh()->is_blocked)->toBeFalse();
});

test('admin cannot be blocked', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');
    $anotherAdmin = User::factory()->create();
    $anotherAdmin->assignRole('admin');

    Livewire::actingAs($admin)
        ->test('admin::users')
        ->call('toggleBlock', $anotherAdmin->id);

    expect($anotherAdmin->fresh()->is_blocked)->toBeFalse();
});

test('no admin can be deleted', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');
    $anotherAdmin = User::factory()->create();
    $anotherAdmin->assignRole('admin');

    Livewire::actingAs($admin)
        ->test('admin::users')
        ->call('confirmDelete', $anotherAdmin->id);

    $this->assertDatabaseHas('users', ['id' => $anotherAdmin->id]);
});
