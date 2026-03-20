<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckSystemMaintenance
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Skip during installation — settings table doesn't exist yet
        if (!file_exists(storage_path('installed'))) {
            return $next($request);
        }

        try {
            $settings = app(\App\Settings\SystemSettings::class);
        } catch (\Throwable $e) {
            return $next($request);
        }

        if ($settings->maintenance_mode) {
            $user = $request->user();

            // Bypass for login and admin routes to allow them to login
            if ($request->routeIs('login', 'logout', 'admin.*', 'livewire.*', 'default-livewire.*')) {
                return $next($request);
            }

            // If user is logged in and is an admin, allow through
            if ($user && $user->hasRole('admin')) {
                return $next($request);
            }

            abort(503, 'System is under maintenance.');
        }

        return $next($request);
    }
}
