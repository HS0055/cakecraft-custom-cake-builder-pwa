<?php

use App\Models\CakeTopping;
use App\Models\ToppingCategory;
use App\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Livewire\Livewire;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->admin = User::factory()->create();
    $role = Role::firstOrCreate(['name' => 'admin']);

    $permissions = [
        'view toppings',
        'create toppings',
        'update toppings',
        'delete toppings',
        'view topping categories',
        'create topping categories',
        'update topping categories',
        'delete topping categories'
    ];

    foreach ($permissions as $perm) {
        Permission::firstOrCreate(['name' => $perm]);
    }

    $role->givePermissionTo($permissions);
    $this->admin->assignRole($role);

    $this->user = User::factory()->create();
});

test('admin can view toppings list', function () {
    $this->actingAs($this->admin)
        ->get(route('admin.cake-toppings'))
        ->assertOk()
        ->assertSeeLivewire('admin::cake-toppings');
});

test('admin can create a topping category', function () {
    Livewire::actingAs($this->admin)
        ->test('admin::topping-categories')
        ->call('openCreate')
        ->set('name', 'Fruits')
        ->call('save')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('topping_categories', [
        'name' => 'Fruits',
    ]);
});

test('admin can create a topping', function () {
    $category = ToppingCategory::factory()->create(['name' => 'Fruits']);

    Livewire::actingAs($this->admin)
        ->test('admin::cake-toppings')
        ->call('openCreate')
        ->set('name', 'Strawberry')
        ->set('topping_category_id', $category->id)
        ->call('save')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('cake_toppings', [
        'name' => 'Strawberry',
        'topping_category_id' => $category->id,
    ]);
});

test('admin can update a topping', function () {
    $category = ToppingCategory::factory()->create();
    $topping = CakeTopping::factory()->create([
        'name' => 'Old Name',
        'topping_category_id' => $category->id
    ]);

    Livewire::actingAs($this->admin)
        ->test('admin::cake-toppings')
        ->call('openEdit', $topping->id)
        ->set('name', 'New Name')
        ->call('save')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('cake_toppings', [
        'id' => $topping->id,
        'name' => 'New Name',
    ]);
});

test('admin can delete a topping', function () {
    $category = ToppingCategory::factory()->create();
    $topping = CakeTopping::factory()->create(['topping_category_id' => $category->id]);

    Livewire::actingAs($this->admin)
        ->test('admin::cake-toppings')
        ->call('confirmDelete', $topping->id)
        ->call('delete')
        ->assertHasNoErrors();

    $this->assertDatabaseMissing('cake_toppings', ['id' => $topping->id]);
});
