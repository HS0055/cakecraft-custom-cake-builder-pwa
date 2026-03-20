<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // 1. Standard CRUD Entities
        $standardEntities = [
            'shapes',
            'flavors',
            'toppings',
            'topping categories',
            'colors',
            'ready cakes',
            'users',
            'roles',
            'sliders',
            'pages',
            'faqs',
            'orders'
        ];
        $standardActions = ['view', 'create', 'update', 'delete'];

        foreach ($standardEntities as $entity) {
            foreach ($standardActions as $action) {
                Permission::firstOrCreate(['name' => "{$action} {$entity}"]);
            }
        }

        // 2. Custom/Singleton Entities
        $customPermissions = [
            'view newsletter subscribers',
            'delete newsletter subscribers',
            'view settings',
            'update settings',
            'view settings audit',
            'import assets'
        ];

        foreach ($customPermissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // 3. Create roles & Assign permissions
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $staffRole = Role::firstOrCreate(['name' => 'staff']);

        // Admin gets everything
        $adminRole->syncPermissions(Permission::all());

        // Staff only gets to manage orders
        $staffRole->syncPermissions(
            Permission::where('name', 'like', '% orders')->get()
        );
    }
}
