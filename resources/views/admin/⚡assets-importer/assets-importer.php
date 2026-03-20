<?php

use App\Models\CakeFlavor;
use App\Models\CakeShape;
use App\Models\CakeTopping;
use App\Models\ToppingCategory;
use App\Models\ShapeFlavor;
use App\Models\ShapeTopping;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Layout;
use Livewire\Component;

/**
 * Class AssetsImporter (Livewire 4 Component)
 *
 * This component provides an interface for mass-importing base images, thumbnails,
 * and vector resources for CakeShapes, CakeFlavors, and CakeToppings from the public directory.
 * It is built to safely scan local directories and trigger the robust ImportAssetsAction.
 */
new #[Layout('layouts::admin', ['title' => 'Assets Importer'])] class extends Component {
    public string $baseFolder = 'assets';

    public $counts = [
        'shapes' => 0,
        'flavors' => 0,
        'toppings' => 0,
    ];

    public $defaultShapePrice = 0;
    public $defaultFlavorPrice = 0;
    public $defaultToppingPrice = 0;

    public $results = [
        'created' => [],
        'skipped' => [],
        'errors' => [],
        'info' => [],
    ];

    public array $importQueue = [];
    public int $importQueueTotal = 0;
    public int $importQueueProcessed = 0;

    public $scanned = false;
    public $importing = false;

    public function getBaseDirProperty(): string
    {
        return public_path($this->baseFolder);
    }

    public function scan()
    {
        $this->authorize('import assets');

        $this->reset(['counts', 'results', 'importQueue', 'importQueueTotal', 'importQueueProcessed']);
        $baseDir = $this->baseDir;

        if (!$this->baseFolder || !File::exists($baseDir)) {
            $this->results['errors'][] = "Source directory public/{$this->baseFolder}/ does not exist.";
            return;
        }

        // Scan Shapes
        $shapesDir = $baseDir . '/shapes';
        if (File::exists($shapesDir)) {
            $dirs = File::directories($shapesDir);
            $this->counts['shapes'] = count($dirs);
            foreach ($dirs as $dir) {
                $this->importQueue[] = ['type' => 'shape', 'path' => $dir];
            }
        }

        // Scan Flavors
        $flavorsDir = $baseDir . '/flavors';
        if (File::exists($flavorsDir)) {
            $dirs = File::directories($flavorsDir);
            $this->counts['flavors'] = count($dirs);
            foreach ($dirs as $dir) {
                $this->importQueue[] = ['type' => 'flavor', 'path' => $dir];
            }
        }

        // Scan Toppings (count unique toppings by category dir instead of individual prefix)
        // Since we refactored the Action to process the whole category directory at once:
        $toppingsDir = $baseDir . '/toppings';
        if (File::exists($toppingsDir)) {
            $dirs = File::directories($toppingsDir);
            $this->counts['toppings'] = count($dirs); // Counting categories here! The exact count isn't as easily known until parsing.
            foreach ($dirs as $dir) {
                $this->importQueue[] = ['type' => 'topping', 'path' => $dir];
            }
        }

        $this->importQueueTotal = count($this->importQueue);
        $this->scanned = true;
    }

    public function startImport()
    {
        $this->authorize('import assets');

        if (empty($this->importQueue)) {
            $this->results['errors'][] = "Nothing to import. Please scan first.";
            return;
        }

        $this->importing = true;
        $this->reset('results');
        $this->importQueueProcessed = 0;

        $this->logMessage('info', '🚀 Starting import from public/' . $this->baseFolder . '/');

        // Dispatch an event to immediately trigger the first batch on the frontend
        $this->dispatch('continue-import');
    }

    public function processNextBatch(\App\Actions\System\ImportAssetsAction $importer)
    {
        if (!$this->importing || empty($this->importQueue)) {
            $this->finishImport();
            return;
        }

        // Pop one directory off the queue to process in this request
        $item = array_shift($this->importQueue);

        $importer->setLogger(function ($type, $message) {
            $this->logMessage($type, $message);
        });

        try {
            switch ($item['type']) {
                case 'shape':
                    $importer->importShapeDirectory($item['path'], $this->defaultShapePrice);
                    break;
                case 'flavor':
                    $importer->importFlavorDirectory($item['path'], $this->defaultFlavorPrice);
                    break;
                case 'topping':
                    $importer->importToppingCategoryDirectory($item['path'], $this->defaultToppingPrice);
                    break;
            }
        } catch (\Exception $e) {
            $this->logMessage('error', 'Error processing ' . basename($item['path']) . ': ' . $e->getMessage());
            Log::error($e);
        }

        $this->importQueueProcessed++;

        // If there's more to do, tell the browser to make another request
        if (!empty($this->importQueue)) {
            $this->dispatch('continue-import');
        } else {
            $this->finishImport();
        }
    }

    private function finishImport()
    {
        $created = count($this->results['created']);
        $skipped = count($this->results['skipped']);
        $errors = count($this->results['errors']);
        $this->logMessage('info', "✅ Import complete — {$created} created, {$skipped} skipped, {$errors} errors");

        $this->importing = false;
    }

    /**
     * Store logs in results. (We can no longer use wire:stream for sequential chunks)
     */
    private function logMessage(string $type, string $message): void
    {
        // Store in results array for persistency after Livewire refresh
        $key = match ($type) {
            'error', 'danger' => 'errors',
            'created', 'success' => 'created',
            'skipped', 'warning' => 'skipped',
            default => 'info'
        };
        $this->results[$key][] = $message;
    }

};
