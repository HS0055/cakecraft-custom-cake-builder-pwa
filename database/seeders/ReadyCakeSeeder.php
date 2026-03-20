<?php

namespace Database\Seeders;

use App\Models\CakeColor;
use App\Models\CakeShape;
use App\Models\ReadyCake;
use App\Models\ShapeFlavor;
use App\Models\ShapeTopping;
use Illuminate\Database\Seeder;

class ReadyCakeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $shapes = CakeShape::with(['flavors', 'toppings'])
            ->has('flavors')
            ->has('toppings')
            ->get();
        $colors = CakeColor::all();

        if ($shapes->isEmpty()) {
            $this->command->warn('No shapes with flavors and toppings found. Please seed shapes first.');
            return;
        }

        // We will seed 50 cakes
        for ($i = 0; $i < 50; $i++) {
            $shape = $shapes->random();

            $flavor = $shape->flavors->random();
            $topping = $shape->toppings->random();

            $color = null;
            $customHex = null;

            if ($colors->isNotEmpty() && mt_rand(1, 100) > 20) {
                // 80% chance of palette color
                $color = $colors->random();
            } else {
                // 20% chance of custom hex color
                $customHex = '#' . str_pad(dechex(mt_rand(0, 0xFFFFFF)), 6, '0', STR_PAD_LEFT);
            }

            $shapePrice = $shape->base_price ?? 0;

            $shapeFlavor = ShapeFlavor::where('cake_shape_id', $shape->id)
                ->where('cake_flavor_id', $flavor->id)
                ->first();
            $flavorPrice = $shapeFlavor?->extra_price ?? 0;

            $toppingPrice = 0;
            if ($topping) {
                $shapeTopping = ShapeTopping::where('cake_shape_id', $shape->id)
                    ->where('cake_topping_id', $topping->id)
                    ->first();
                $toppingPrice = $shapeTopping?->price ?? 0;
            }

            $price = $shapePrice + $flavorPrice + $toppingPrice;

            $parts = [];
            $parts[] = $shape->name;
            $parts[] = $flavor->name;
            if ($topping) {
                $parts[] = $topping->name;
            }
            $name = implode(' - ', $parts);

            ReadyCake::create([
                'name' => $name,
                'price' => number_format($price, 2, '.', ''),
                'cake_shape_id' => $shape->id,
                'cake_flavor_id' => $flavor->id,
                'cake_color_id' => $color?->id,
                'cake_topping_id' => $topping?->id,
                'custom_color_hex' => $customHex,
                'is_active' => true,
                'is_customizable' => mt_rand(0, 1) === 1,
            ]);
        }

        $this->command->info('Created 50 ready cakes successfully.');
    }
}
