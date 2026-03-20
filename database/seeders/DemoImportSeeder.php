<?php

namespace Database\Seeders;

use App\Models\CakeFlavor;
use App\Models\CakeShape;
use App\Models\CakeTopping;
use App\Models\ShapeFlavor;
use App\Models\ShapeTopping;
use App\Models\ToppingCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DemoImportSeeder extends Seeder
{
    private string $cdn = 'https://cakecraft.fra1.cdn.digitaloceanspaces.com';

    private function url(int $id, string $file): string
    {
        return "{$this->cdn}/{$id}/{$file}";
    }

    private function addMedia($model, string $collection, string $url): void
    {
        try {
            if (!$model->hasMedia($collection)) {
                $model->addMediaFromUrl($url)
                    ->preservingOriginal()
                    ->toMediaCollection($collection);
            }
        } catch (\Exception $e) {
            $this->command->warn("  Skip media ({$collection}): " . basename($url));
        }
    }

    public function run(): void
    {
        // ── 1. SHAPES ────────────────────────────────────────────
        // exclude Square (already added by user with real local images)
        $shapesData = [
            'Two Layer'   => ['price' => 10.00, 'thumb' => 17,   'base' => 18,   'cut' => 19],
            'Round'       => ['price' => 10.00, 'thumb' => 1318, 'base' => 1319, 'cut' => 1320],
            'High Round'  => ['price' => 10.00, 'thumb' => 5,    'base' => 6,    'cut' => 7],
            'Rectangular' => ['price' => 10.00, 'thumb' => 1317, 'base' => 9,    'cut' => 10],
            'Heart'       => ['price' => 10.00, 'thumb' => 2,    'base' => 3,    'cut' => 4],
        ];

        $shapeModels = [];
        foreach ($shapesData as $name => $d) {
            $shape = CakeShape::firstOrCreate(['name' => $name], ['base_price' => $d['price']]);
            $this->addMedia($shape, 'thumbnail',       $this->url($d['thumb'], 'thumbnail.png'));
            $this->addMedia($shape, 'base_image',      $this->url($d['base'],  'base.png'));
            $this->addMedia($shape, 'base_cut_image',  $this->url($d['cut'],   'cut.png'));
            $shapeModels[$name] = $shape;
            $this->command->info("Shape: {$name}");
        }

        // Include Square in shapeModels for relationships
        $square = CakeShape::where('name', 'Square')->first();
        if ($square) $shapeModels['Square'] = $square;

        // ── 2. FLAVORS ───────────────────────────────────────────
        // Pattern: flavor thumbnail at ID Y, shape images at Y+1..Y+12
        // Shapes order (alphabetical): Heart, High Round, Rectangular, Round, Square, Two Layer
        $flavorsData = [
            'Black Forest'          => 20,
            'Chocolate Chip Cake'   => 33,
            'Ferrero'               => 46,
            'Mixed Fruit Cake'      => 59,
            'Raspberry Chocolate'   => 72,
            'Red Velvet'            => 85,
            'Strawberry Vanilla Cake' => 98,
            'Vanilla Caramel Cake'  => 111,
        ];

        // Shape order for shape-flavor images (alphabetical = upload order)
        $sfShapeOrder = ['Heart', 'High Round', 'Rectangular', 'Round', 'Square', 'Two Layer'];

        $flavorModels = [];
        foreach ($flavorsData as $name => $thumbId) {
            $flavor = CakeFlavor::firstOrCreate(['name' => $name]);
            $this->addMedia($flavor, 'thumbnail', $this->url($thumbId, 'thumbnail.png'));
            $flavorModels[$name] = $flavor;
            $this->command->info("Flavor: {$name}");
        }

        // ── 3. SHAPE-FLAVORS ─────────────────────────────────────
        foreach ($flavorsData as $flavorName => $thumbId) {
            $flavor = $flavorModels[$flavorName];
            foreach ($sfShapeOrder as $i => $shapeName) {
                if (!isset($shapeModels[$shapeName])) continue;
                $shape = $shapeModels[$shapeName];

                $fullId = $thumbId + 1 + ($i * 2);
                $cutId  = $thumbId + 2 + ($i * 2);

                $pivot = ShapeFlavor::firstOrCreate(
                    ['cake_shape_id' => $shape->id, 'cake_flavor_id' => $flavor->id],
                    ['extra_price' => 10.00]
                );
                $this->addMedia($pivot, 'full_image', $this->url($fullId, 'full.png'));
                $this->addMedia($pivot, 'cut_image',  $this->url($cutId,  'cut.png'));
            }
            $this->command->info("Shape-Flavors linked for: {$flavorName}");
        }

        // ── 4. TOPPINGS + SHAPE-TOPPINGS ─────────────────────────
        // Pattern: each topping block = 7 IDs (thumbnail + 6 shape layers)
        // Shape order for layers: heart(+1), high-round(+2), rectangular(+3), round(+4), square(+5), two-layer(+6)
        $stShapeOrder = [
            'Heart'      => ['offset' => 1, 'slug' => 'heart'],
            'High Round' => ['offset' => 2, 'slug' => 'high-round'],
            'Rectangular'=> ['offset' => 3, 'slug' => 'rectangular'],
            'Round'      => ['offset' => 4, 'slug' => 'round'],
            'Square'     => ['offset' => 5, 'slug' => 'square'],
            'Two Layer'  => ['offset' => 6, 'slug' => 'two-layer'],
        ];

        // All toppings: [name, category, thumbId, code, catSlug]
        // thumbId verified from demo CDN. Pattern: Baby starts at 124, each +7
        $toppingsData = $this->buildToppingsData();

        foreach ($toppingsData as $t) {
            [$name, $catName, $thumbId, $code, $catSlug] = $t;

            $category = ToppingCategory::firstOrCreate(['name' => $catName]);
            $topping  = CakeTopping::firstOrCreate(
                ['name' => $name, 'topping_category_id' => $category->id]
            );

            $thumbFile = "{$code}-heart-{$catSlug}.png";
            $this->addMedia($topping, 'thumbnail', $this->url($thumbId, $thumbFile));

            // Shape-topping layers
            foreach ($stShapeOrder as $shapeName => $info) {
                if (!isset($shapeModels[$shapeName])) continue;
                $shape = $shapeModels[$shapeName];

                $layerId   = $thumbId + $info['offset'];
                $layerFile = "{$code}-{$info['slug']}-{$catSlug}.png";

                $pivot = ShapeTopping::firstOrCreate(
                    ['cake_shape_id' => $shape->id, 'cake_topping_id' => $topping->id],
                    ['price' => 10.00]
                );
                $this->addMedia($pivot, 'image_layer', $this->url($layerId, $layerFile));
            }

            $this->command->line("  Topping: {$name} ({$catName})");
        }

        $this->command->info('');
        $this->command->info('Demo import complete!');
    }

    private function buildToppingsData(): array
    {
        $data = [];

        // Baby: 21 items, IDs 124-264, codes 01-21
        for ($i = 1; $i <= 21; $i++) {
            $code    = str_pad($i, 2, '0', STR_PAD_LEFT);
            $thumbId = 124 + (($i - 1) * 7);
            $data[]  = ["Baby {$code}", 'Baby', $thumbId, $code, 'baby'];
        }

        // Boy: 45 items, IDs 271+
        // Boy 01-32 (simple codes), Boy 33-2 through 45-2 (-2 suffix)
        $boyStart = 271;
        for ($i = 1; $i <= 32; $i++) {
            $code    = str_pad($i, 2, '0', STR_PAD_LEFT);
            $thumbId = $boyStart + (($i - 1) * 7);
            $data[]  = ["Boy {$code}", 'Boy', $thumbId, $code, 'boy'];
        }
        for ($i = 33; $i <= 45; $i++) {
            $code    = $i . '-2';
            $thumbId = $boyStart + (($i - 1) * 7);
            $data[]  = ["Boy {$code}", 'Boy', $thumbId, $code, 'boy'];
        }

        // Bride To Be: 25 items, ID 581
        // 01-18 simple, 19-2 through 25-2
        $btbStart = 581;
        for ($i = 1; $i <= 18; $i++) {
            $code    = str_pad($i, 2, '0', STR_PAD_LEFT);
            $thumbId = $btbStart + (($i - 1) * 7);
            $data[]  = ["Bride To Be {$code}", 'Bride To Be', $thumbId, $code, 'bride-to-be'];
        }
        for ($i = 19; $i <= 25; $i++) {
            $code    = $i . '-2';
            $thumbId = $btbStart + (($i - 1) * 7);
            $data[]  = ["Bride To Be {$code}", 'Bride To Be', $thumbId, $code, 'bride-to-be'];
        }

        // Flowers: 5 items, ID 756
        $flowersStart = 756;
        for ($i = 1; $i <= 5; $i++) {
            $code    = str_pad($i, 2, '0', STR_PAD_LEFT);
            $thumbId = $flowersStart + (($i - 1) * 7);
            $data[]  = ["Flowers {$code}", 'Flowers', $thumbId, $code, 'flowers'];
        }

        // Girl: 32 items, ID 791
        // 01-17 simple, 18-2 through 32-2
        $girlStart = 791;
        for ($i = 1; $i <= 17; $i++) {
            $code    = str_pad($i, 2, '0', STR_PAD_LEFT);
            $thumbId = $girlStart + (($i - 1) * 7);
            $data[]  = ["Girl {$code}", 'Girl', $thumbId, $code, 'girl'];
        }
        for ($i = 18; $i <= 32; $i++) {
            $code    = $i . '-2';
            $thumbId = $girlStart + (($i - 1) * 7);
            $data[]  = ["Girl {$code}", 'Girl', $thumbId, $code, 'girl'];
        }

        // Graduation: 20 items, ID 1015
        // 01-07 simple, 08-2 through 20-2
        $gradStart = 1015;
        for ($i = 1; $i <= 7; $i++) {
            $code    = str_pad($i, 2, '0', STR_PAD_LEFT);
            $thumbId = $gradStart + (($i - 1) * 7);
            $data[]  = ["Graduation {$code}", 'Graduation', $thumbId, $code, 'graduation'];
        }
        for ($i = 8; $i <= 20; $i++) {
            $code    = $i . '-2';
            $thumbId = $gradStart + (($i - 1) * 7);
            $data[]  = ["Graduation {$code}", 'Graduation', $thumbId, $code, 'graduation'];
        }

        // Teenager: 23 items, ID 1155
        // 01-14 simple, 15-2 through 23-2
        $teenStart = 1155;
        for ($i = 1; $i <= 14; $i++) {
            $code    = str_pad($i, 2, '0', STR_PAD_LEFT);
            $thumbId = $teenStart + (($i - 1) * 7);
            $data[]  = ["Teenager {$code}", 'Teenager', $thumbId, $code, 'teenager'];
        }
        for ($i = 15; $i <= 23; $i++) {
            $code    = $i . '-2';
            $thumbId = $teenStart + (($i - 1) * 7);
            $data[]  = ["Teenager {$code}", 'Teenager', $thumbId, $code, 'teenager'];
        }

        return $data;
    }
}
