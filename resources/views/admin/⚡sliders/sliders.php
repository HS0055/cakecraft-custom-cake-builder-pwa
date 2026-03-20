<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

use App\Livewire\Traits\HasCrudModal;
use App\Models\Slider;
use App\Models\ReadyCake;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

/**
 * Class Sliders (Livewire 4 Multi-File Component)
 *
 * This Livewire component handles the administrative listing and CRUD operations
 * for promotional Sliders in the system.
 */
new #[Layout('layouts::admin', ['title' => 'Slider Management'])] class extends Component {
    use WithPagination, WithFileUploads, HasCrudModal;

    public string $action_type = 'ready_cake';
    public ?int $ready_cake_id = null;
    public bool $is_active = true;
    public int $sort_order = 0;
    public $image;
    public $existingImage;
    public bool $remove_image = false;

    protected function rules()
    {
        return [
            'action_type' => 'required|in:ready_cake,custom_builder',
            'ready_cake_id' => 'required_if:action_type,ready_cake|nullable|exists:ready_cakes,id',
            'is_active' => 'boolean',
            'sort_order' => 'integer|min:0',
            'image' => ($this->editingId && !$this->remove_image) ? 'nullable|image|max:2048' : 'required|image|max:2048',
        ];
    }

    /**
     * Initializes the component and enforces authorization.
     */
    public function mount()
    {
        $this->authorize('view sliders');
    }

    /**
     * Opens the modal for creating a new slider.
     */
    public function openCreate()
    {
        $this->authorize('create sliders');
        $this->resetForm();
        $this->showModal = true;
    }

    /**
     * Opens the modal for editing an existing slider.
     *
     * @param int $id The target slider ID.
     */
    public function openEdit(int $id)
    {
        $this->authorize('update sliders');
        $this->resetForm();
        $slider = Slider::findOrFail($id);
        $this->editingId = $id;
        $this->action_type = $slider->action_type;
        $this->ready_cake_id = $slider->ready_cake_id;
        $this->is_active = $slider->is_active;
        $this->sort_order = $slider->sort_order;
        $this->existingImage = $slider->getFirstMediaUrl('image');
        $this->showModal = true;
    }

    /**
     * Finalizes form variables and saves the slider record.
     * Enclosed in a DB Transaction.
     */
    public function save()
    {
        $this->authorize($this->editingId ? 'update sliders' : 'create sliders');
        $this->validate();

        try {
            DB::transaction(function () {
                $slider = $this->editingId ? Slider::findOrFail($this->editingId) : new Slider();
                $slider->action_type = $this->action_type;
                $slider->ready_cake_id = $this->action_type === 'ready_cake' ? $this->ready_cake_id : null;
                $slider->is_active = $this->is_active;
                $slider->sort_order = $this->sort_order;
                $slider->save();

                if ($this->remove_image) {
                    $slider->clearMediaCollection('image');
                }

                if ($this->image) {
                    $path = $this->image->store('sliders');
                    $slider->addMediaFromDisk($path)
                        ->usingFileName($this->image->getClientOriginalName())
                        ->toMediaCollection('image');
                }
            });

            $this->showModal = false;
            $this->resetForm();
            session()->flash('success', $this->editingId ? __('admin.sliders.slider_updated') : __('admin.sliders.slider_created'));
        } catch (\Exception $e) {
            Log::error('Failed to save slider: ' . $e->getMessage());
            session()->flash('error', __('admin.sliders.slider_error'));
        }
    }

    /**
     * Toggles the boolean is_active visibility status for a slider.
     * Enclosed in a transaction.
     *
     * @param int $id The target slider ID.
     */
    public function toggleActive(int $id)
    {
        $this->authorize('update sliders');

        try {
            DB::transaction(function () use ($id) {
                $slider = Slider::findOrFail($id);
                $slider->is_active = !$slider->is_active;
                $slider->save();
            });
            session()->flash('success', __('admin.sliders.status_updated'));
        } catch (\Exception $e) {
            Log::error('Failed to toggle active status for slider: ' . $e->getMessage());
            session()->flash('error', __('admin.sliders.status_error'));
        }
    }

    /**
     * Prepares removal logic and triggers the confirmation modal.
     *
     * @param int $id The slider ID to delete.
     */
    public function confirmDelete(int $id)
    {
        $this->authorize('delete sliders');
        $this->deletingId = $id;
        $this->showDeleteModal = true;
    }

    /**
     * Action called to permanently remove the target associated slider ID.
     * Transaction enclosed.
     */

    public function delete()
    {
        $this->authorize('delete sliders');

        try {
            DB::transaction(function () {
                Slider::findOrFail($this->deletingId)->delete();
            });

            $this->showDeleteModal = false;
            $this->deletingId = null;
            session()->flash('success', __('admin.sliders.slider_deleted'));
        } catch (\Exception $e) {
            Log::error('Failed to delete slider: ' . $e->getMessage());
            $this->showDeleteModal = false;
            $this->deletingId = null;
            session()->flash('error', __('admin.sliders.slider_error'));
        }
    }

    /**
     * Reorder an item when arranged via the UI drag-and-drop.
     *
     * @param int $id The ID of the dragged Slider.
     * @param int $position The new zero-based position.
     */
    public function updateOrder($id, $position)
    {
        $this->authorize('update sliders');

        try {
            DB::transaction(function () use ($id, $position) {
                $page = $this->getPage();
                $perPage = 10; // Matches pagination
                $absolutePosition = ($page - 1) * $perPage + $position;

                $sliderIds = Slider::orderBy('sort_order')->pluck('id')->toArray();

                $index = array_search($id, $sliderIds);
                if ($index !== false) {
                    array_splice($sliderIds, $index, 1);
                }

                array_splice($sliderIds, $absolutePosition, 0, $id);

                foreach ($sliderIds as $newPosition => $sliderId) {
                    Slider::where('id', $sliderId)->update(['sort_order' => $newPosition]);
                }
            });
        } catch (\Exception $e) {
            Log::error('Failed to update slider order: ' . $e->getMessage());
            $this->addError('order', 'An error occurred while reorganizing slides.');
        }
    }

    /**
     * Defaults form properties back to baseline state logic.
     */
    public function resetForm()
    {
        $this->resetCrudState();
        $this->action_type = 'ready_cake';
        $this->ready_cake_id = null;
        $this->is_active = true;
        $this->sort_order = Slider::max('sort_order') + 1;
        $this->image = null;
        $this->existingImage = null;
        $this->remove_image = false;
    }

    /**
     * Map component dependencies and properties explicitly into the blade response wrapper.
     * 
     * @return array<string, mixed>
     */
    public function with(): array
    {
        return [
            'sliders' => Slider::with('readyCake')->orderBy('sort_order')->paginate(10),
            'readyCakes' => ReadyCake::orderBy('name')->get(),
        ];
    }
};
