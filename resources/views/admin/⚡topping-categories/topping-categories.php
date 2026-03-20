<?php

use App\Livewire\Traits\HasCrudModal;
use App\Models\ToppingCategory;
use App\Settings\GeneralSettings;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Class ToppingCategories (Livewire 4 Multi-File Component)
 *
 * This Livewire component governs the CRUD interface for managing
 * Topping Categories within the admin portal.
 */
new #[Layout('layouts::admin', ['title' => 'Topping Categories'])] class extends Component {
    use WithPagination, HasCrudModal;

    #[Validate('required|string|max:255|unique:topping_categories,name')]
    public string $name = '';

    public string $search = '';

    public int $perPage = 15;

    public function mount(): void
    {
        $this->authorize('view topping categories');
        $this->perPage = settings(GeneralSettings::class)->pagination_limit;
    }

    public function openCreate(): void
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function openEdit(int $id): void
    {
        $category = ToppingCategory::findOrFail($id);
        $this->editingId = $id;
        $this->name = $category->name;
        $this->showModal = true;
    }

    public function save(): void
    {
        $this->authorize($this->editingId ? 'update topping categories' : 'create topping categories');

        $this->validate([
            'name' => 'required|string|max:255|unique:topping_categories,name,' . $this->editingId,
        ]);

        try {
            DB::transaction(function () {
                if ($this->editingId) {
                    $category = ToppingCategory::findOrFail($this->editingId);
                    $category->update(['name' => $this->name]);
                } else {
                    ToppingCategory::create(['name' => $this->name]);
                }
            });

            $this->showModal = false;
            session()->flash('success', $this->editingId ? __('admin.topping_categories.updated_successfully') : __('admin.topping_categories.created_successfully'));
            $this->resetForm();
        } catch (\Exception $e) {
            Log::error('Failed to save topping category: ' . $e->getMessage());
            session()->flash('error', __('admin.topping_categories.error_saving'));
        }
    }

    public function confirmDelete(int $id): void
    {
        $this->authorize('delete topping categories');
        $this->deletingId = $id;
        $this->showDeleteModal = true;
    }

    public function delete(): void
    {
        $this->authorize('delete topping categories');

        try {
            DB::transaction(function () {
                ToppingCategory::findOrFail($this->deletingId)->delete();
            });

            $this->showDeleteModal = false;
            $this->deletingId = null;
            session()->flash('success', __('admin.topping_categories.deleted_successfully'));
        } catch (\Exception $e) {
            Log::error('Failed to delete topping category: ' . $e->getMessage());
            session()->flash('error', __('admin.topping_categories.error_deleting'));
        }
    }

    public function resetForm(): void
    {
        $this->resetCrudState();
        $this->name = '';
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    /**
     * Provide component state exclusively to the view file using Livewire 4 array mapping.
     *
     * @return array{categories: LengthAwarePaginator}
     */
    public function with(): array
    {
        return [
            'categories' => ToppingCategory::query()
                ->when($this->search, fn($query) => $query->where('name', 'like', "%{$this->search}%"))
                ->latest()
                ->paginate($this->perPage),
        ];
    }
};
