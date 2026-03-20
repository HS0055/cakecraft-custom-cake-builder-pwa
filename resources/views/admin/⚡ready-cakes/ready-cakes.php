<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

use App\Models\CakeFlavor;
use App\Models\CakeShape;
use App\Models\ReadyCake;
use App\Settings\GeneralSettings;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * Class ReadyCakes (Livewire 4 Multi-File Component)
 *
 * This Livewire component handles the administrative listing, filtering, and deletion
 * of Ready Cakes in the system. It implements pagination and basic status toggles.
 */
new #[Layout('layouts::admin', ['title' => 'Ready Cakes'])] class extends Component {
    use WithPagination;

    public bool $showDeleteModal = false;

    public ?int $deletingId = null;

    public string $search = '';
    public string $filter_shape_id = '';
    public string $filter_flavor_id = '';
    public string $filter_status = '';
    public int $perPage = 15;

    /**
     * Component initialization.
     * Enforces viewing authorization constraints.
     */
    public function mount(): void
    {
        $this->authorize('view ready cakes');
        $this->perPage = settings(GeneralSettings::class)->pagination_limit;
    }

    /**
     * Opens the deletion confirmation modal mapping to the target cake ID.
     *
     * @param int $id The ID of the Ready Cake to discard.
     */
    public function confirmDelete(int $id): void
    {
        $this->authorize('delete ready cakes');
        $this->deletingId = $id;
        $this->showDeleteModal = true;
    }

    /**
     * Executes the hard deletion command mapped from the confirmation modal.
     * Uses a DB transaction to ensure associations (media) roll back if deletion fails.
     */
    public function delete(): void
    {
        $this->authorize('delete ready cakes');

        try {
            DB::transaction(function () {
                ReadyCake::findOrFail($this->deletingId)->delete();
            });

            $this->showDeleteModal = false;
            $this->deletingId = null;
            session()->flash('success', __('admin.ready_cakes.deleted_successfully'));
        } catch (\Exception $e) {
            Log::error('Failed to delete ready cake: ' . $e->getMessage());
            $this->showDeleteModal = false;
            $this->deletingId = null;
            session()->flash('error', __('admin.ready_cakes.error_deleting'));
        }
    }

    /**
     * Toggles the boolean is_active visibility status for a cake.
     * Enclosed in a transaction to prevent partial state updates.
     *
     * @param int $id The target cake ID.
     */
    public function toggleActive(int $id): void
    {
        $this->authorize('update ready cakes');

        try {
            DB::transaction(function () use ($id) {
                $readyCake = ReadyCake::findOrFail($id);
                $readyCake->is_active = !$readyCake->is_active;
                $readyCake->save();
            });
            session()->flash('success', __('admin.ready_cakes.toggled_successfully'));
        } catch (\Exception $e) {
            Log::error('Failed to toggle active status for ready cake: ' . $e->getMessage());
            session()->flash('error', __('admin.ready_cakes.error_toggling'));
        }
    }

    /**
     * Reset pagination implicitly when searching.
     */
    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    /**
     * Reset pagination when changing shape filters.
     */
    public function updatingFilterShapeId(): void
    {
        $this->resetPage();
    }

    /**
     * Reset pagination when changing flavor filters.
     */
    public function updatingFilterFlavorId(): void
    {
        $this->resetPage();
    }

    /**
     * Reset pagination when adjusting visibility status filters.
     */
    public function updatingFilterStatus(): void
    {
        $this->resetPage();
    }

    /**
     * Clears all filters strings and forces pagination back to index 1.
     */
    public function resetFilters(): void
    {
        $this->search = '';
        $this->filter_shape_id = '';
        $this->filter_flavor_id = '';
        $this->filter_status = '';
        $this->resetPage();
    }

    /**
     * Supplies variables dynamically to the underlying Blade component.
     * Prevents rendering N+1 bottlenecks by cascading constraints.
     *
     * @return array<string, mixed>
     */
    public function with(): array
    {
        return [
            'readyCakes' => ReadyCake::with(['cakeShape.media', 'cakeFlavor', 'cakeColor', 'cakeTopping', 'media'])
                ->when($this->search, fn($query) => $query->where('name', 'like', "%{$this->search}%"))
                ->when($this->filter_shape_id, fn($query) => $query->where('cake_shape_id', $this->filter_shape_id))
                ->when($this->filter_flavor_id, fn($query) => $query->where('cake_flavor_id', $this->filter_flavor_id))
                ->when($this->filter_status !== '', fn($query) => $query->where('is_active', $this->filter_status))
                ->latest()
                ->paginate($this->perPage),
            'shapes' => CakeShape::orderBy('name')->get(),
            'flavors' => CakeFlavor::orderBy('name')->get(),
        ];
    }
};
