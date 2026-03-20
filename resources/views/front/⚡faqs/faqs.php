<?php

use App\Models\Faq;
use Livewire\Attributes\{Layout, Title};

new
    #[Layout('layouts::front')]
    #[Title('FAQs')]
    class extends \Livewire\Component {
    public function with(): array
    {
        return [
            'faqs' => Faq::where('is_active', true)->orderBy('sort_order')->get(),
        ];
    }
};
