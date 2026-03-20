<?php

use App\Models\ReadyCake;
use App\Models\CakeShape;
use App\Livewire\Traits\WithCartActions;
use Livewire\Attributes\{Layout, Title, Url};
use Livewire\WithPagination;

new
    #[Layout('layouts::front')]
    #[Title('Shop')]
    class extends \Livewire\Component {
    use WithPagination, WithCartActions;

    #[Url]
    public string $search = '';

    #[Url]
    public ?int $shapeFilter = null;

    #[Url]
    public string $sort = 'newest';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingShapeFilter(): void
    {
        $this->resetPage();
    }

    public function updatingSort(): void
    {
        $this->resetPage();
    }

    public function filterByShape(?int $shapeId): void
    {
        $this->shapeFilter = $this->shapeFilter === $shapeId ? null : $shapeId;
        $this->resetPage();
    }

    public function with(): array
    {
        $query = ReadyCake::where('is_active', true)
            ->with(['cakeShape', 'cakeFlavor', 'cakeColor', 'cakeTopping.media', 'shapeToppings.media', 'media']);

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->shapeFilter) {
            $query->where('cake_shape_id', $this->shapeFilter);
        }

        $query = match ($this->sort) {
            'price_asc' => $query->orderBy('price', 'asc'),
            'price_desc' => $query->orderBy('price', 'desc'),
            default => $query->latest(),
        };

        return [
            'cakes' => $query->paginate(12),
            'shapes' => CakeShape::orderBy('name')->get(),
        ];
    }
};
