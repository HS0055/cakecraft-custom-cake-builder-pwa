<?php

use App\Models\CakeShape;
use App\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->admin = User::factory()->create();
    $role = Role::firstOrCreate(['name' => 'admin']);

    // Create permissions
    $permissions = ['view shapes', 'create shapes', 'update shapes', 'delete shapes'];
    foreach ($permissions as $perm) {
        Permission::firstOrCreate(['name' => $perm]);
    }

    $role->givePermissionTo($permissions);
    $this->admin->assignRole($role);

    $this->user = User::factory()->create();
});

test('admin can view shapes list', function () {
    $this->actingAs($this->admin)
        ->get(route('admin.cake-shapes'))
        ->assertOk()
        ->assertSeeLivewire('admin::cake-shapes');
});

test('non-admin cannot view shapes list', function () {
    $this->actingAs($this->user)
        ->get(route('admin.cake-shapes'))
        ->assertForbidden();
});

test('admin can create a shape', function () {
    Livewire::actingAs($this->admin)
        ->test('admin::cake-shapes')
        ->set('name', 'Square')
        ->set('base_price', '15.00')
        ->call('save')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('cake_shapes', [
        'name' => 'Square',
        'base_price' => 15.00,
    ]);
});

test('validation rules are enforced', function () {
    Livewire::actingAs($this->admin)
        ->test('admin::cake-shapes')
        ->set('name', '')
        ->set('base_price', '')
        ->call('save')
        ->assertHasErrors(['name', 'base_price']);
});

test('admin can update a shape', function () {
    $shape = CakeShape::factory()->create(['name' => 'Old Name', 'base_price' => 10.00]);

    Livewire::actingAs($this->admin)
        ->test('admin::cake-shapes')
        ->call('openEdit', $shape->id)
        ->set('name', 'New Name')
        ->set('base_price', '20.00')
        ->call('save')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('cake_shapes', [
        'id' => $shape->id,
        'name' => 'New Name',
        'base_price' => 20.00,
    ]);
});

test('admin can delete a shape', function () {
    $shape = CakeShape::factory()->create();

    Livewire::actingAs($this->admin)
        ->test('admin::cake-shapes')
        ->call('confirmDelete', $shape->id)
        ->call('delete')
        ->assertHasNoErrors();

    $this->assertDatabaseMissing('cake_shapes', ['id' => $shape->id]);
});
