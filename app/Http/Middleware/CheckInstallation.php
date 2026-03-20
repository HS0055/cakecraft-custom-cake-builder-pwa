<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckInstallation
{
    /**
     * Handle an incoming request.
     *
     * Uses a file-based sentinel (storage/installed) instead of env()
     * because env() is unreliable when config is cached in production.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $isInstalled = file_exists(storage_path('installed'));

        // If already installed, block access to the installer
        if ($isInstalled) {
            if ($request->is('install') || $request->is('install/*')) {
                return redirect('/');
            }
            return $next($request);
        }

        // Not installed: allow installer and livewire routes through
        if (
            $request->is('install') ||
            $request->is('install/*') ||
            $request->routeIs('livewire.*') ||
            str_starts_with($request->path(), 'livewire')
        ) {
            return $next($request);
        }

        // Redirect everything else to the installer
        return redirect()->route('install');
    }
}
