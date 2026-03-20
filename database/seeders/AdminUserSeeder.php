<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        // Setup default admin user
        $admin = User::firstOrCreate(
            ['email' => 'admin@cakecraft.test'],
            [
                'name' => 'Admin',
                'password' => bcrypt('password'),
            ]
        );

        if (!$admin->hasRole('admin')) {
            $admin->assignRole('admin');
        }

        // Setup default staff user
        $staff = User::firstOrCreate(
            ['email' => 'staff@cakecraft.test'],
            [
                'name' => 'Staff Member',
                'password' => bcrypt('password'),
            ]
        );

        if (!$staff->hasRole('staff')) {
            $staff->assignRole('staff');
        }
    }
}
