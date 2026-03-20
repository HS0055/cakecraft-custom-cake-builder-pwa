<?php

use App\Livewire\Traits\HasCrudModal;
use App\Models\CakeColor;
use App\Settings\GeneralSettings;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Class CakeColors (Livewire 4 Multi-File Component)
 *
 * This Livewire component governs the CRUD interface for managing
 * Cake Colors and their hex codes within the admin portal.
 */
new #[Layout('layouts::admin', ['title' => 'Cake Colors'])] class extends Component {
    use WithPagination, HasCrudModal;

    #[Validate('required|string|max:255')]
    public string $name = '';

    #[Validate('required|string|regex:/^#[0-9A-Fa-f]{6}$/')]
    public string $hex_code = '#000000';

    public string $search = '';

    public function mount(): void
    {
        $this->authorize('view colors');
    }

    public function openCreate(): void
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function openEdit(int $id): void
    {
        $color = CakeColor::findOrFail($id);
        $this->editingId = $id;
        $this->name = $color->name;
        $this->hex_code = $color->hex_code;
        $this->showModal = true;
    }

    public function save(): void
    {
        $this->authorize($this->editingId ? 'update colors' : 'create colors');
        $this->validate();

        try {
            DB::transaction(function () {
                $color = $this->editingId ? CakeColor::findOrFail($this->editingId) : new CakeColor();
                $color->name = $this->name;
                $color->hex_code = $this->hex_code;
                $color->save();
            });

            $this->showModal = false;
            session()->flash('success', $this->editingId ? __('admin.cake_colors.updated_successfully') : __('admin.cake_colors.created_successfully'));
            $this->resetForm();
        } catch (\Exception $e) {
            Log::error('Failed to save color: ' . $e->getMessage());
            session()->flash('error', __('admin.cake_colors.error_saving'));
        }
    }

    public function confirmDelete(int $id): void
    {
        $this->authorize('delete colors');
        $this->deletingId = $id;
        $this->showDeleteModal = true;
    }

    public function delete(): void
    {
        $this->authorize('delete colors');

        try {
            DB::transaction(function () {
                CakeColor::findOrFail($this->deletingId)->delete();
            });

            $this->showDeleteModal = false;
            $this->deletingId = null;
            session()->flash('success', __('admin.cake_colors.deleted_successfully'));
        } catch (\Exception $e) {
            Log::error('Failed to delete color: ' . $e->getMessage());
            session()->flash('error', __('admin.cake_colors.error_deleting'));
        }
    }

    public function resetForm()
    {
        $this->resetCrudState();
        $this->name = '';
        $this->hex_code = '#000000';
        $this->resetValidation();
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    /**
     * Provide component state exclusively to the view file using Livewire 4 array mapping.
     *
     * @return array{colors: LengthAwarePaginator}
     */
    public function with(): array
    {
        return [
            'colors' => CakeColor::query()
                ->when($this->search, fn($query) => $query->where('name', 'like', "%{$this->search}%"))
                ->latest()
                ->paginate(settings(\App\Settings\GeneralSettings::class)->pagination_limit),
        ];
    }
};