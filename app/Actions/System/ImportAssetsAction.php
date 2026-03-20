<?php

namespace App\Actions\System;

use App\Models\CakeFlavor;
use App\Models\CakeShape;
use App\Models\CakeTopping;
use App\Models\ShapeFlavor;
use App\Models\ShapeTopping;
use App\Models\ToppingCategory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

/**
 * Class ImportAssetsAction
 *
 * Action class responsible for navigating a local directory structure and idempotently
 * importing Shapes, Flavors, Toppings, and their associated Spatie Media items.
 * Wrapped in transactions to maintain relational integrity on a per-item basis.
 */
class ImportAssetsAction
{
    private $onLog;
    private $validShapes = ['heart', 'round', 'high-round', 'two-layer', 'rectangular', 'square'];

    public function execute(string $baseDir, callable $onLog, array $defaultPrices = [])
    {
        // This is no longer the main entrypoint. The AssetsImporter component 
        // will call the directory-specific methods directly based on its queue.
        // We leave this here just in case, but it's no longer used.
    }

    public function setLogger(callable $onLog): void
    {
        $this->onLog = $onLog;
    }

    private function log(string $type, string $message): void
    {
        if ($this->onLog) {
            call_user_func($this->onLog, $type, $message);
        }
    }

    private function capitalizeName(string $name): string
    {
        return ucwords(str_replace('-', ' ', $name));
    }

    public function importShapeDirectory(string $dir, float $price)
    {
        if (!File::exists($dir))
            return;

        $rawName = basename($dir);
        $name = $this->capitalizeName($rawName);

        try {
            DB::transaction(function () use ($name, $price, $dir) {
                $shape = CakeShape::firstOrCreate(
                    ['name' => $name],
                    ['base_price' => $price]
                );

                if ($shape->wasRecentlyCreated) {
                    $this->log('created', "Shape: {$name}");
                } else {
                    $this->log('skipped', "Shape: {$name} (already exists)");
                }

                $mediaMap = [
                    'thumbnail.png' => 'thumbnail',
                    'base.png' => 'base_image',
                    'cut.png' => 'base_cut_image',
                ];

                foreach ($mediaMap as $file => $collection) {
                    $filePath = $dir . '/' . $file;
                    if (File::exists($filePath)) {
                        if (!$shape->hasMedia($collection)) {
                            $shape->addMedia($filePath)
                                ->preservingOriginal()
                                ->toMediaCollection($collection);
                            $this->log('created', "  + Attached {$file} to {$name}");
                        }
                    }
                }
            });
        } catch (\Exception $e) {
            $this->log('error', "Shape Error ({$name}): " . $e->getMessage());
        }
    }

    public function importFlavorDirectory(string $dir, float $price)
    {
        if (!File::exists($dir))
            return;

        $rawName = basename($dir);
        $name = $this->capitalizeName($rawName);

        try {
            DB::transaction(function () use ($name, $dir, $price) {
                $flavor = CakeFlavor::firstOrCreate(['name' => $name]);

                if ($flavor->wasRecentlyCreated) {
                    $this->log('created', "Flavor: {$name}");
                } else {
                    $this->log('skipped', "Flavor: {$name} (already exists)");
                }

                // Thumbnail
                $thumbPath = $dir . '/thumbnail.png';
                if (File::exists($thumbPath) && !$flavor->hasMedia('thumbnail')) {
                    $flavor->addMedia($thumbPath)
                        ->preservingOriginal()
                        ->toMediaCollection('thumbnail');
                    $this->log('created', "  + Attached thumbnail to {$name}");
                }

                // Shape Pivots
                $shapesSubDir = $dir . '/shapes';
                if (File::exists($shapesSubDir)) {
                    foreach (File::directories($shapesSubDir) as $shapeDir) {
                        $rawShapeName = basename($shapeDir);
                        $shapeName = $this->capitalizeName($rawShapeName);
                        $shape = CakeShape::where('name', $shapeName)->first();

                        if (!$shape) {
                            $this->log('skipped', "  - Flavor-Shape Pivot: Shape '{$shapeName}' missing for Flavor '{$name}'");
                            continue;
                        }

                        $pivot = ShapeFlavor::firstOrCreate([
                            'cake_shape_id' => $shape->id,
                            'cake_flavor_id' => $flavor->id,
                        ], ['extra_price' => $price]);

                        if ($pivot->wasRecentlyCreated) {
                            $this->log('created', "  + Pivot: Flavor '{$name}' for Shape '{$shapeName}'");
                        }

                        // Attach pivot media
                        if (File::exists($shapeDir . '/full.png') && !$pivot->hasMedia('full_image')) {
                            $pivot->addMedia($shapeDir . '/full.png')
                                ->preservingOriginal()
                                ->toMediaCollection('full_image');
                        }
                        if (File::exists($shapeDir . '/cut.png') && !$pivot->hasMedia('cut_image')) {
                            $pivot->addMedia($shapeDir . '/cut.png')
                                ->preservingOriginal()
                                ->toMediaCollection('cut_image');
                        }
                    }
                }
            });
        } catch (\Exception $e) {
            $this->log('error', "Flavor Error ({$name}): " . $e->getMessage());
        }
    }

    public function importToppingCategoryDirectory(string $dir, float $price)
    {
        if (!File::exists($dir))
            return;

        $rawCatName = basename($dir);
        $catName = $this->capitalizeName($rawCatName);

        try {
            DB::transaction(function () use ($catName, $dir, $price) {
                $category = ToppingCategory::firstOrCreate(['name' => $catName]);

                // Group files by their number prefix
                $groups = [];
                foreach (File::files($dir) as $file) {
                    if ($file->getExtension() !== 'png')
                        continue;

                    $filename = pathinfo($file->getFilename(), PATHINFO_FILENAME);
                    $clean = strtolower($filename);
                    $parts = explode('-', $clean);

                    $numberParts = [];
                    foreach ($parts as $part) {
                        if (is_numeric($part)) {
                            $numberParts[] = $part;
                        } else {
                            break;
                        }
                    }
                    $groupKey = implode('-', $numberParts);

                    if ($groupKey !== '') {
                        $groups[$groupKey][] = $file;
                    }
                }

                // Process each group
                foreach ($groups as $groupKey => $files) {
                    $toppingName = $catName . ' ' . $groupKey;

                    $topping = CakeTopping::firstOrCreate([
                        'name' => $toppingName,
                        'topping_category_id' => $category->id,
                    ]);

                    if ($topping->wasRecentlyCreated) {
                        $this->log('created', "Topping: {$toppingName} ({$catName})");
                    } else {
                        $this->log('skipped', "Topping: {$toppingName} (already exists)");
                    }

                    // Use the first file as the topping thumbnail
                    $firstFile = $files[0];
                    if (!$topping->hasMedia('thumbnail')) {
                        $topping->addMedia($firstFile->getPathname())
                            ->preservingOriginal()
                            ->toMediaCollection('thumbnail');
                        $this->log('created', "  + Thumbnail: {$firstFile->getFilename()}");
                    }

                    // Attach to each shape found in the group files
                    foreach ($files as $file) {
                        $filename = pathinfo($file->getFilename(), PATHINFO_FILENAME);
                        $clean = strtolower($filename);
                        $parts = explode('-', $clean);

                        $nonNumericParts = [];
                        $pastNumbers = false;
                        foreach ($parts as $part) {
                            if (!$pastNumbers && is_numeric($part)) {
                                continue;
                            }
                            $pastNumbers = true;
                            $nonNumericParts[] = $part;
                        }

                        $normalizedShapeName = null;
                        $current = '';
                        foreach ($nonNumericParts as $segment) {
                            $current = ($current === '') ? $segment : $current . '-' . $segment;
                            if (in_array($current, $this->validShapes)) {
                                $normalizedShapeName = $current;
                                break;
                            }
                        }

                        if (!$normalizedShapeName)
                            continue;

                        $shapeName = $this->capitalizeName($normalizedShapeName);
                        $shape = CakeShape::where('name', $shapeName)->first();

                        if (!$shape) {
                            $this->log('error', "Shape not found: '{$shapeName}' from file '{$file->getFilename()}'");
                            continue;
                        }

                        $pivot = ShapeTopping::firstOrCreate([
                            'cake_shape_id' => $shape->id,
                            'cake_topping_id' => $topping->id,
                        ], ['price' => $price]);

                        if ($pivot->wasRecentlyCreated) {
                            $this->log('created', "  + Linked {$toppingName} → {$shapeName}");
                        }

                        if (!$pivot->hasMedia('image_layer')) {
                            $pivot->addMedia($file->getPathname())
                                ->preservingOriginal()
                                ->toMediaCollection('image_layer');
                            $this->log('created', "    + Image: {$file->getFilename()}");
                        }
                    }
                }
            });
        } catch (\Exception $e) {
            $this->log('error', "Topping/Category Error ({$catName}): " . $e->getMessage());
        }
    }
}
