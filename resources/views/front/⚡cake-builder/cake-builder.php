<?php

use App\Models\{CakeShape, CakeFlavor, CakeColor, CakeTopping, ToppingCategory};
use Livewire\Attributes\{Layout, Title, Computed};

new
    #[Layout('layouts::front')]
    #[Title('Build Your Cake')]
    class extends \Livewire\Component {
    public int $step = 1;
    public ?int $shapeId = null;
    public ?int $flavorId = null;
    public ?int $colorId = null;
    public string $customHex = '#ffffff';
    public bool $colorStepHandled = false;
    public bool $isCustomColor = false;
    public ?int $toppingId = null;
    public ?int $selectedCategoryId = null;

    public int $totalSteps = 5;

    public array $stepLabels = ['Shape', 'Flavor', 'Color', 'Toppings', 'Review'];

    #[Computed]
    public function shapes()
    {
        return CakeShape::with('media')->get();
    }

    public function mount()
    {
        if (request()->has('ready_cake')) {
            $readyCake = \App\Models\ReadyCake::with('cakeTopping')->find(request('ready_cake'));
            if ($readyCake && $readyCake->is_customizable) {
                $this->shapeId = (int) $readyCake->cake_shape_id;
                $this->flavorId = (int) $readyCake->cake_flavor_id;
                $this->colorId = $readyCake->cake_color_id ? (int) $readyCake->cake_color_id : null;
                $this->customHex = $readyCake->custom_color_hex ?: '#ffffff';
                if ($readyCake->custom_color_hex) {
                    $this->isCustomColor = true;
                    $this->colorStepHandled = true;
                } elseif ($this->colorId) {
                    $this->colorStepHandled = true;
                }
                $this->toppingId = $readyCake->cake_topping_id ? (int) $readyCake->cake_topping_id : null;
            }
        }
    }

    #[Computed]
    public function flavors()
    {
        if (!$this->shapeId)
            return collect();
        $shape = CakeShape::find($this->shapeId);
        return $shape ? $shape->flavors()->with('media')->get() : collect();
    }

    #[Computed]
    public function colors()
    {
        return CakeColor::all();
    }

    #[Computed]
    public function toppingCategories()
    {
        return ToppingCategory::has('toppings')->get();
    }

    #[Computed]
    public function toppings()
    {
        if (!$this->shapeId)
            return collect();

        $query = CakeTopping::whereHas('shapes', function ($q) {
            $q->where('cake_shape_id', $this->shapeId);
        })->with(['media', 'shapes']);

        if ($this->selectedCategoryId) {
            $query->where('topping_category_id', $this->selectedCategoryId);
        }

        return $query->get();
    }

    #[Computed]
    public function selectedShape()
    {
        return $this->shapeId ? CakeShape::find($this->shapeId) : null;
    }

    #[Computed]
    public function selectedFlavor()
    {
        return $this->flavorId ? CakeFlavor::find($this->flavorId) : null;
    }

    #[Computed]
    public function selectedColor()
    {
        if ($this->colorId) {
            return CakeColor::find($this->colorId);
        }

        if ($this->isCustomColor) {
            return (object) [
                'name' => __('front.cake_builder.custom_color'),
                'hex_code' => $this->customHex,
                'id' => null
            ];
        }

        return null;
    }

    #[Computed]
    public function selectedTopping()
    {
        return $this->toppingId ? CakeTopping::find($this->toppingId) : null;
    }

    #[Computed]
    public function visualShape()
    {
        return $this->shapeId
            ? CakeShape::with('media')->find($this->shapeId)
            : null;
    }

    #[Computed]
    public function visualFlavorLayer()
    {
        if (!$this->shapeId || !$this->flavorId)
            return null;

        return \App\Models\ShapeFlavor::with('media')
            ->where('cake_shape_id', $this->shapeId)
            ->where('cake_flavor_id', $this->flavorId)
            ->first();
    }

    #[Computed]
    public function visualToppingLayers()
    {
        if (!$this->shapeId || !$this->toppingId)
            return collect();

        return \App\Models\ShapeTopping::with('media')
            ->where('cake_shape_id', $this->shapeId)
            ->where('cake_topping_id', $this->toppingId)
            ->get();
    }

    #[Computed]
    public function totalPrice(): float
    {
        $price = 0;

        if ($this->selectedShape) {
            $price += (float) $this->selectedShape->base_price;
        }

        if ($this->selectedFlavor && $this->shapeId) {
            $pivot = $this->selectedFlavor->shapes()
                ->where('cake_shape_id', $this->shapeId)
                ->first()?->pivot;
            if ($pivot) {
                $price += (float) ($pivot->extra_price ?? 0);
            }
        }

        if ($this->selectedTopping && $this->shapeId) {
            $pivot = $this->selectedTopping->shapes()
                ->where('cake_shape_id', $this->shapeId)
                ->first()?->pivot;
            if ($pivot) {
                $price += (float) ($pivot->price ?? 0);
            }
        }

        return $price;
    }

    #[Computed]
    public function previewMode(): string
    {
        return match ($this->step) {
            1 => 'shape',
            2 => 'flavor',
            3 => 'color',
            4 => 'toppings',
            default => 'final',
        };
    }

    public function selectShape(int $id): void
    {
        $this->shapeId = $id;
        $this->flavorId = null;
        $this->toppingId = null;
        $this->selectedCategoryId = null;
    }

    public function selectFlavor(int $id): void
    {
        $this->flavorId = $id;
    }

    public function selectColor(?int $id = null): void
    {
        $this->colorId = $id;
        $this->colorStepHandled = true;
        $this->isCustomColor = false;
        $this->customHex = '#ffffff';
    }

    public function updatedCustomHex(): void
    {
        $this->enableCustomColor();
    }

    public function enableCustomColor(): void
    {
        $this->colorId = null;
        $this->colorStepHandled = true;
        $this->isCustomColor = true;
    }

    public function selectTopping(?int $id): void
    {
        $this->toppingId = $id;
    }

    public function selectCategory(?int $id): void
    {
        $this->selectedCategoryId = $id;
    }

    public function nextStep(): void
    {
        if ($this->step === 1 && !$this->shapeId)
            return;
        if ($this->step === 2 && !$this->flavorId)
            return;
        if ($this->step === 3 && !$this->colorStepHandled)
            return;

        if ($this->step < $this->totalSteps) {
            $this->step++;
        }
    }

    public function prevStep(): void
    {
        if ($this->step > 1) {
            $this->step--;
        }
    }

    public function goToStep(int $step): void
    {
        if ($step < $this->step) {
            $this->step = $step;
        }
    }

    public function addToCart(): void
    {
        if (!$this->shapeId || !$this->flavorId || !$this->colorStepHandled)
            return;

        $cart = session('cart', []);
        $cart[] = [
            'type' => 'custom',
            'ready_cake_id' => null,
            'name' => 'Custom ' . ($this->selectedShape?->name ?? 'Cake'),
            'price' => $this->totalPrice,
            'quantity' => 1,
            'details' => [
                'shape' => $this->selectedShape?->name,
                'shape_id' => $this->shapeId,
                'flavor' => $this->selectedFlavor?->name,
                'flavor_id' => $this->flavorId,
                'color' => $this->selectedColor?->name,
                'color_id' => $this->colorId,
                'color_hex' => $this->selectedColor?->hex_code,
                'topping' => $this->selectedTopping?->name,
                'topping_id' => $this->toppingId,
            ],
            'image' => $this->selectedShape?->getFirstMediaUrl('thumbnail'),
        ];
        session(['cart' => $cart]);

        $this->redirect(route('front.cart'), navigate: true);
    }
};
