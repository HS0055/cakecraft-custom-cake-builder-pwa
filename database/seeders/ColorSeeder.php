<?php

namespace Database\Seeders;

use App\Models\CakeColor;
use Illuminate\Database\Seeder;

class ColorSeeder extends Seeder
{
    /**
     * Seed 10 joyful cake colors.
     */
    public function run(): void
    {
        $colors = [
            // Row 1
            ['name' => 'White', 'hex_code' => '#FFFFFF'],
            ['name' => 'Light Blue', 'hex_code' => '#BAE6FD'],
            ['name' => 'Light Green', 'hex_code' => '#D9ECC7'],
            ['name' => 'Light Pink', 'hex_code' => '#FCE0DD'],
            ['name' => 'Light Yellow', 'hex_code' => '#FDE68A'],

            // Row 2
            ['name' => 'Peach', 'hex_code' => '#FCE2B9'],
            ['name' => 'Cyan', 'hex_code' => '#22D3EE'],
            ['name' => 'Mint Green', 'hex_code' => '#86C57F'],
            ['name' => 'Rose', 'hex_code' => '#F4B3B8'],
            ['name' => 'Orange', 'hex_code' => '#FB923C'],

            // Row 3
            ['name' => 'Soft Orange', 'hex_code' => '#FCD39A'],
            ['name' => 'Navy', 'hex_code' => '#1E3A8A'],
            ['name' => 'Green', 'hex_code' => '#4BA844'],
            ['name' => 'Pink', 'hex_code' => '#F66B9E'],
            ['name' => 'Red', 'hex_code' => '#B91C1C'],

            // Row 4
            ['name' => 'Tan', 'hex_code' => '#E3BA8F'],
            ['name' => 'Lavender', 'hex_code' => '#E0D4E7'],
            ['name' => 'Mauve', 'hex_code' => '#C481B6'],
            ['name' => 'Magenta', 'hex_code' => '#C7517D'],
            ['name' => 'Custom (Dropper)', 'hex_code' => '#FCA5A5'], // Dropper icon color as fallback
        ];

        foreach ($colors as $color) {
            CakeColor::firstOrCreate(
                ['name' => $color['name']],
                ['hex_code' => $color['hex_code']]
            );
        }
    }
}
