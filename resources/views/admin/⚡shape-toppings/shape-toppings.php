<?php

use App\Livewire\Traits\HasCrudModal;
use App\Models\CakeShape;
use App\Models\CakeTopping;
use App\Models\ShapeTopping;
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
 * Class ShapeToppings (Livewire 4 Multi-File Component)
 *
 * This Livewire component governs the CRUD interface for managing
 * Shape & Topping linkage combinations and their respective media sets.
 */
new #[Layout('layouts::admin', ['title' => 'Shape-Toppings'])] class extends Component {
    use WithPagination, WithFileUploads, HasCrudModal;

    #[Validate('required|exists:cake_shapes,id')]
    public string $cake_shape_id = '';

    #[Validate('required|exists:cake_toppings,id')]
    public string $cake_topping_id = '';

    #[Validate('required|numeric|min:0')]
    public string $price = '0.00';

    #[Validate('nullable|image|max:4096')]
    public $image_layer;

    public bool $delete_image_layer = false;

    public string $search = '';
    public string $filter_shape_id = '';
    public string $filter_topping_category_id = '';
    public string $filter_topping_id = '';
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
        $shapeTopping = ShapeTopping::findOrFail($id);
        $this->editingId = $id;
        $this->cake_shape_id = (string) $shapeTopping->cake_shape_id;
        $this->cake_topping_id = (string) $shapeTopping->cake_topping_id;

        $this->price = (string) $shapeTopping->price;
        $this->delete_image_layer = false;
        $this->showModal = true;
    }

    public function save(): void
    {
        $this->authorize('update shapes');
        $this->validate();

        try {
            DB::transaction(function () {
                $shapeTopping = $this->editingId ? ShapeTopping::findOrFail($this->editingId) : new ShapeTopping();
                $shapeTopping->cake_shape_id = $this->cake_shape_id;
                $shapeTopping->cake_topping_id = $this->cake_topping_id;
                $shapeTopping->price = $this->price;
                $shapeTopping->save();

                if ($this->delete_image_layer && !$this->image_layer) {
                    $shapeTopping->clearMediaCollection('image_layer');
                } elseif ($this->image_layer) {
                    $path = $this->image_layer->store('shape-toppings');
                    $shapeTopping->addMediaFromDisk($path)
                        ->usingFileName($this->image_layer->getClientOriginalName())
                        ->toMediaCollection('image_layer');
                }
            });

            $this->showModal = false;
            session()->flash('success', $this->editingId ? __('admin.shape_toppings.updated_successfully') : __('admin.shape_toppings.created_successfully'));
            $this->resetForm();
        } catch (\Exception $e) {
            Log::error('Failed to save shape-topping combo: ' . $e->getMessage());
            session()->flash('error', __('admin.shape_toppings.error_saving'));
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
                ShapeTopping::findOrFail($this->deletingId)->delete();
            });

            $this->showDeleteModal = false;
            $this->deletingId = null;
            session()->flash('success', __('admin.shape_toppings.deleted_successfully'));
        } catch (\Exception $e) {
            Log::error('Failed to delete shape-topping combo: ' . $e->getMessage());
            session()->flash('error', __('admin.shape_toppings.error_deleting'));
        }
    }

    public function resetForm(): void
    {
        $this->resetCrudState();
        $this->cake_shape_id = '';
        $this->cake_topping_id = '';
        $this->price = '0.00';
        $this->image_layer = null;
        $this->delete_image_layer = false;
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingFilterShapeId(): void
    {
        $this->resetPage();
    }

    public function updatingFilterToppingCategoryId(): void
    {
        $this->filter_topping_id = '';
        $this->resetPage();
    }

    public function updatingFilterToppingId(): void
    {
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->search = '';
        $this->filter_shape_id = '';
        $this->filter_topping_category_id = '';
        $this->filter_topping_id = '';
        $this->resetPage();
    }

    /**
     * Provide component state exclusively to the view file using Livewire 4 array mapping.
     *
     * @return array{combos: LengthAwarePaginator, shapes: mixed, toppingCategories: mixed, toppings: mixed}
     */
    public function with(): array
    {
        $toppingsQuery = CakeTopping::orderBy('name');
        if ($this->filter_topping_category_id) {
            $toppingsQuery->where('topping_category_id', $this->filter_topping_category_id);
        }

        return [
            'combos' => ShapeTopping::with(['shape', 'topping.category'])
                ->when($this->search, fn($query) => $query->whereHas('shape', fn($subQuery) => $subQuery->where('name', 'like', "%{$this->search}%"))
                    ->orWhereHas('topping', fn($subQuery) => $subQuery->where('name', 'like', "%{$this->search}%")))
                ->when($this->filter_shape_id, fn($query) => $query->where('cake_shape_id', $this->filter_shape_id))
                ->when($this->filter_topping_category_id, fn($query) => $query->whereHas('topping', fn($q) => $q->where('topping_category_id', $this->filter_topping_category_id)))
                ->when($this->filter_topping_id, fn($query) => $query->where('cake_topping_id', $this->filter_topping_id))
                ->latest()
                ->paginate($this->perPage),
            'shapes' => CakeShape::orderBy('name')->get(),
            'toppingCategories' => ToppingCategory::orderBy('name')->get(),
            'toppings' => $toppingsQuery->get(),
        ];
    }
};