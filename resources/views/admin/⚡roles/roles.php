<?php

use App\Livewire\Traits\HasCrudModal;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

/**
 * Class Roles (Livewire 4 Component)
 *
 * This component manages the display, creation, updating, and deletion of Roles and their associated Permissions.
 * It uses the `with(): array` pattern and database transactions to ensure data integrity during role and permission syncing.
 */
new #[Layout('layouts::admin', ['title' => 'Role Management'])] class extends Component {
    use WithPagination, HasCrudModal;

    public string $name = '';
    public array $selectedPermissions = [];

    protected $rules = [
        'name' => 'required|string|max:255|unique:roles,name',
    ];

    public function mount()
    {
        $this->authorize('view roles');
    }

    public function openCreate()
    {
        $this->authorize('create roles');
        $this->resetForm();
        $this->showModal = true;
    }

    public function openEdit(int $id)
    {
        $this->authorize('update roles');
        $this->resetForm();
        $role = Role::findOrFail($id);

        if ($role->name === 'admin') {
            session()->flash('error', __('admin.roles.cannot_edit_admin'));
            return;
        }

        $this->editingId = $id;
        $this->name = $role->name;
        $this->selectedPermissions = $role->permissions->pluck('name')->toArray();
        $this->showModal = true;
    }

    public function save()
    {
        $this->authorize($this->editingId ? 'update roles' : 'create roles');

        $rules = $this->rules;
        if ($this->editingId) {
            $rules['name'] = 'required|string|max:255|unique:roles,name,' . $this->editingId;
        }

        $this->validate($rules);

        if ($this->editingId && Role::find($this->editingId)?->name === 'admin') {
            session()->flash('error', __('admin.roles.cannot_edit_admin'));
            return;
        }

        try {
            DB::transaction(function () {
                $role = $this->editingId ? Role::findOrFail($this->editingId) : Role::create(['name' => $this->name, 'guard_name' => 'web']);

                if ($this->editingId) {
                    $role->name = $this->name;
                    $role->save();
                }

                $role->syncPermissions($this->selectedPermissions);
            });

            $this->showModal = false;
            session()->flash('success', $this->editingId ? __('admin.roles.updated_successfully') : __('admin.roles.created_successfully'));
            $this->resetForm();

        } catch (\Exception $e) {
            Log::error('Failed to save role: ' . $e->getMessage());
            $this->addError('system', 'A critical error occurred while saving the role.');
        }
    }

    public function confirmDelete(int $id)
    {
        $this->authorize('delete roles');
        $role = Role::findOrFail($id);

        if ($role->name === 'admin') {
            session()->flash('error', __('admin.roles.cannot_delete_admin'));
            return;
        }

        $this->deletingId = $id;
        $this->showDeleteModal = true;
    }

    public function delete()
    {
        $this->authorize('delete roles');
        $role = Role::findOrFail($this->deletingId);

        if ($role->name === 'admin') {
            session()->flash('error', __('admin.roles.cannot_delete_admin'));
            $this->showDeleteModal = false;
            return;
        }

        try {
            DB::transaction(function () use ($role) {
                $role->delete();
            });

            $this->showDeleteModal = false;
            $this->deletingId = null;
            session()->flash('success', __('admin.roles.deleted_successfully'));

        } catch (\Exception $e) {
            Log::error('Failed to delete role: ' . $e->getMessage());
            $this->showDeleteModal = false;
            session()->flash('error', __('admin.roles.error_deleting'));
        }
    }

    public function resetForm()
    {
        $this->resetCrudState();
        $this->name = '';
        $this->selectedPermissions = [];
    }

    public function getPermissionsProperty()
    {
        return Permission::orderBy('name')->get()->groupBy(function ($permission) {
            $parts = explode(' ', $permission->name);
            return count($parts) > 1 ? end($parts) : 'system';
        });
    }

    public function with(): array
    {
        return [
            'roles' => Role::withCount('permissions')->orderBy('name')->paginate(10),
            'groupedPermissions' => $this->permissions,
        ];
    }
};
