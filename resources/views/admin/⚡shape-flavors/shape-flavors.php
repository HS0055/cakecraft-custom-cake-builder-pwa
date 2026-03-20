<?php

use App\Livewire\Traits\HasCrudModal;
use App\Models\CakeFlavor;
use App\Models\CakeShape;
use App\Models\ShapeFlavor;
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
 * Class ShapeFlavors (Livewire 4 Multi-File Component)
 *
 * This Livewire component governs the CRUD interface for managing
 * Shape & Flavor linkage combinations and their respective media sets.
 */
new #[Layout('layouts::admin', ['title' => 'Shape-Flavors'])] class extends Component {
    use WithPagination, WithFileUploads, HasCrudModal;

    #[Validate('required|exists:cake_shapes,id')]
    public string $cake_shape_id = '';

    #[Validate('required|exists:cake_flavors,id')]
    public string $cake_flavor_id = '';

    #[Validate('required|numeric|min:0')]
    public string $price = '0.00';

    #[Validate('nullable|image|max:4096')]
    public $full_image;

    #[Validate('nullable|image|max:4096')]
    public $cut_image;

    public bool $delete_full_image = false;
    public bool $delete_cut_image = false;

    public string $search = '';
    public string $filter_shape_id = '';
    public string $filter_flavor_id = '';
    public int $perPage = 15;

    public function mount(): void
    {
        $this->authorize('view shapes');
        $this->perPage = settings(GeneralSettings::class)->pagination_limit;
    }

    public function openCreate(): void
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function openEdit(int $id): void
    {
        $shapeFlavor = ShapeFlavor::findOrFail($id);
        $this->editingId = $id;
        $this->cake_shape_id = (string) $shapeFlavor->cake_shape_id;
        $this->cake_flavor_id = (string) $shapeFlavor->cake_flavor_id;
        $this->price = (string) $shapeFlavor->extra_price;
        $this->delete_full_image = false;
        $this->delete_cut_image = false;
        $this->showModal = true;
    }

    public function save(): void
    {
        $this->authorize('update shapes');
        $this->validate();

        try {
            DB::transaction(function () {
                $shapeFlavor = $this->editingId ? ShapeFlavor::findOrFail($this->editingId) : new ShapeFlavor();
                $shapeFlavor->cake_shape_id = $this->cake_shape_id;
                $shapeFlavor->cake_flavor_id = $this->cake_flavor_id;
                $shapeFlavor->extra_price = $this->price;
                $shapeFlavor->save();

                if ($this->delete_full_image && !$this->full_image) {
                    $shapeFlavor->clearMediaCollection('full_image');
                } elseif ($this->full_image) {
                    $path = $this->full_image->store('shape-flavors');
                    $shapeFlavor->addMediaFromDisk($path)
                        ->usingFileName($this->full_image->getClientOriginalName())
                        ->toMediaCollection('full_image');
                }

                if ($this->delete_cut_image && !$this->cut_image) {
                    $shapeFlavor->clearMediaCollection('cut_image');
                } elseif ($this->cut_image) {
                    $path = $this->cut_image->store('shape-flavors');
                    $shapeFlavor->addMediaFromDisk($path)
                        ->usingFileName($this->cut_image->getClientOriginalName())
                        ->toMediaCollection('cut_image');
                }
            });

            $this->showModal = false;
            session()->flash('success', $this->editingId ? __('admin.shape_flavors.updated_successfully') : __('admin.shape_flavors.created_successfully'));
            $this->resetForm();
        } catch (\Exception $e) {
            Log::error('Failed to save shape-flavor combo: ' . $e->getMessage());
            session()->flash('error', __('admin.shape_flavors.error_saving'));
        }
    }

    public function confirmDelete(int $id): void
    {
        $this->authorize('update shapes');
        $this->deletingId = $id;
        $this->showDeleteModal = true;
    }

    public function delete(): void
    {
        $this->authorize('update shapes');

        try {
            DB::transaction(function () {
                ShapeFlavor::findOrFail($this->deletingId)->delete();
            });

            $this->showDeleteModal = false;
            $this->deletingId = null;
            session()->flash('success', __('admin.shape_flavors.deleted_successfully'));
        } catch (\Exception $e) {
            Log::error('Failed to delete shape-flavor combo: ' . $e->getMessage());
            session()->flash('error', __('admin.shape_flavors.error_deleting'));
        }
    }

    public function resetForm(): void
    {
        $this->resetCrudState();
        $this->cake_shape_id = '';
        $this->cake_flavor_id = '';
        $this->price = '0.00';
        $this->full_image = null;
        $this->cut_image = null;
        $this->delete_full_image = false;
        $this->delete_cut_image = false;
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingFilterShapeId(): void
    {
        $this->resetPage();
    }

    public function updatingFilterFlavorId(): void
    {
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->search = '';
        $this->filter_shape_id = '';
        $this->filter_flavor_id = '';
        $this->resetPage();
    }

    /**
     * Provide component state exclusively to the view file using Livewire 4 array mapping.
     *
     * @return array{combos: LengthAwarePaginator, shapes: mixed, flavors: mixed}
     */
    public function with(): array
    {
        return [
            'combos' => ShapeFlavor::with(['shape', 'flavor'])
                ->when($this->search, fn($query) => $query->whereHas('shape', fn($subQuery) => $subQuery->where('name', 'like', "%{$this->search}%"))
                    ->orWhereHas('flavor', fn($subQuery) => $subQuery->where('name', 'like', "%{$this->search}%")))
                ->when($this->filter_shape_id, fn($query) => $query->where('cake_shape_id', $this->filter_shape_id))
                ->when($this->filter_flavor_id, fn($query) => $query->where('cake_flavor_id', $this->filter_flavor_id))
                ->latest()
                ->paginate($this->perPage),
            'shapes' => CakeShape::orderBy('name')->get(),
            'flavors' => CakeFlavor::orderBy('name')->get(),
        ];
    }
};