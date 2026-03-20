<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

beforeEach(function () {
    app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
    $permissions = ['view roles', 'create roles', 'update roles', 'delete roles'];
    foreach ($permissions as $p) {
        Permission::create(['name' => $p, 'guard_name' => 'web']);
    }
    Role::create(['name' => 'admin', 'guard_name' => 'web'])->givePermissionTo($permissions);
});

test('admin can create role', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    Livewire::actingAs($admin)
        ->test('admin::roles')
        ->set('name', 'Manager')
        ->set('selectedPermissions', ['view roles'])
        ->call('save')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('roles', ['name' => 'Manager']);
});

test('admin cannot delete admin role', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');
    $role = Role::where('name', 'admin')->first();

    Livewire::actingAs($admin)
        ->test('admin::roles')
        ->call('confirmDelete', $role->id);

    $this->assertDatabaseHas('roles', ['id' => $role->id]);
});
