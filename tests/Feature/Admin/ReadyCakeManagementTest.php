<?php

use App\Models\CakeFlavor;
use App\Models\CakeShape;
use App\Models\CakeTopping;
use App\Models\ReadyCake;
use App\Models\ShapeFlavor;
use App\Models\ShapeTopping;
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
        'view ready cakes',
        'create ready cakes',
        'update ready cakes',
        'delete ready cakes'
    ];

    foreach ($permissions as $perm) {
        Permission::firstOrCreate(['name' => $perm]);
    }

    $role->givePermissionTo($permissions);
    $this->admin->assignRole($role);

    $this->user = User::factory()->create();
});

test('admin can view ready cakes list', function () {
    $this->actingAs($this->admin)
        ->get(route('admin.ready-cakes'))
        ->assertOk()
        ->assertSeeLivewire('admin::ready-cakes');
});

test('admin can create a ready cake via wizard', function () {
    // Setup dependencies
    $shape = CakeShape::factory()->create(['name' => 'Round']);
    $flavor = CakeFlavor::factory()->create(['name' => 'Chocolate']);
    $topping = CakeTopping::factory()->create(['name' => 'Sprinkles']);

    // Create necessary combinations
    ShapeFlavor::create([
        'cake_shape_id' => $shape->id,
        'cake_flavor_id' => $flavor->id,
        'extra_price' => 0
    ]);

    ShapeTopping::create([
        'cake_shape_id' => $shape->id,
        'cake_topping_id' => $topping->id,
        'price' => 0
    ]);

    Livewire::actingAs($this->admin)
        ->test('admin::ready-cake-wizard')
        // Step 1: Shape
        ->set('cake_shape_id', $shape->id)
        ->call('nextStep')
        // Step 2: Flavor
        ->set('cake_flavor_id', $flavor->id)
        ->call('nextStep')
        // Step 3: Color
        ->set('custom_hex', '#ff0000') // Custom color
        ->call('nextStep')
        // Step 4: Topping
        ->set('cake_topping_id', $topping->id)
        ->call('nextStep')
        // Step 5: Info
        ->set('name', 'Birthday Cake')
        ->set('price', '50.00')
        ->set('is_active', true)
        ->set('is_customizable', false)
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('admin.ready-cakes'));

    $this->assertDatabaseHas('ready_cakes', [
        'name' => 'Birthday Cake',
        'price' => 50.00,
        'cake_shape_id' => $shape->id,
        'cake_flavor_id' => $flavor->id,
        'custom_color_hex' => '#ff0000',
    ]);
});

test('admin can delete a ready cake', function () {
    $cake = ReadyCake::factory()->create();

    Livewire::actingAs($this->admin)
        ->test('admin::ready-cakes')
        ->call('confirmDelete', $cake->id)
        ->call('delete')
        ->assertHasNoErrors();

    $this->assertDatabaseMissing('ready_cakes', ['id' => $cake->id]);
});

test('admin can toggle ready cake status', function () {
    $cake = ReadyCake::factory()->create(['is_active' => false]);

    Livewire::actingAs($this->admin)
        ->test('admin::ready-cakes')
        ->call('toggleActive', $cake->id);

    expect($cake->fresh()->is_active)->toBeTrue();
});
