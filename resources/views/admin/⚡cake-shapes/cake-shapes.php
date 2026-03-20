<?php

use App\Livewire\Traits\HasCrudModal;
use App\Models\CakeShape;
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
 * Class CakeShapes (Livewire 4 Multi-File Component)
 *
 * This Livewire component governs the CRUD interface for managing base
 * Cake Shapes and their associated media assets within the admin portal.
 */
new #[Layout('layouts::admin', ['title' => 'Cake Shapes'])] class extends Component {
    use WithPagination, WithFileUploads, HasCrudModal;

    #[Validate('required|string|max:255')]
    public string $name = '';

    #[Validate('required|numeric|min:0')]
    public string $base_price = '0.00';

    #[Validate('nullable|image|max:2048')]
    public $thumbnail;

    #[Validate('nullable|image|max:4096')]
    public $base_image;

    #[Validate('nullable|image|max:4096')]
    public $base_cut_image;

    public bool $delete_thumbnail = false;
    public bool $delete_base_image = false;
    public bool $delete_base_cut_image = false;

    public string $search = '';
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
        $this->resetForm();
        $shape = CakeShape::findOrFail($id);
        $this->editingId = $id;
        $this->name = $shape->name;
        $this->base_price = $shape->base_price;
        $this->showModal = true;
    }

    public function save(): void
    {
        $this->authorize($this->editingId ? 'update shapes' : 'create shapes');

        $this->validate();

        try {
            DB::transaction(function () {
                $shape = $this->editingId
                    ? CakeShape::findOrFail($this->editingId)
                    : new CakeShape();

                $shape->name = $this->name;
                $shape->base_price = $this->base_price;
                $shape->save();

                // Handle media uploads
                if ($this->delete_thumbnail && !$this->thumbnail) {
                    $shape->clearMediaCollection('thumbnail');
                } elseif ($this->thumbnail) {
                    $path = $this->thumbnail->store('cake-shapes');
                    $shape->addMediaFromDisk($path)
                        ->usingFileName($this->thumbnail->getClientOriginalName())
                        ->toMediaCollection('thumbnail');
                }

                if ($this->delete_base_image && !$this->base_image) {
                    $shape->clearMediaCollection('base_image');
                } elseif ($this->base_image) {
                    $path = $this->base_image->store('cake-shapes');
                    $shape->addMediaFromDisk($path)
                        ->usingFileName($this->base_image->getClientOriginalName())
                        ->toMediaCollection('base_image');
                }

                if ($this->delete_base_cut_image && !$this->base_cut_image) {
                    $shape->clearMediaCollection('base_cut_image');
                } elseif ($this->base_cut_image) {
                    $path = $this->base_cut_image->store('cake-shapes');
                    $shape->addMediaFromDisk($path)
                        ->usingFileName($this->base_cut_image->getClientOriginalName())
                        ->toMediaCollection('base_cut_image');
                }
            });

            $this->showModal = false;
            session()->flash('success', $this->editingId ? __('admin.cake_shapes.updated_successfully') : __('admin.cake_shapes.created_successfully'));
            $this->resetForm();
        } catch (\Exception $e) {
            Log::error('Failed to save shape: ' . $e->getMessage());
            session()->flash('error', __('admin.cake_shapes.error_saving') . ' ' . $e->getMessage());
        }
    }

    public function confirmDelete(int $id): void
    {
        $this->authorize('delete shapes');
        $this->deletingId = $id;
        $this->showDeleteModal = true;
    }

    public function delete(): void
    {
        $this->authorize('delete shapes');

        try {
            DB::transaction(function () {
                $shape = CakeShape::findOrFail($this->deletingId);
                // Clear media collections before deletion if necessary, though Spatie usually handles this via observers
                $shape->delete();
            });

            $this->showDeleteModal = false;
            $this->deletingId = null;
            session()->flash('success', __('admin.cake_shapes.deleted_successfully'));
        } catch (\Exception $e) {
            Log::error('Failed to delete shape: ' . $e->getMessage());
            session()->flash('error', __('admin.cake_shapes.error_deleting'));
        }
    }

    public function resetForm(): void
    {
        $this->resetCrudState();
        $this->name = '';
        $this->base_price = '0.00';
        $this->thumbnail = null;
        $this->base_image = null;
        $this->base_cut_image = null;
        $this->delete_thumbnail = false;
        $this->delete_base_image = false;
        $this->delete_base_cut_image = false;
        $this->resetValidation();
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    /**
     * Provide component state exclusively to the view file using Livewire 4 array mapping.
     *
     * @return array{shapes: LengthAwarePaginator}
     */
    public function with(): array
    {
        return [
            'shapes' => CakeShape::query()
                ->when($this->search, fn($query) => $query->where('name', 'like', "%{$this->search}%"))
                ->latest()
                ->paginate($this->perPage),
        ];
    }
};