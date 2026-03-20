<?php

namespace App\Services;

use Spatie\Image\Image;
use Spatie\Image\Enums\Fit;
use Illuminate\Support\Facades\Log;

class PwaIconGenerator
{
    protected static array $iconSizes = [72, 96, 128, 144, 152, 192, 384, 512];

    /**
     * Generate all PWA icon sizes from the uploaded logo.
     */
    public static function generateFromLogo(string $sourcePath): bool
    {
        $outputDir = public_path('images/icons');

        if (!file_exists($sourcePath)) {
            Log::warning('PwaIconGenerator: Source logo not found', ['path' => $sourcePath]);
            return false;
        }

        $generated = 0;

        foreach (self::$iconSizes as $size) {
            $outputPath = $outputDir . "/icon-{$size}x{$size}.png";

            try {
                Image::load($sourcePath)
                    ->fit(Fit::Contain, $size, $size)
                    ->save($outputPath);
                $generated++;
            } catch (\Throwable $e) {
                Log::warning("PwaIconGenerator: Failed to generate {$size}x{$size}", [
                    'error' => $e->getMessage(),
                ]);
            }
        }

        if ($generated > 0) {
            self::bumpServiceWorkerVersion();
        }

        Log::info("PwaIconGenerator: Generated {$generated}/" . count(self::$iconSizes) . " PWA icons");

        return $generated > 0;
    }

    /**
     * Copy/convert the uploaded favicon to public/favicon.ico and update fallback logos.
     */
    public static function updateFavicon(string $sourcePath): bool
    {
        if (!file_exists($sourcePath)) {
            Log::warning('PwaIconGenerator: Source favicon not found', ['path' => $sourcePath]);
            return false;
        }

        try {
            // Generate a 32x32 PNG favicon (modern browsers accept PNG)
            Image::load($sourcePath)
                ->fit(Fit::Contain, 32, 32)
                ->save(public_path('favicon.ico'));

            // Update the fallback logo images used when no branding URL is set
            Image::load($sourcePath)
                ->fit(Fit::Contain, 180, 180)
                ->save(public_path('images/logo.png'));

            Image::load($sourcePath)
                ->fit(Fit::Contain, 180, 180)
                ->save(public_path('logo.png'));

            self::bumpServiceWorkerVersion();

            Log::info('PwaIconGenerator: Updated favicon and fallback logos');
            return true;
        } catch (\Throwable $e) {
            Log::warning('PwaIconGenerator: Failed to update favicon', [
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Rewrite serviceworker.js with a new version constant to force browser re-install.
     */
    public static function bumpServiceWorkerVersion(): void
    {
        $swPath = public_path('serviceworker.js');

        if (!file_exists($swPath)) {
            return;
        }

        $version = time();

        $content = <<<JS
var CACHE_VERSION = '{$version}';
var staticCacheName = "pwa-v" + CACHE_VERSION;
var filesToCache = [
    '/offline/',
    '/images/icons/icon-72x72.png',
    '/images/icons/icon-96x96.png',
    '/images/icons/icon-128x128.png',
    '/images/icons/icon-144x144.png',
    '/images/icons/icon-152x152.png',
    '/images/icons/icon-192x192.png',
    '/images/icons/icon-384x384.png',
    '/images/icons/icon-512x512.png',
];

// Cache on install
self.addEventListener("install", event => {
    event.waitUntil(
        caches.open(staticCacheName)
            .then(cache => {
                return cache.addAll(filesToCache);
            })
            .then(() => self.skipWaiting())
    );
});

// Clear old caches on activate and take control immediately
self.addEventListener('activate', event => {
    event.waitUntil(
        caches.keys().then(cacheNames => {
            return Promise.all(
                cacheNames
                    .filter(cacheName => (cacheName.startsWith("pwa-")))
                    .filter(cacheName => (cacheName !== staticCacheName))
                    .map(cacheName => caches.delete(cacheName))
            );
        }).then(() => self.clients.claim())
    );
});

// Serve from Cache — skip non-GET and dynamic routes (Livewire, API, etc.)
self.addEventListener("fetch", event => {
    if (event.request.method !== 'GET') return;

    const url = new URL(event.request.url);
    if (url.pathname.startsWith('/livewire')) return;
    if (url.pathname.startsWith('/api')) return;

    event.respondWith(
        caches.match(event.request)
            .then(response => {
                return response || fetch(event.request);
            })
            .catch(() => {
                return caches.match('/offline/').then(r => r || new Response('Offline', { status: 503 }));
            })
    );
});
JS;

        file_put_contents($swPath, $content);
        Log::info('PwaIconGenerator: Service worker version bumped', ['version' => $version]);
    }
}
