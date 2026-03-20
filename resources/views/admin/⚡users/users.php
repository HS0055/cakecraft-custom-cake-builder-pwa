<?php

use App\Livewire\Traits\HasCrudModal;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role;

/**
 * Class Users (Livewire 4 Component)
 *
 * This component manages the display, creation, updating, blocking, and deletion of Users.
 * It utilizes the `with(): array` pattern for efficient data rendering and ensures data
 * integrity by encapsulating modifications within database transactions. Includes Spatie Role mappings.
 */
new #[Layout('layouts::admin', ['title' => 'User Management'])] class extends Component {
    use WithPagination, HasCrudModal;

    public string $name = '';
    public string $email = '';
    public string $password = '';
    public array $selectedRoles = [];
    public bool $is_blocked = false;

    public string $search = '';

    protected function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $this->editingId,
            'password' => $this->editingId ? 'nullable|min:6' : 'required|min:6',
            'selectedRoles' => 'required|array|min:1',
            'is_blocked' => 'boolean',
        ];
    }

    public function mount()
    {
        $this->authorize('view users');
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function openCreate()
    {
        $this->authorize('create users');
        $this->resetForm();
        $this->showModal = true;
    }

    public function openEdit(int $id)
    {
        $this->authorize('update users');
        $this->resetForm();
        $user = User::findOrFail($id);
        $this->editingId = $id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->is_blocked = $user->is_blocked;
        $this->selectedRoles = $user->roles->pluck('name')->toArray();
        $this->showModal = true;
    }

    public function save()
    {
        $this->authorize($this->editingId ? 'update users' : 'create users');
        $this->validate();

        // Safety: Non-admins cannot assign the admin role
        if (!auth()->user()->hasRole('admin') && in_array('admin', $this->selectedRoles)) {
            $this->addError('selectedRoles', 'You do not have permission to assign the admin role.');
            return;
        }

        try {
            DB::transaction(function () {
                $user = $this->editingId ? User::findOrFail($this->editingId) : new User();
                $user->name = $this->name;
                $user->email = $this->email;
                $user->is_blocked = $this->is_blocked;

                if ($this->password) {
                    $user->password = Hash::make($this->password);
                }

                $user->save();
                $user->syncRoles($this->selectedRoles);
            });

            $this->showModal = false;
            session()->flash('success', $this->editingId ? __('admin.users.updated_successfully') : __('admin.users.created_successfully'));
            $this->resetForm();

        } catch (\Exception $e) {
            Log::error('Failed to save user: ' . $e->getMessage());
            $this->addError('system', 'A critical error occurred while saving the user.');
        }
    }

    public function toggleBlock(int $id)
    {
        $this->authorize('update users');
        $user = User::findOrFail($id);

        if ($user->id === auth()->id()) {
            session()->flash('error', __('admin.users.cannot_block_self'));
            return;
        }

        if ($user->hasRole('admin')) {
            session()->flash('error', __('admin.users.cannot_block_admin'));
            return;
        }

        try {
            DB::transaction(function () use ($user) {
                $user->is_blocked = !$user->is_blocked;
                $user->save();
            });

            session()->flash('success', $user->is_blocked ? __('admin.users.block_success') : __('admin.users.unblock_success'));
        } catch (\Exception $e) {
            Log::error('Failed to toggle block status: ' . $e->getMessage());
            session()->flash('error', __('admin.users.block_error'));
        }
    }

    public function confirmDelete(int $id)
    {
        $this->authorize('delete users');
        $user = User::findOrFail($id);

        if ($user->id === auth()->id()) {
            session()->flash('error', __('admin.users.cannot_delete_self'));
            return;
        }

        if ($user->hasRole('admin')) {
            session()->flash('error', __('admin.users.cannot_delete_admin'));
            return;
        }

        $this->deletingId = $id;
        $this->showDeleteModal = true;
    }

    public function delete()
    {
        $this->authorize('delete users');
        $user = User::findOrFail($this->deletingId);

        if ($user->id === auth()->id() || $user->hasRole('admin')) {
            $this->showDeleteModal = false;
            return;
        }

        try {
            DB::transaction(function () use ($user) {
                $user->delete();
            });

            $this->showDeleteModal = false;
            $this->deletingId = null;
            session()->flash('success', __('admin.users.deleted_successfully'));

        } catch (\Exception $e) {
            Log::error('Failed to delete user: ' . $e->getMessage());
            $this->showDeleteModal = false;
            session()->flash('error', __('admin.users.error_deleting'));
        }
    }

    public function resetForm()
    {
        $this->resetCrudState();
        $this->name = '';
        $this->email = '';
        $this->password = '';
        $this->selectedRoles = [];
        $this->is_blocked = false;
    }

    public function with(): array
    {
        return [
            'users' => User::with('roles')
                ->when($this->search, function ($query) {
                    $query->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('email', 'like', '%' . $this->search . '%');
                })
                ->orderBy('name')
                ->paginate(10),
            'roles' => Role::orderBy('name')->get(),
        ];
    }
};
