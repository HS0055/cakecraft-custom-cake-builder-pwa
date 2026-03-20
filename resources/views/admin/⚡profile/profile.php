<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Component;

/**
 * Class Profile (Livewire 4 Component)
 *
 * Provides a user interface for the authenticated user to update their name, email, and password.
 * Uses 10/10 Architecture standards including `with(): array` and robust transactions.
 */
new #[Layout('layouts::admin', ['title' => 'My Profile'])] class extends Component {
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    public function mount()
    {
        $user = Auth::user();
        $this->name = $user->name;
        $this->email = $user->email;
    }

    public function save()
    {
        $user = Auth::user();

        $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => ['nullable', 'string', 'min:6', 'confirmed'],
        ]);

        try {
            DB::transaction(function () use ($user) {
                $user->name = $this->name;
                $user->email = $this->email;

                if (!empty($this->password)) {
                    $user->password = Hash::make($this->password);
                }

                $user->save();
            });

            // Reset password fields after successful save
            $this->password = '';
            $this->password_confirmation = '';

            session()->flash('success', __('admin.profile.updated_successfully'));
        } catch (\Exception $e) {
            Log::error('Profile update failed: ' . $e->getMessage());
            $this->addError('system', 'Failed to update profile. Please try again.');
        }
    }

    public function with(): array
    {
        return [
            'user' => Auth::user(),
        ];
    }
};
