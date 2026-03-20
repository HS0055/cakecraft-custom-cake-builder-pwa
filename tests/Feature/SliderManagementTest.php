<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Slider;
use App\Models\ReadyCake;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

beforeEach(function () {
    app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
    Storage::fake('public');

    $permissions = ['view sliders', 'create sliders', 'update sliders', 'delete sliders'];
    foreach ($permissions as $p) {
        Permission::create(['name' => $p, 'guard_name' => 'web']);
    }
    Role::create(['name' => 'admin', 'guard_name' => 'web'])->givePermissionTo($permissions);
});

test('admin can see sliders list', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $this->actingAs($admin)
        ->get(route('admin.sliders'))
        ->assertOk();
});

test('admin can create slider', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');
    $cake = ReadyCake::factory()->create();

    $image = UploadedFile::fake()->image('slide.jpg');

    Livewire::actingAs($admin)
        ->test('admin::sliders')
        ->set('action_type', 'ready_cake')
        ->set('ready_cake_id', $cake->id)
        ->set('image', $image)
        ->call('save')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('sliders', [
        'ready_cake_id' => $cake->id,
    ]);
});

test('slider validation rules', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    Livewire::actingAs($admin)
        ->test('admin::sliders')
        ->set('action_type', 'ready_cake')
        ->set('ready_cake_id', null)
        ->call('save')
        ->assertHasErrors(['ready_cake_id' => 'required_if']);
});

test('admin can toggle slider status', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');
    $slider = Slider::create([
        'title' => 'Promo',
        'action_type' => 'custom_builder',
        'is_active' => true,
    ]);

    Livewire::actingAs($admin)
        ->test('admin::sliders')
        ->call('toggleActive', $slider->id);

    expect($slider->fresh()->is_active)->toBeFalse();
});

test('admin can delete slider', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');
    $slider = Slider::create([
        'title' => 'To Delete',
        'action_type' => 'custom_builder',
    ]);

    Livewire::actingAs($admin)
        ->test('admin::sliders')
        ->call('confirmDelete', $slider->id)
        ->call('delete');

    $this->assertDatabaseMissing('sliders', ['id' => $slider->id]);
});
