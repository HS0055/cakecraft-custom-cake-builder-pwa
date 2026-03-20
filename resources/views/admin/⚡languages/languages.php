<?php

use function Livewire\Volt\{state, rules, compute, on, mount};
use App\Models\Language;
use App\Livewire\Traits\HasCrudModal;
use Livewire\Attributes\Layout;
use Livewire\Component;

/**
 * Class Languages (Livewire 4 Multi-File Component)
 *
 * This component provides administrative CRUD operations for supported languages.
 */
new #[Layout('layouts::admin', ['title' => 'Language Management'])] class extends Component {
    use HasCrudModal;

    /** @var string Search query for filtering languages. */
    public string $search = '';

    /** @var string The language code. */
    public string $code = '';

    /** @var string The language name. */
    public string $name = '';

    /** @var bool Active status. */
    public bool $is_active = true;

    /** @var bool Flag for editing mode. */
    public bool $isEditing = false;

    /**
     * Component validation rules.
     */
    protected function rules()
    {
        return [
            'code' => 'required|string|max:2|unique:languages,code,' . ($this->editingId ?? 'NULL'),
            'name' => 'required|string|max:255',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Initializes the component.
     */
    public function mount()
    {
        // Authorization check if needed
    }

    /**
     * Reset form and open modal for creation.
     */
    public function create()
    {
        $this->resetForm();
        $this->isEditing = false;
        $this->showModal = true;
    }

    /**
     * Open modal for editing an existing language.
     *
     * @param int $id
     */
    public function edit(int $id)
    {
        $language = Language::find($id);
        if ($language) {
            $this->resetForm();
            $this->isEditing = true;
            $this->editingId = $language->id;
            $this->code = $language->code;
            $this->name = $language->name;
            $this->is_active = $language->is_active;
            $this->showModal = true;
        }
    }

    /**
     * Save the language record.
     */
    public function save()
    {
        $this->validate();

        if ($this->isEditing && $this->editingId) {
            $language = Language::find($this->editingId);
            $language->update([
                'code' => $this->code,
                'name' => $this->name,
                'is_active' => $this->is_active,
            ]);
            $this->dispatch('toast', message: 'Language updated successfully', type: 'success');
        } else {
            Language::create([
                'code' => $this->code,
                'name' => $this->name,
                'is_active' => $this->is_active,
            ]);
            $this->dispatch('toast', message: 'Language created successfully', type: 'success');
        }

        $this->showModal = false;
        $this->resetForm();
    }

    /**
     * Show delete confirmation modal.
     */
    public function confirmDelete(int $id): void
    {
        $this->deletingId = $id;
        $this->showDeleteModal = true;
    }

    /**
     * Delete the confirmed language record.
     */
    public function delete(): void
    {
        if ($this->deletingId) {
            Language::destroy($this->deletingId);
            $this->dispatch('toast', message: 'Language deleted successfully', type: 'info');
            $this->closeDeleteModal();
            $this->resetCrudState();
        }
    }

    /**
     * Toggle active status.
     *
     * @param int $id
     */
    public function toggleActive(int $id)
    {
        $language = Language::find($id);
        if ($language) {
            $language->is_active = !$language->is_active;
            $language->save();
            $this->dispatch('toast', message: 'Language status toggled', type: 'success');
        }
    }

    /**
     * Reset form data.
     */
    public function resetForm()
    {
        $this->resetCrudState();
        $this->code = '';
        $this->name = '';
        $this->is_active = true;
        $this->isEditing = false;
    }

    /**
     * Computed property for languages list.
     */
    public function getLanguagesProperty()
    {
        return Language::query()
            ->when($this->search, function ($query) {
                $query->where('name', 'like', "%{$this->search}%")
                    ->orWhere('code', 'like', "%{$this->search}%");
            })
            ->orderBy('name')
            ->get();
    }

    /**
     * Map component dependencies and properties explicitly into the blade response wrapper.
     */
    public function with(): array
    {
        return [
            'languagesList' => $this->languages,
        ];
    }
};
