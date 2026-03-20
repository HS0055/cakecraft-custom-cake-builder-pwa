<?php

namespace App\Livewire\Traits;

trait HasCrudModal
{
    public bool $showModal = false;
    public bool $showDeleteModal = false;
    public ?int $editingId = null;
    public ?int $deletingId = null;

    public function closeModal(): void
    {
        $this->showModal = false;
    }

    public function closeDeleteModal(): void
    {
        $this->showDeleteModal = false;
    }

    /**
     * Reset shared CRUD state. Call this from your component's resetForm().
     */
    public function resetCrudState(): void
    {
        $this->editingId = null;
        $this->deletingId = null;
        $this->showModal = false;
        $this->showDeleteModal = false;
        $this->resetValidation();
    }
}
