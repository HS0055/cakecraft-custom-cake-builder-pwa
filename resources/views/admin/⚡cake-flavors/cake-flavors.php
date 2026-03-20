<?php

use App\Livewire\Traits\HasCrudModal;
use App\Models\CakeFlavor;
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
 * Class CakeFlavors (Livewire 4 Multi-File Component)
 *
 * This Livewire component governs the CRUD interface for managing
 * Cake Flavors and their associated media assets within the admin portal.
 */
new #[Layout('layouts::admin', ['title' => 'Cake Flavors'])] class extends Component {
    use WithPagination, WithFileUploads, HasCrudModal;

    #[Validate('required|string|max:255')]
    public string $name = '';

    #[Validate('nullable|image|max:2048')]
    public $thumbnail;

    public bool $delete_thumbnail = false;

    public string $search = '';
    public int $perPage = 15;

    public function mount(): void
    {
        $this->authorize('view flavors');
        $this->perPage = settings(GeneralSettings::class)->pagination_limit;
    }

    public function openCreate(): void
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function openEdit(int $id): void
    {
        $flavor = CakeFlavor::findOrFail($id);
        $this->editingId = $id;
        $this->name = $flavor->name;
        $this->delete_thumbnail = false;
        $this->showModal = true;
    }

    public function save(): void
    {
        $this->authorize($this->editingId ? 'update flavors' : 'create flavors');

        $this->validate();

        try {
            DB::transaction(function () {
                $flavor = $this->editingId
                    ? CakeFlavor::findOrFail($this->editingId)
                    : new CakeFlavor();

                $flavor->name = $this->name;
                $flavor->save();

                if ($this->delete_thumbnail && !$this->thumbnail) {
                    $flavor->clearMediaCollection('thumbnail');
                } elseif ($this->thumbnail) {
                    $path = $this->thumbnail->store('cake-flavors');
                    $flavor->addMediaFromDisk($path)
                        ->usingFileName($this->thumbnail->getClientOriginalName())
                        ->toMediaCollection('thumbnail');
                }
            });

            $this->showModal = false;
            session()->flash('success', $this->editingId ? __('admin.cake_flavors.updated_successfully') : __('admin.cake_flavors.created_successfully'));
            $this->resetForm();
        } catch (\Exception $e) {
            Log::error('Failed to save flavor: ' . $e->getMessage());
            session()->flash('error', __('admin.cake_flavors.error_saving'));
        }
    }

    public function confirmDelete(int $id): void
    {
        $this->authorize('delete flavors');
        $this->deletingId = $id;
        $this->showDeleteModal = true;
    }

    public function delete(): void
    {
        $this->authorize('delete flavors');

        try {
            DB::transaction(function () {
                CakeFlavor::findOrFail($this->deletingId)->delete();
            });

            $this->showDeleteModal = false;
            $this->deletingId = null;
            session()->flash('success', __('admin.cake_flavors.deleted_successfully'));
        } catch (\Exception $e) {
            Log::error('Failed to delete flavor: ' . $e->getMessage());
            session()->flash('error', __('admin.cake_flavors.error_deleting'));
        }
    }

    public function resetForm()
    {
        $this->resetCrudState();
        $this->name = '';
        $this->name = '';
        $this->thumbnail = null;
        $this->delete_thumbnail = false;
        $this->resetValidation();
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    /**
     * Provide component state exclusively to the view file using Livewire 4 array mapping.
     *
     * @return array{flavors: LengthAwarePaginator}
     */
    public function with(): array
    {
        return [
            'flavors' => CakeFlavor::query()
                ->when($this->search, fn($query) => $query->where('name', 'like', "%{$this->search}%"))
                ->latest()
                ->paginate($this->perPage),
        ];
    }
};