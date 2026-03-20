<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

/**
 * Class Header (Livewire 4 Component)
 *
 * This component manages the global admin header, including user session termination (logout),
 * cache management, and application navigation shortcuts. Built with 10/10 Livewire 4 architecture.
 */
new class extends Component {
    public string $title = 'Dashboard';

    public function with(): array
    {
        return [
            'activeLanguages' => \App\Models\Language::where('is_active', true)->get(),
        ];
    }

    public function logout(): void
    {
        try {
            Auth::logout();
            session()->invalidate();
            session()->regenerateToken();
            $this->redirect(route('login'), navigate: true);
        } catch (\Exception $e) {
            Log::error('Logout failed: ' . $e->getMessage());
            $this->redirect(route('login')); // Fallback redirect
        }
    }

    public function clearCache(): void
    {
        $this->authorize('view settings'); // Restrict cache clearing to admins

        try {
            Artisan::call('cache:clear');
            Artisan::call('view:clear');
            Artisan::call('config:clear');

            session()->flash('success', __('admin.header.cache_cleared_successfully'));
        } catch (\Exception $e) {
            Log::error('Cache clear failed: ' . $e->getMessage());
            session()->flash('error', __('admin.header.cache_clear_error'));
        }

        $this->redirect(request()->header('Referer') ?? route('admin.dashboard'), navigate: true);
    }
};