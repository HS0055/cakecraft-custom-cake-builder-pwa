<?php

use App\Models\NewsletterSubscriber;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * Newsletter Subscribers Component
 */
new #[Layout('layouts::admin', ['title' => 'Newsletter Subscribers'])] class extends Component {
    use WithPagination;

    #[Url(history: true)]
    public string $search = '';

    public int $deleteId = 0;
    public bool $showDeleteModal = false;

    public function mount()
    {
        $this->authorize('view newsletter subscribers');
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    #[Computed]
    public function subscribers()
    {
        return NewsletterSubscriber::query()
            ->when($this->search, function ($query) {
                $query->where('email', 'like', "%{$this->search}%");
            })
            ->latest()
            ->paginate(15);
    }

    public function confirmDelete(int $id)
    {
        $this->authorize('delete newsletter subscribers');
        $this->deleteId = $id;
        $this->showDeleteModal = true;
    }

    public function delete()
    {
        $this->authorize('delete newsletter subscribers');
        if ($this->deleteId) {
            NewsletterSubscriber::destroy($this->deleteId);
            session()->flash('success', __('admin.newsletter_subscribers.deleted_msg'));
        }

        $this->showDeleteModal = false;
        $this->deleteId = 0;
    }
};
