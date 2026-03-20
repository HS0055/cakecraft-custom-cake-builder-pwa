<?php

use App\Livewire\Traits\HasCrudModal;
use App\Models\CakeTopping;
use App\Models\ToppingCategory;
use App\Settings\GeneralSettings;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Class CakeToppings (Livewire 4 Multi-File Component)
 *
 * This Livewire component governs the CRUD interface for managing
 * Cake Toppings and their associated category/media logic in the admin portal.
 */
new #[Layout('layouts::admin', ['title' => 'Cake Toppings'])] class extends Component {
    use WithPagination, WithFileUploads, HasCrudModal;

    #[Validate('required|string|max:255')]
    public string $name = '';

    #[Validate('nullable|exists:topping_categories,id')]
    public ?int $topping_category_id = null;

    #[Validate('nullable|image|max:2048')]
    public $thumbnail;

    public bool $delete_thumbnail = false;
    public string $search = '';
    public string $filter_category_id = '';
    public int $perPage = 15;

    public function mount(): void
    {
        $this->authorize('view toppings');
        $this->perPage = settings(GeneralSettings::class)->pagination_limit;
    }

    public function openCreate(): void
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function openEdit(int $id): void
    {
        $topping = CakeTopping::findOrFail($id);
        $this->editingId = $id;
        $this->name = $topping->name;
        $this->topping_category_id = $topping->topping_category_id;
        $this->delete_thumbnail = false;
        $this->showModal = true;
    }

    public function save(): void
    {
        $this->authorize($this->editingId ? 'update toppings' : 'create toppings');
        $this->validate();

        try {
            DB::transaction(function () {
                $topping = $this->editingId ? CakeTopping::findOrFail($this->editingId) : new CakeTopping();
                $topping->name = $this->name;
                $topping->topping_category_id = $this->topping_category_id;
                $topping->save();

                if ($this->delete_thumbnail && !$this->thumbnail) {
                    $topping->clearMediaCollection('thumbnail');
                } elseif ($this->thumbnail) {
                    $path = $this->thumbnail->store('cake-toppings');
                    $topping->addMediaFromDisk($path)
                        ->usingFileName($this->thumbnail->getClientOriginalName())
                        ->toMediaCollection('thumbnail');
                }
            });

            $this->showModal = false;
            session()->flash('success', $this->editingId ? __('admin.cake_toppings.updated_successfully') : __('admin.cake_toppings.created_successfully'));
            $this->resetForm();
        } catch (\Exception $e) {
            Log::error('Failed to save topping: ' . $e->getMessage());
            session()->flash('error', __('admin.cake_toppings.error_saving'));
        }
    }

    public function confirmDelete(int $id): void
    {
        $this->authorize('delete toppings');
        $this->deletingId = $id;
        $this->showDeleteModal = true;
    }

    public function delete(): void
    {
        $this->authorize('delete toppings');

        try {
            DB::transaction(function () {
                CakeTopping::findOrFail($this->deletingId)->delete();
            });

            $this->showDeleteModal = false;
            $this->deletingId = null;
            session()->flash('success', __('admin.cake_toppings.deleted_successfully'));
        } catch (\Exception $e) {
            Log::error('Failed to delete topping: ' . $e->getMessage());
            session()->flash('error', __('admin.cake_toppings.error_deleting'));
        }
    }

    public function resetForm()
    {
        $this->resetCrudState();
        $this->name = '';
        $this->topping_category_id = null;
        $this->thumbnail = null;
        $this->delete_thumbnail = false;
        $this->resetValidation();
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingFilterCategoryId(): void
    {
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->search = '';
        $this->filter_category_id = '';
        $this->resetPage();
    }

    /**
     * Provide component state exclusively to the view file using Livewire 4 array mapping.
     *
     * @return array{toppings: LengthAwarePaginator, categories: mixed}
     */
    public function with(): array
    {
        return [
            'toppings' => CakeTopping::query()
                ->with('category')
                ->when($this->search, fn($query) => $query->where('name', 'like', "%{$this->search}%"))
                ->when($this->filter_category_id, fn($query) => $query->where('topping_category_id', $this->filter_category_id))
                ->latest()
                ->paginate($this->perPage),
            'categories' => ToppingCategory::all(),
        ];
    }
};