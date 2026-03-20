<?php

use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

use App\Livewire\Traits\HasCrudModal;
use App\Models\Page;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * Class Pages (Livewire 4 Multi-File Component)
 *
 * This component provides administrative CRUD operations for static content pages.
 */
new #[Layout('layouts::admin', ['title' => 'Static Pages'])] class extends Component {
    use WithPagination, HasCrudModal;

    /** @var string The title of the page used for display headings. */
    public string $title = '';

    /** @var string The URL-friendly identifier for the page. Auto-generated if omitted. */
    public string $slug = '';

    /** @var string|null The HTML formatted body content of the page (bound to Trix editor). */
    public ?string $content = '';

    /** @var bool Determines whether the static page is accessible on the frontend. */
    public bool $is_active = true;

    protected function rules()
    {
        return [
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:pages,slug,' . $this->editingId,
            'content' => 'nullable|string',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Initializes the component and enforces authorization.
     */
    public function mount()
    {
        $this->authorize('view pages');
    }

    /**
     * Opens the modal for creating a new page.
     */
    public function openCreate()
    {
        $this->authorize('create pages');
        $this->resetForm();
        $this->showModal = true;
    }

    /**
     * Opens the modal for editing an existing page.
     *
     * @param int $id
     */
    public function openEdit(int $id)
    {
        $this->authorize('update pages');
        $this->resetForm();
        $page = Page::findOrFail($id);
        $this->editingId = $id;
        $this->title = $page->title;
        $this->slug = $page->slug;
        $this->content = $page->content;
        $this->is_active = $page->is_active;
        $this->showModal = true;
    }

    /**
     * Finalizes form variables, generates the slug if absent, validates, and saves (creates or updates) the Page.
     * Enclosed in a database transaction to ensure atomicity.
     *
     * @return void
     */
    public function save()
    {
        $this->authorize($this->editingId ? 'update pages' : 'create pages');

        // Automatically generate a slug if none was provided
        if (empty($this->slug)) {
            $this->slug = Str::slug($this->title);
        }

        $this->validate();

        try {
            DB::transaction(function () {
                $page = $this->editingId ? Page::findOrFail($this->editingId) : new Page();
                $page->title = $this->title;
                $page->slug = $this->slug;
                $page->content = $this->content;
                $page->is_active = $this->is_active;
                $page->save();
            });

            $this->showModal = false;
            session()->flash('success', $this->editingId ? __('admin.pages.updated_successfully') : __('admin.pages.created_successfully'));
            $this->resetForm();
        } catch (\Exception $e) {
            Log::error('Failed to save page: ' . $e->getMessage());
            session()->flash('error', __('admin.pages.error_occurred'));
        }
    }

    /**
     * Toggles the boolean is_active visibility status for a specific page.
     * Enclosed in a database transaction.
     *
     * @param int $id The target Page identifier.
     * @return void
     */
    public function toggleActive(int $id)
    {
        $this->authorize('update pages');

        try {
            DB::transaction(function () use ($id) {
                $page = Page::findOrFail($id);
                $page->is_active = !$page->is_active;
                $page->save();
            });
            session()->flash('success', __('admin.pages.status_updated'));
        } catch (\Exception $e) {
            Log::error('Failed to toggle active status for page: ' . $e->getMessage());
            session()->flash('error', __('admin.pages.status_error'));
        }
    }

    /**
     * Prepares the deletion logic and triggers the visibility of the confirmation modal.
     *
     * @param int $id The pending Page identifier to be deleted.
     * @return void
     */
    public function confirmDelete(int $id)
    {
        $this->authorize('delete pages');
        $this->deletingId = $id;
        $this->showDeleteModal = true;
    }

    /**
     * Action called by the confirmation modal to permanently remove the target associated Page record.
     * Enclosed in a database transaction.
     *
     * @return void
     */
    public function delete()
    {
        $this->authorize('delete pages');

        try {
            DB::transaction(function () {
                Page::findOrFail($this->deletingId)->delete();
            });

            $this->showDeleteModal = false;
            $this->deletingId = null;
            session()->flash('success', __('admin.pages.deleted_successfully'));
        } catch (\Exception $e) {
            Log::error('Failed to delete page: ' . $e->getMessage());
            $this->showDeleteModal = false;
            $this->deletingId = null;
            session()->flash('error', __('admin.pages.delete_error'));
        }
    }

    /**
     * Defaults form properties back to baseline state logic.
     */
    public function resetForm()
    {
        $this->resetCrudState();
        $this->title = '';
        $this->slug = '';
        $this->content = '';
        $this->is_active = true;
    }

    /**
     * Map component dependencies and properties explicitly into the blade response wrapper.
     */
    public function with(): array
    {
        return [
            'pages' => Page::latest()->paginate(10),
        ];
    }
};