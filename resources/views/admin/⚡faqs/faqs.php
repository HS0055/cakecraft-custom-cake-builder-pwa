<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

use App\Livewire\Traits\HasCrudModal;
use App\Models\Faq;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * Class Faqs (Livewire 4 Multi-File Component)
 *
 * This component provides administrative CRUD operations and reordering for FAQs.
 */
new #[Layout('layouts::admin', ['title' => 'FAQ Management'])] class extends Component {
    use WithPagination, HasCrudModal;

    /** @var string The question text for the FAQ form. */
    public string $question = '';

    /** @var string The HTML answer content for the FAQ form (bound to Trix editor). */
    public string $answer = '';

    /** @var bool Determines whether the FAQ is visible on the frontend. */
    public bool $is_active = true;

    /** @var int The display order index for the FAQ list. */
    public int $sort_order = 0;

    protected function rules()
    {
        return [
            'question' => 'required|string|max:255',
            'answer' => 'required|string',
            'is_active' => 'boolean',
            'sort_order' => 'integer|min:0',
        ];
    }

    /**
     * Initializes the component and enforces authorization.
     */
    public function mount()
    {
        $this->authorize('view faqs');
    }

    /**
     * Opens the modal for creating a new FAQ.
     */
    public function openCreate()
    {
        $this->authorize('create faqs');
        $this->resetForm();
        $this->showModal = true;
    }

    /**
     * Opens the modal for editing an existing FAQ.
     *
     * @param int $id
     */
    public function openEdit(int $id)
    {
        $this->authorize('update faqs');
        $this->resetForm();
        $faq = Faq::findOrFail($id);
        $this->editingId = $id;
        $this->question = $faq->question;
        $this->answer = $faq->answer;
        $this->is_active = $faq->is_active;
        $this->sort_order = $faq->sort_order;
        $this->showModal = true;
    }

    /**
     * Finalizes form variables, passes validation, and saves (creates or updates) the FAQ record.
     * Enclosed in a database transaction to ensure atomicity.
     *
     * @return void
     */
    public function save()
    {
        $this->authorize($this->editingId ? 'update faqs' : 'create faqs');
        $this->validate();

        try {
            DB::transaction(function () {
                $faq = $this->editingId ? Faq::findOrFail($this->editingId) : new Faq();
                $faq->question = $this->question;
                $faq->answer = $this->answer;
                $faq->is_active = $this->is_active;
                $faq->sort_order = $this->sort_order;
                $faq->save();
            });

            $this->showModal = false;
            session()->flash('success', $this->editingId ? __('admin.faqs.faq_updated') : __('admin.faqs.faq_created'));
            $this->resetForm();
        } catch (\Exception $e) {
            Log::error('Failed to save faq: ' . $e->getMessage());
            session()->flash('error', __('admin.faqs.faq_error'));
        }
    }

    /**
     * Toggles the boolean is_active visibility status for a specific FAQ.
     * Enclosed in a transaction.
     *
     * @param int $id The target FAQ identifier.
     * @return void
     */
    public function toggleActive(int $id)
    {
        $this->authorize('update faqs');

        try {
            DB::transaction(function () use ($id) {
                $faq = Faq::findOrFail($id);
                $faq->is_active = !$faq->is_active;
                $faq->save();
            });
            session()->flash('success', __('admin.faqs.faq_updated'));
        } catch (\Exception $e) {
            Log::error('Failed to toggle active status for faq: ' . $e->getMessage());
            session()->flash('error', __('admin.faqs.faq_error'));
        }
    }

    /**
     * Prepares the deletion logic and triggers the visibility of the confirmation modal.
     *
     * @param int $id The pending FAQ identifier to be deleted.
     * @return void
     */
    public function confirmDelete(int $id)
    {
        $this->authorize('delete faqs');
        $this->deletingId = $id;
        $this->showDeleteModal = true;
    }

    /**
     * Action called by the confirmation modal to permanently remove the target associated FAQ record.
     * Enclosed in a transaction.
     *
     * @return void
     */
    public function delete()
    {
        $this->authorize('delete faqs');

        try {
            DB::transaction(function () {
                Faq::findOrFail($this->deletingId)->delete();
            });

            $this->showDeleteModal = false;
            $this->deletingId = null;
            session()->flash('success', __('admin.faqs.faq_deleted'));
        } catch (\Exception $e) {
            Log::error('Failed to delete faq: ' . $e->getMessage());
            $this->showDeleteModal = false;
            $this->deletingId = null;
            session()->flash('error', __('admin.faqs.faq_error'));
        }
    }

    /**
     * Reorders an item when rearranged via the Livewire 4 drag-and-drop UI (`wire:sort`).
     * Calculates the absolute array position factoring in pagination offsets, reindexes
     * the entire list logically, and persists the new sequential sort orders.
     *
     * @param int $id The ID of the dragged FAQ.
     * @param int $position The new zero-based dropped position index on the current view pane.
     * @return void
     */
    public function updateOrder($id, $position)
    {
        $this->authorize('update faqs');

        try {
            DB::transaction(function () use ($id, $position) {
                $page = $this->getPage();
                $perPage = 15; // Matches pagination
                $absolutePosition = ($page - 1) * $perPage + $position;

                $faqIds = Faq::orderBy('sort_order')->pluck('id')->toArray();

                $index = array_search($id, $faqIds);
                if ($index !== false) {
                    array_splice($faqIds, $index, 1);
                }

                array_splice($faqIds, $absolutePosition, 0, $id);

                foreach ($faqIds as $newPosition => $faqId) {
                    Faq::where('id', $faqId)->update(['sort_order' => $newPosition]);
                }
            });
        } catch (\Exception $e) {
            Log::error('Failed to update FAQ order: ' . $e->getMessage());
            $this->addError('order', 'An error occurred while reorganizing FAQs.');
        }
    }

    /**
     * Defaults form properties back to baseline state logic.
     */
    public function resetForm()
    {
        $this->resetCrudState();
        $this->question = '';
        $this->answer = '';
        $this->is_active = true;
        // set default sort_order to next highest
        $this->sort_order = Faq::max('sort_order') + 1;
    }

    /**
     * Map component dependencies and properties explicitly into the blade response wrapper.
     */
    public function with(): array
    {
        return [
            'faqs' => Faq::orderBy('sort_order')->paginate(15),
        ];
    }
};