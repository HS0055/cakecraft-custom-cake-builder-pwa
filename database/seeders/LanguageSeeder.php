<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LanguageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Language::firstOrCreate(
            ['code' => 'en'],
            ['name' => 'English', 'is_active' => true]
        );

        \App\Models\Language::firstOrCreate(
            ['code' => 'ar'],
            ['name' => 'Arabic', 'is_active' => true]
        );
    }
}
