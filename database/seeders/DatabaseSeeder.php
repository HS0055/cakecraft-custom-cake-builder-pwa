<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
                // 1. Core Config & Security
            LanguageSeeder::class,
            RolesAndPermissionsSeeder::class,
            AdminUserSeeder::class,

                // 2. Base Product Options
            ColorSeeder::class,

                // 3. Site Content/CMS
            PageSeeder::class,
            FaqSeeder::class,
        ]);
    }
}
