<?php

use App\Models\Page;
use Livewire\Attributes\{Layout, Title};

new
    #[Layout('layouts::front')]
    class extends \Livewire\Component {

    public Page $page;

    public function mount(Page $page)
    {
        if (!$page->is_active) {
            abort(404);
        }
        $this->page = $page;
    }

    public function title(): string
    {
        return $this->page->title ?? 'Page';
    }
};
