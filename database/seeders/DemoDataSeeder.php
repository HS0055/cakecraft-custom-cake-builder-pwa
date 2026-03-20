<?php

namespace Database\Seeders;

use App\Models\CakeFlavor;
use App\Models\CakeShape;
use App\Models\CakeTopping;
use App\Models\ToppingCategory;
use Illuminate\Database\Seeder;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        // ── Shapes ────────────────────────────────────────────────
        $shapes = [
            ['name' => 'Round',     'base_price' => 25.00],
            ['name' => 'Square',    'base_price' => 28.00],
            ['name' => 'Heart',     'base_price' => 32.00],
            ['name' => 'Rectangle', 'base_price' => 30.00],
            ['name' => 'Hexagon',   'base_price' => 35.00],
        ];

        $shapeModels = [];
        foreach ($shapes as $data) {
            $shapeModels[$data['name']] = CakeShape::firstOrCreate(
                ['name' => $data['name']],
                ['base_price' => $data['base_price']]
            );
        }

        // ── Flavors ───────────────────────────────────────────────
        $flavors = [
            'Vanilla',
            'Chocolate',
            'Strawberry',
            'Red Velvet',
            'Lemon',
            'Carrot',
        ];

        $flavorModels = [];
        foreach ($flavors as $name) {
            $flavorModels[$name] = CakeFlavor::firstOrCreate(['name' => $name]);
        }

        // ── Topping Categories ────────────────────────────────────
        $categories = ['Fruits', 'Candies', 'Nuts', 'Drizzles', 'Decorations'];

        $catModels = [];
        foreach ($categories as $name) {
            $catModels[$name] = ToppingCategory::firstOrCreate(['name' => $name]);
        }

        // ── Toppings ──────────────────────────────────────────────
        $toppings = [
            'Fruits'      => ['Fresh Strawberries', 'Blueberries', 'Raspberries', 'Sliced Mango'],
            'Candies'     => ['Sprinkles', 'M&Ms', 'Gummy Bears', 'Chocolate Chips'],
            'Nuts'        => ['Crushed Almonds', 'Walnuts', 'Pistachios', 'Hazelnuts'],
            'Drizzles'    => ['Chocolate Drizzle', 'Caramel Drizzle', 'Strawberry Glaze', 'White Chocolate'],
            'Decorations' => ['Edible Flowers', 'Gold Leaf', 'Sugar Pearls', 'Fondant Stars'],
        ];

        $toppingModels = [];
        foreach ($toppings as $catName => $items) {
            foreach ($items as $name) {
                $toppingModels[$name] = CakeTopping::firstOrCreate(
                    ['name' => $name],
                    ['topping_category_id' => $catModels[$catName]->id]
                );
            }
        }

        // ── Shape-Flavors (every shape gets all flavors) ──────────
        $flavorPrices = [
            'Vanilla'    => 0.00,
            'Chocolate'  => 2.00,
            'Strawberry' => 3.00,
            'Red Velvet' => 4.00,
            'Lemon'      => 2.50,
            'Carrot'     => 3.50,
        ];

        foreach ($shapeModels as $shape) {
            foreach ($flavorModels as $flavorName => $flavor) {
                $exists = $shape->flavors()->where('cake_flavor_id', $flavor->id)->exists();
                if (! $exists) {
                    $shape->flavors()->attach($flavor->id, [
                        'extra_price' => $flavorPrices[$flavorName],
                    ]);
                }
            }
        }

        // ── Shape-Toppings (every shape gets all toppings) ────────
        $toppingPrices = [
            'Fresh Strawberries' => 3.00,
            'Blueberries'        => 3.50,
            'Raspberries'        => 4.00,
            'Sliced Mango'       => 3.50,
            'Sprinkles'          => 1.00,
            'M&Ms'               => 2.00,
            'Gummy Bears'        => 2.00,
            'Chocolate Chips'    => 1.50,
            'Crushed Almonds'    => 2.50,
            'Walnuts'            => 2.50,
            'Pistachios'         => 3.00,
            'Hazelnuts'          => 2.50,
            'Chocolate Drizzle'  => 1.50,
            'Caramel Drizzle'    => 1.50,
            'Strawberry Glaze'   => 2.00,
            'White Chocolate'    => 2.00,
            'Edible Flowers'     => 5.00,
            'Gold Leaf'          => 8.00,
            'Sugar Pearls'       => 2.00,
            'Fondant Stars'      => 3.00,
        ];

        foreach ($shapeModels as $shape) {
            foreach ($toppingModels as $toppingName => $topping) {
                $exists = $shape->toppings()->where('cake_topping_id', $topping->id)->exists();
                if (! $exists) {
                    $shape->toppings()->attach($topping->id, [
                        'price' => $toppingPrices[$toppingName],
                    ]);
                }
            }
        }

        $this->command->info('Demo data seeded:');
        $this->command->info('  ' . count($shapeModels) . ' shapes');
        $this->command->info('  ' . count($flavorModels) . ' flavors');
        $this->command->info('  ' . count($catModels) . ' topping categories');
        $this->command->info('  ' . array_sum(array_map('count', $toppings)) . ' toppings');
        $this->command->info('  Shape-Flavor links: ' . (count($shapeModels) * count($flavorModels)));
        $this->command->info('  Shape-Topping links: ' . (count($shapeModels) * count($toppingModels)));
    }
}
