<?php

namespace App\Console\Commands;

use App\Models\System;
use App\Services\PwaIconGenerator;
use Illuminate\Console\Command;

class RegeneratePwaIcons extends Command
{
    protected $signature = 'pwa:regenerate-icons';
    protected $description = 'Regenerate PWA icons and favicon from the current branding logo';

    public function handle(): int
    {
        $system = System::first();

        if (!$system) {
            $this->error('No System record found. Upload a logo via Admin Settings first.');
            return self::FAILURE;
        }

        $logoMedia = $system->getFirstMedia('logo');
        $faviconMedia = $system->getFirstMedia('favicon');

        $success = false;

        if ($logoMedia && file_exists($logoMedia->getPath())) {
            $this->info("Generating PWA icons from: {$logoMedia->file_name}");
            if (PwaIconGenerator::generateFromLogo($logoMedia->getPath())) {
                $this->info('PWA icons regenerated successfully.');
                $success = true;
            } else {
                $this->warn('Failed to generate some PWA icons. Check logs.');
            }
        } else {
            $this->warn('No logo found in media library. Upload one in Admin > Settings > Branding.');
        }

        if ($faviconMedia && file_exists($faviconMedia->getPath())) {
            $this->info("Updating favicon from: {$faviconMedia->file_name}");
            if (PwaIconGenerator::updateFavicon($faviconMedia->getPath())) {
                $this->info('Favicon updated successfully.');
                $success = true;
            } else {
                $this->warn('Failed to update favicon. Check logs.');
            }
        } elseif ($logoMedia && file_exists($logoMedia->getPath())) {
            $this->info('No separate favicon found. Using logo for favicon.');
            if (PwaIconGenerator::updateFavicon($logoMedia->getPath())) {
                $this->info('Favicon updated from logo successfully.');
                $success = true;
            }
        }

        if ($success) {
            $this->info('Service worker cache version bumped.');
            $this->newLine();
            $this->info('Users may need to clear browser cache or reinstall the PWA to see changes.');
        }

        return $success ? self::SUCCESS : self::FAILURE;
    }
}
