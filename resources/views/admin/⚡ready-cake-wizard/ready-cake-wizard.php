<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

use App\Models\CakeColor;
use App\Models\CakeFlavor;
use App\Models\CakeShape;
use App\Models\CakeTopping;
use App\Models\ReadyCake;
use App\Models\ShapeFlavor;
use App\Models\ShapeTopping;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use App\Models\ToppingCategory;

/**
 * Class ReadyCakeWizard (Livewire 4 Multi-File Component)
 *
 * This Livewire component drives the step-by-step creation and editing flow
 * for Ready Cakes. It enforces validation rules for interconnected properties
 * (e.g. valid flavors for a shape) and tracks dynamic price summation.
 */
new #[Layout('layouts::admin', ['title' => 'Ready Cakes'])] class extends Component {
    use WithFileUploads, WithPagination;

    public int $step = 1;

    public ?int $editingId = null;

    #[Validate('required|string|max:255')]
    public string $name = '';

    #[Validate('required|numeric|min:0')]
    public string $price = '';

    public bool $price_manually_edited = false;

    public bool $name_manually_edited = false;

    #[Validate('boolean')]
    public bool $is_active;

    #[Validate('boolean')]
    public bool $is_customizable;

    #[Validate('required|exists:cake_shapes,id')]
    public string $cake_shape_id = '';

    #[Validate('required|exists:cake_flavors,id')]
    public string $cake_flavor_id = '';

    #[Validate('nullable|exists:cake_colors,id')]
    public ?string $cake_color_id = null;

    #[Validate('nullable|exists:cake_toppings,id')]
    public ?string $cake_topping_id = null;

    public ?string $selected_topping_category_id = null;

    #[Validate('nullable|image|max:4096')]
    public $preview;

    #[Validate('regex:/^#[a-f0-9]{6}$/i')]
    public string $custom_hex = '#ffffff';

    public ?string $previewUrl = null;

    /**
     * Initializes the component state.
     * Enforces authorization and seeds default values or mapped Eloquent model values.
     *
     * @param ReadyCake|null $readyCake The target cake if editing, null if creating.
     */
    public function mount(?ReadyCake $readyCake = null): void
    {
        $this->authorize($readyCake ? 'update ready cakes' : 'create ready cakes');

        if (!$readyCake) {
            $readySettings = settings(\App\Settings\ReadyCakeSettings::class);
            $this->is_active = $readySettings->default_is_active;
            $this->is_customizable = $readySettings->default_is_customizable;
            $this->price = '';
            return;
        }

        $this->editingId = $readyCake->id;
        $this->name = $readyCake->name ?? '';
        if ($readyCake && !empty($this->name)) {
            $this->name_manually_edited = true;
        }
        $this->price = $readyCake->price ?? '0.00';
        $this->price_manually_edited = true;
        $this->is_active = $readyCake->is_active ?? true;
        $this->is_customizable = $readyCake->is_customizable ?? true;
        $this->cake_shape_id = (string) ($readyCake->cake_shape_id ?? '');
        $this->cake_flavor_id = (string) ($readyCake->cake_flavor_id ?? '');
        $this->cake_color_id = $readyCake->cake_color_id ? (string) $readyCake->cake_color_id : null;
        $this->custom_hex = $readyCake->custom_color_hex ?: '#ffffff';
        $this->cake_topping_id = (string) $readyCake->cake_topping_id;
        $this->previewUrl = $readyCake->getFirstMediaUrl('preview') ?: null;

        $firstCategory = ToppingCategory::orderBy('name')->first();
        $this->selected_topping_category_id = $readyCake->cakeTopping?->topping_category_id
            ? (string) $readyCake->cakeTopping->topping_category_id
            : ($firstCategory ? (string) $firstCategory->id : null);
    }

    /**
     * Fired automatically when the selected shape changes.
     * Resets dependent flavor and topping associations to prevent invalid states.
     */
    public function updatedCakeShapeId(): void
    {
        $this->cake_flavor_id = '';
        $this->cake_topping_id = null;
        $this->resetValidation(['cake_flavor_id', 'cake_topping_id']);
        $this->calculatePrice();
    }

    /**
     * Action called by the frontend to select a shape variant.
     *
     * @param int $id Shape ID.
     */
    public function selectShape(int $id): void
    {
        if ($this->cake_shape_id !== (string) $id) {
            $this->cake_shape_id = (string) $id;
            $this->cake_flavor_id = '';
            $this->cake_topping_id = null;
            $this->resetValidation(['cake_flavor_id', 'cake_topping_id']);
            $this->calculatePrice();
        }
    }

    /**
     * Action called by the frontend to apply a flavor variant.
     *
     * @param int $id Flavor ID.
     */
    public function selectFlavor(int $id): void
    {
        $this->cake_flavor_id = (string) $id;
        $this->calculatePrice();
    }

    /**
     * Recalculates price dynamically when a topping is selected or unselected via the UI.
     */
    public function updatedCakeToppingId(): void
    {
        $this->calculatePrice();
    }

    /**
     * Resets pagination when the active topping category filter is changed.
     */
    public function updatedSelectedToppingCategoryId(): void
    {
        $this->resetPage();
    }

    /**
     * Flags when the administrator modifies the price input field directly.
     * Halts further dynamic recalculations to respect user overrides.
     */
    public function updatedPrice(): void
    {
        $this->price_manually_edited = true;
    }

    /**
     * Flags when the administrator modifies the name input field directly.
     * Halts further dynamic name generation to respect user overrides.
     */
    public function updatedName(): void
    {
        $this->name_manually_edited = true;
    }

    /**
     * Generates a default name based on the selected shape, flavor, and topping.
     */
    public function generateName(): void
    {
        if ($this->name_manually_edited) {
            return;
        }

        $parts = [];

        if ($this->cake_shape_id) {
            $parts[] = CakeShape::find($this->cake_shape_id)?->name;
        }

        if ($this->cake_flavor_id) {
            $parts[] = CakeFlavor::find($this->cake_flavor_id)?->name;
        }

        if ($this->cake_topping_id) {
            $parts[] = CakeTopping::find($this->cake_topping_id)?->name;
        }

        $parts = array_filter($parts);
        if (count($parts) > 0) {
            $this->name = implode(' - ', $parts);
        } else {
            $this->name = '';
        }
    }

    /**
     * Automatically attempts to sum the individual component parts of the cake
     * to formulate a suggested retail price point.
     */
    public function calculatePrice(): void
    {
        $this->generateName();

        if ($this->price_manually_edited) {
            return;
        }

        $shapePrice = 0;
        if ($this->cake_shape_id) {
            $shapePrice = CakeShape::find($this->cake_shape_id)?->base_price ?? 0;
        }

        $flavorPrice = 0;
        if ($this->cake_shape_id && $this->cake_flavor_id) {
            $flavorPrice = ShapeFlavor::where('cake_shape_id', $this->cake_shape_id)
                ->where('cake_flavor_id', $this->cake_flavor_id)
                ->first()?->extra_price ?? 0;
        }

        $toppingPrice = 0;
        if ($this->cake_shape_id && $this->cake_topping_id) {
            $toppingPrice = ShapeTopping::where('cake_shape_id', $this->cake_shape_id)
                ->where('cake_topping_id', $this->cake_topping_id)
                ->first()?->price ?? 0;
        }

        $this->price = number_format($shapePrice + $flavorPrice + $toppingPrice, 2, '.', '');
    }

    /**
     * Action called by the frontend to pick an exterior frosting color.
     * Handles resetting the custom hex picker if a palette color is chosen.
     *
     * @param int|null $id Color ID or null for no defined color.
     */
    public function selectColor(?int $id = null): void
    {
        $this->cake_color_id = $id ? (string) $id : null;
        if (!$id) {
            $this->custom_hex = '#ffffff';
        }
    }

    /**
     * Enforces explicit hex code validation when typing manually.
     */
    public function updatedCustomHex(): void
    {
        $this->validateOnly('custom_hex');
        $this->cake_color_id = null;
    }

    /**
     * Pushes the wizard one stage forward, triggering validation rules for the current step.
     */
    public function nextStep(): void
    {
        $this->validateStep();
        $this->step = min($this->step + 1, 5);
    }

    /**
     * Pushes the wizard one stage backward.
     */
    public function previousStep(): void
    {
        $this->step = max($this->step - 1, 1);
    }

    /**
     * Navigates to a specific step if logical ordering allows (preventing skipping).
     *
     * @param int $step The target step.
     */
    public function goToStep(int $step): void
    {
        if ($step < 1 || $step > 5) {
            return;
        }

        if ($step > $this->step) {
            return;
        }

        $this->step = $step;
    }

    protected function validateStep(): void
    {
        $fields = match ($this->step) {
            1 => ['cake_shape_id'],
            2 => ['cake_flavor_id'],
            3 => ['cake_color_id'],
            4 => ['cake_topping_id'],
            5 => ['name', 'price', 'is_active', 'is_customizable'],
            default => [],
        };

        foreach ($fields as $field) {
            $this->validateOnly($field);
        }

        if ($this->step === 2) {
            $this->validateFlavorForShape();
        }

        if ($this->step === 4) {
            $this->validateToppingsForShape();
        }
    }

    protected function validateFlavorForShape(): void
    {
        if (!$this->cake_shape_id || !$this->cake_flavor_id) {
            return;
        }

        $exists = ShapeFlavor::where('cake_shape_id', $this->cake_shape_id)
            ->where('cake_flavor_id', $this->cake_flavor_id)
            ->exists();

        if (!$exists) {
            throw ValidationException::withMessages([
                'cake_flavor_id' => 'Selected flavor is not available for this shape.',
            ]);
        }
    }

    protected function validateToppingsForShape(): void
    {
        if (!$this->cake_shape_id || !$this->cake_topping_id) {
            return;
        }

        $exists = ShapeTopping::where('cake_shape_id', $this->cake_shape_id)
            ->where('cake_topping_id', $this->cake_topping_id)
            ->exists();

        if (!$exists) {
            throw ValidationException::withMessages([
                'cake_topping_id' => 'Selected topping is not available for this shape.',
            ]);
        }
    }



    /**
     * Finalizes the form variables, commits them to Eloquent Models, and uploads assigned media.
     * Assumes a fully valid state enforced incrementally during stage progression.
     * Enclosed in a DB Transaction to ensure media process failures roll back the database entry.
     */
    public function save(): void
    {
        $this->authorize($this->editingId ? 'update ready cakes' : 'create ready cakes');

        $this->validate([
            'cake_topping_id' => 'nullable|exists:cake_toppings,id',
        ]);
        $this->validateFlavorForShape();
        $this->validateToppingsForShape();

        try {
            DB::transaction(function () {
                $readyCake = $this->editingId
                    ? ReadyCake::findOrFail($this->editingId)
                    : new ReadyCake;

                $readyCake->name = $this->name;
                $readyCake->price = $this->price;
                $readyCake->is_active = $this->is_active;
                $readyCake->is_customizable = $this->is_customizable;
                $readyCake->cake_shape_id = $this->cake_shape_id;
                $readyCake->cake_flavor_id = $this->cake_flavor_id;
                $readyCake->cake_color_id = $this->cake_color_id ?: null;
                $readyCake->custom_color_hex = $this->cake_color_id ? null : ($this->custom_hex !== '#ffffff' ? $this->custom_hex : null);

                // Set topping right away, no need for split save.
                $readyCake->cake_topping_id = $this->cake_topping_id ?: null;
                $readyCake->save();

                // Handle manually uploaded file (via input)
                if ($this->preview) {
                    $readyCake->addMedia($this->preview->getRealPath())
                        ->usingFileName($this->preview->getClientOriginalName())
                        ->toMediaCollection('preview');
                }
            });

            session()->flash('success', $this->editingId ? __('admin.ready_cakes.updated_successfully') : __('admin.ready_cakes.created_successfully'));
            $this->redirect(route('admin.ready-cakes'), navigate: true);

        } catch (\Exception $e) {
            Log::error('Failed to save ready cake in wizard: ' . $e->getMessage());
            $this->addError('save', 'An unexpected error occurred while saving the customized ready cake. Please try again.');
        }
    }

    /**
     * Assembles view dependency injections (collections) and dynamic UI representations.
     * Prevents rendering N+1 bottlenecks by cascading constraints.
     *
     * @return array<string, mixed>
     */
    public function with(): array
    {
        $shape = $this->cake_shape_id
            ? CakeShape::with('media')->find($this->cake_shape_id)
            : null;

        $flavorLayer = $this->cake_shape_id && $this->cake_flavor_id
            ? ShapeFlavor::with('media')
                ->where('cake_shape_id', $this->cake_shape_id)
                ->where('cake_flavor_id', $this->cake_flavor_id)
                ->first()
            : null;

        $toppingLayers = $this->cake_shape_id && $this->cake_topping_id
            ? ShapeTopping::with('media')
                ->where('cake_shape_id', $this->cake_shape_id)
                ->where('cake_topping_id', $this->cake_topping_id)
                ->get()
            : collect();

        $colors = CakeColor::orderBy('name')->get();

        return [
            'shapes' => CakeShape::with('media')->orderBy('name')->get(),
            'flavors' => $this->cake_shape_id
                ? CakeFlavor::with('media')
                    ->whereHas('shapes', fn($query) => $query->where('cake_shapes.id', $this->cake_shape_id))
                    ->with(['shapes' => fn($query) => $query->where('cake_shapes.id', $this->cake_shape_id)])
                    ->orderBy('name')
                    ->get()
                : collect(),
            'colors' => $colors,
            'toppings' => $this->cake_shape_id
                ? CakeTopping::with('media')
                    ->whereHas('shapes', fn($query) => $query->where('cake_shapes.id', $this->cake_shape_id))
                    ->with(['shapes' => fn($query) => $query->where('cake_shapes.id', $this->cake_shape_id)])
                    ->when($this->selected_topping_category_id, fn($query) => $query->where('topping_category_id', $this->selected_topping_category_id))
                    ->orderBy('name')
                    ->paginate(11)
                : collect(),
            'toppingCategories' => ToppingCategory::orderBy('name')->get(),
            'shape' => $shape,
            'flavorLayer' => $flavorLayer,
            'toppingLayers' => $toppingLayers,
            'color' => $this->cake_color_id
                ? $colors->firstWhere('id', (int) $this->cake_color_id)
                : ($this->custom_hex !== '#ffffff' ? (object) ['name' => 'Custom', 'hex_code' => $this->custom_hex] : null),
        ];
    }
};
