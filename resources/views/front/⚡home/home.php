<?php

use App\Models\Slider;
use App\Models\ReadyCake;
use App\Livewire\Traits\WithCartActions;
use Livewire\Attributes\{Layout, Title};

new
    #[Layout('layouts::front')]
    #[Title('Home')]
    class extends \Livewire\Component {
    use WithCartActions;

    public function with(): array
    {
        return [
            'featuredCakes' => ReadyCake::where('is_active', true)
                ->with(['cakeShape.media', 'cakeFlavor', 'cakeColor', 'cakeTopping.media', 'shapeToppings.media', 'media'])
                ->latest()
                ->take(8)
                ->get(),
        ];
    }
};
