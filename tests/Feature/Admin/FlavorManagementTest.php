<?php

use App\Models\CakeFlavor;
use App\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Livewire\Livewire;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->admin = User::factory()->create();
    $role = Role::firstOrCreate(['name' => 'admin']);

    $permissions = ['view flavors', 'create flavors', 'update flavors', 'delete flavors'];
    foreach ($permissions as $perm) {
        Permission::firstOrCreate(['name' => $perm]);
    }

    $role->givePermissionTo($permissions);
    $this->admin->assignRole($role);

    $this->user = User::factory()->create();
});

test('admin can view flavors list', function () {
    $this->actingAs($this->admin)
        ->get(route('admin.cake-flavors'))
        ->assertOk()
        ->assertSeeLivewire('admin::cake-flavors');
});

test('non-admin cannot view flavors list', function () {
    $this->actingAs($this->user)
        ->get(route('admin.cake-flavors'))
        ->assertForbidden();
});

test('admin can create a flavor', function () {
    Livewire::actingAs($this->admin)
        ->test('admin::cake-flavors')
        ->call('openCreate')
        ->set('name', 'Vanilla')
        ->call('save')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('cake_flavors', [
        'name' => 'Vanilla',
    ]);
});

test('validation rules are enforced', function () {
    Livewire::actingAs($this->admin)
        ->test('admin::cake-flavors')
        ->call('openCreate')
        ->set('name', '')
        ->call('save')
        ->assertHasErrors(['name']);
});

test('admin can update a flavor', function () {
    $flavor = CakeFlavor::factory()->create(['name' => 'Old Name']);

    Livewire::actingAs($this->admin)
        ->test('admin::cake-flavors')
        ->call('openEdit', $flavor->id)
        ->set('name', 'New Name')
        ->call('save')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('cake_flavors', [
        'id' => $flavor->id,
        'name' => 'New Name',
    ]);
});

test('admin can delete a flavor', function () {
    $flavor = CakeFlavor::factory()->create();

    Livewire::actingAs($this->admin)
        ->test('admin::cake-flavors')
        ->call('confirmDelete', $flavor->id)
        ->call('delete')
        ->assertHasNoErrors();

    $this->assertDatabaseMissing('cake_flavors', ['id' => $flavor->id]);
});
