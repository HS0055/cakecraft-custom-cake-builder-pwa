<div class="animate-fade-in space-y-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="font-display text-2xl font-semibold text-foreground">
                {{ $editingId ? __('admin.ready_cake_wizard.edit_title') : __('admin.ready_cake_wizard.create_title') }}
            </h2>
            <p class="mt-1 text-sm text-foreground-muted">{{ __('admin.ready_cake_wizard.subtitle') }}</p>
        </div>
        <a href="{{ route('admin.ready-cakes') }}" wire:navigate
            class="btn-base border border-border bg-surface text-foreground hover:bg-surface-alt">
            {{ __('admin.ready_cake_wizard.back_to_ready_cakes') }}
        </a>
    </div>

    @php
        $selectedFlavor = $cake_flavor_id ? $flavors->firstWhere('id', (int) $cake_flavor_id) : null;
        $selectedTopping = $cake_topping_id ? $toppings->firstWhere('id', (int) $cake_topping_id) : null;

        $steps = [
            1 => __('admin.ready_cake_wizard.step_shape'),
            2 => __('admin.ready_cake_wizard.step_flavor'),
            3 => __('admin.ready_cake_wizard.step_color'),
            4 => __('admin.ready_cake_wizard.step_toppings'),
            5 => __('admin.ready_cake_wizard.step_publish'),
        ];
    @endphp

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-12">
        
        {{-- LEFT COLUMN: Steps --}}
        <div class="lg:col-span-7 xl:col-span-8 space-y-6">
            <div class="card-base p-4">
                <div class="grid grid-cols-2 gap-3 md:grid-cols-6">
                    @foreach ($steps as $number => $label)
                        <button type="button" wire:click="goToStep({{ $number }})" @disabled($number > $step)
                            class="flex items-center gap-2 rounded-xl border border-border px-3 py-2 text-start text-sm transition-colors duration-150
                            {{ $step === $number ? 'bg-surface ring-2 ring-accent/60' : 'bg-surface-alt/40' }}
                            {{ $number > $step ? 'cursor-not-allowed opacity-50' : 'hover:bg-surface-alt' }}">
                            <span
                                class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full text-xs font-semibold {{ $step >= $number ? 'bg-accent text-accent-foreground' : 'bg-surface-alt text-foreground-subtle' }}">
                                {{ $number }}
                            </span>
                            <span class="truncate">{{ $label }}</span>
                        </button>
                    @endforeach
                </div>
            </div>

            <div class="card-base p-6 min-h-[400px]">
                @if ($step === 1)
                    <div class="space-y-6 animate-fade-in">
                        <div>
                            <h3 class="text-lg font-semibold text-foreground">{{ __('admin.ready_cake_wizard.shape_title') }}</h3>
                            <p class="text-sm text-foreground-muted">{{ __('admin.ready_cake_wizard.shape_subtitle') }}</p>
                        </div>
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            @forelse ($shapes as $shapeItem)
                                <button type="button" wire:click="selectShape({{ $shapeItem->id }})"
                                    class="card-base p-4 text-start transition-colors duration-150 flex items-center gap-4
                                    {{ $cake_shape_id === (string) $shapeItem->id ? 'ring-2 ring-accent/60 border-accent/50 bg-surface-alt/30' : 'hover:bg-surface-alt/60' }}">
                                    @if ($shapeItem->getFirstMediaUrl('thumbnail'))
                                        <img src="{{ $shapeItem->getFirstMediaUrl('thumbnail') }}" alt="{{ $shapeItem->name }}"
                                            class="h-14 w-14 shrink-0 rounded-xl object-cover border border-border" />
                                    @else
                                        <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-xl bg-surface-alt text-foreground-subtle">
                                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909M18 13.5V18a2.25 2.25 0 0 1-2.25 2.25H5.25A2.25 2.25 0 0 1 3 18v-4.5" />
                                            </svg>
                                        </div>
                                    @endif
                                    <div>
                                        <p class="text-base font-semibold text-foreground">{{ $shapeItem->name }}</p>
                                        <p class="text-xs text-foreground-muted">{{ settings(\App\Settings\CurrencySettings::class)->currency_symbol }}{{ number_format($shapeItem->base_price, 2) }}</p>
                                    </div>
                                    @if ($cake_shape_id === (string) $shapeItem->id)
                                        <div class="ms-auto text-accent">
                                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                            </svg>
                                        </div>
                                    @endif
                                </button>
                            @empty
                                    <div class="col-span-full py-8 text-center text-sm text-foreground-muted border-2 border-dashed border-border rounded-xl">
                                    {{ __('admin.ready_cake_wizard.no_shapes') }}
                                </div>
                            @endforelse
                        </div>
                        @error('cake_shape_id') <p class="text-xs text-danger">{{ $message }}</p> @enderror
                    </div>
                @endif

                @if ($step === 2)
                    <div class="space-y-6 animate-fade-in">
                        <div>
                            <h3 class="text-lg font-semibold text-foreground">{{ __('admin.ready_cake_wizard.flavor_title') }}</h3>
                            <p class="text-sm text-foreground-muted">{{ __('admin.ready_cake_wizard.flavor_subtitle') }}</p>
                        </div>

                        @if (!$cake_shape_id)
                            <div class="rounded-xl border border-border bg-surface-alt/50 p-6 text-center text-sm text-foreground-muted">
                                {{ __('admin.ready_cake_wizard.select_shape_first') }}
                            </div>
                        @else
                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                                @forelse ($flavors as $flavorItem)
                                    <button type="button" wire:click="selectFlavor({{ $flavorItem->id }})"
                                        class="card-base p-4 text-start transition-colors duration-150 flex flex-col items-center text-center
                                        {{ $cake_flavor_id === (string) $flavorItem->id ? 'ring-2 ring-accent/60 border-accent/50 bg-surface-alt/30' : 'hover:bg-surface-alt/60' }}">

                                        @if ($flavorItem->getFirstMediaUrl('thumbnail'))
                                            <img src="{{ $flavorItem->getFirstMediaUrl('thumbnail') }}" alt="{{ $flavorItem->name }}"
                                                class="h-16 w-16 mb-3 rounded-full object-cover border-2 border-border shadow-sm" />
                                        @else
                                            <div class="flex h-16 w-16 mb-3 items-center justify-center rounded-full bg-surface-alt text-foreground-subtle border-2 border-border">
                                                <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909M18 13.5V18a2.25 2.25 0 0 1-2.25 2.25H5.25A2.25 2.25 0 0 1 3 18v-4.5" />
                                                </svg>
                                            </div>
                                        @endif

                                        <p class="text-sm font-semibold text-foreground">{{ $flavorItem->name }}</p>
                                        @php
                                            $extraPrice = $flavorItem->shapes->first()?->pivot?->extra_price ?? 0;
                                        @endphp
                                        @if($extraPrice > 0)
                                            <p class="mt-1 text-xs font-medium text-pink">
                                                +{{ settings(\App\Settings\CurrencySettings::class)->currency_symbol }}{{ number_format($extraPrice, 2) }}
                                            </p>
                                        @else
                                            <p class="mt-1 text-xs text-foreground-muted">{{ __('admin.ready_cake_wizard.included') }}</p>
                                        @endif
                                    </button>
                                @empty
                                    <div class="col-span-full py-8 text-center text-sm text-foreground-muted border-2 border-dashed border-border rounded-xl">
                                        {{ __('admin.ready_cake_wizard.no_flavors') }}
                                    </div>
                                @endforelse
                            </div>
                        @endif
                        @error('cake_flavor_id') <p class="text-xs text-danger">{{ $message }}</p> @enderror
                    </div>
                @endif

                @if ($step === 3)
                    <div class="space-y-6 animate-fade-in">
                         <div>
                            <h3 class="text-lg font-semibold text-foreground">{{ __('admin.ready_cake_wizard.color_title') }}</h3>
                            <p class="text-sm text-foreground-muted">{{ __('admin.ready_cake_wizard.color_subtitle') }}</p>
                        </div>
                        <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 md:grid-cols-4">
                            <button type="button" wire:click="selectColor"
                                class="card-base p-4 text-center transition-colors duration-150 flex flex-col items-center justify-center gap-2
                                {{ $cake_color_id === null && $custom_hex === '#ffffff' ? 'ring-2 ring-accent/60 border-accent/50' : 'hover:bg-surface-alt/60' }}">
                                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-surface-alt text-foreground-subtle border border-border">
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m6 18 12-12M6 6l12 12" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-xs font-semibold text-foreground">{{ __('admin.ready_cake_wizard.no_color') }}</p>
                                </div>
                            </button>

                            <div class="card-base p-4 text-center transition-colors duration-150 relative group flex flex-col items-center gap-2
                                {{ $custom_hex !== '#ffffff' && $cake_color_id === null ? 'ring-2 ring-accent/60 border-accent/50' : 'hover:bg-surface-alt/60' }}">
                                <div class="relative h-10 w-10 overflow-hidden rounded-full border shadow-sm" style="border-color: {{ $custom_hex }}">
                                    <input wire:model.live.debounce.300ms="custom_hex" type="color" 
                                        class="absolute inset-0 h-[150%] w-[150%] -translate-x-1/4 -translate-y-1/4 cursor-pointer border-0 p-0" 
                                        title="{{ __('admin.ready_cake_wizard.choose_custom_color') }}" />
                                </div>
                                <div>
                                    <p class="text-xs font-semibold text-foreground">{{ __('admin.ready_cake_wizard.custom_color') }}</p>
                                </div>
                            </div>

                            @foreach ($colors as $colorItem)
                                <button type="button" wire:click="selectColor({{ $colorItem->id }})"
                                    class="card-base p-4 text-center transition-colors duration-150 flex flex-col items-center gap-2
                                    {{ $cake_color_id === (string) $colorItem->id ? 'ring-2 ring-accent/60 border-accent/50' : 'hover:bg-surface-alt/60' }}">
                                    <div class="h-10 w-10 rounded-full border border-border shadow-sm transform transition-transform group-hover:scale-110"
                                        @style(["background-color: {$colorItem->hex_code}"])></div>
                                    <div>
                                        <p class="text-xs font-semibold text-foreground line-clamp-1">{{ $colorItem->name }}</p>
                                    </div>
                                </button>
                            @endforeach
                        </div>
                        @error('custom_hex') <p class="mt-2 text-xs text-danger">{{ $message }}</p> @enderror
                        @error('cake_color_id') <p class="mt-2 text-xs text-danger">{{ $message }}</p> @enderror
                    </div>
                @endif

                @if ($step === 4)
                    <div class="space-y-6 animate-fade-in">
                         <div>
                            <h3 class="text-lg font-semibold text-foreground">{{ __('admin.ready_cake_wizard.toppings_title') }}</h3>
                            <p class="text-sm text-foreground-muted">{{ __('admin.ready_cake_wizard.toppings_subtitle') }}</p>
                        </div>

                        @if (!$cake_shape_id)
                            <div class="rounded-xl border border-border bg-surface-alt/50 p-6 text-center text-sm text-foreground-muted">
                                {{ __('admin.ready_cake_wizard.select_shape_first_toppings') }}
                            </div>
                        @else
                                                             <!-- Category Tabs -->
                                                             <div class="flex overflow-x-auto border-b border-border pb-px">
                                                                <button type="button" 
                                                                    wire:click="$set('selected_topping_category_id', null)"
                                                                    class="whitespace-nowrap border-b-2 px-4 py-2 text-sm font-medium transition-colors duration-150
                                                                    {{ $selected_topping_category_id === null
                            ? 'border-accent text-accent'
                            : 'border-transparent text-foreground-muted hover:border-border hover:text-foreground' }}">
                                                                    {{ __('admin.ready_cake_wizard.all_toppings') }}
                                                                </button>
                                                                @foreach ($toppingCategories as $category)
                                                                                                                            <button type="button" 
                                                                                                                                wire:click="$set('selected_topping_category_id', '{{ $category->id }}')"
                                                                                                                                class="whitespace-nowrap border-b-2 px-4 py-2 text-sm font-medium transition-colors duration-150
                                                                                                                                {{ $selected_topping_category_id === (string) $category->id
                                                                    ? 'border-accent text-accent'
                                                                    : 'border-transparent text-foreground-muted hover:border-border hover:text-foreground' }}">
                                                                                                                                {{ $category->name }}
                                                                                                                            </button>
                                                                @endforeach
                                                            </div>

                                                            <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-4 pt-2">
                                                                <label
                                                                    class="card-base flex flex-col cursor-pointer items-center text-center gap-3 p-4 transition-colors duration-150 relative
                                                                    {{ $cake_topping_id === null ? 'ring-2 ring-accent/60 border-accent/50 bg-surface-alt/30' : 'hover:bg-surface-alt/60' }}">
                                                                    <input type="radio" value="" wire:model.live="cake_topping_id" class="hidden" />
                                                                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-surface-alt text-foreground-subtle">
                                                                         <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                                                        </svg>
                                                                    </div>
                                                                    <div>
                                                                        <p class="text-sm font-semibold text-foreground">{{ __('admin.ready_cake_wizard.no_topping') }}</p>
                                                                    </div>
                                                                </label>

                                                                @forelse ($toppings as $toppingItem)
                                                                    @php
                                                                        $isSelected = (string) $toppingItem->id === $cake_topping_id;
                                                                    @endphp
                                                                    <label
                                                                        class="card-base flex flex-col cursor-pointer items-center text-center gap-3 p-4 transition-colors duration-150 relative
                                                                        {{ $isSelected ? 'ring-2 ring-accent/60 border-accent/50 bg-surface-alt/30' : 'hover:bg-surface-alt/60' }}">
                                                                        @if($isSelected)
                                                                            <div class="absolute top-2 end-2 text-accent">
                                                                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                                                                </svg>
                                                                            </div>
                                                                        @endif

                                                                        <input type="radio" value="{{ $toppingItem->id }}" wire:model.live="cake_topping_id" class="hidden" />
                                                                        @if ($toppingItem->getFirstMediaUrl('thumbnail'))
                                                                            <img src="{{ $toppingItem->getFirstMediaUrl('thumbnail') }}" alt="{{ $toppingItem->name }}"
                                                                                class="h-12 w-12 rounded-xl object-cover border border-border" />
                                                                        @else
                                                                            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-surface-alt text-foreground-subtle">
                                                                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09Z" />
                                                                                </svg>
                                                                            </div>
                                                                        @endif

                                                                        <div>
                                                                            <p class="text-sm font-semibold text-foreground line-clamp-1">{{ $toppingItem->name }}</p>
                                                                            @php
                                                                                $toppingPrice = $toppingItem->shapes->first()?->pivot?->price ?? 0;
                                                                            @endphp
                                                                            @if($toppingPrice > 0)
                                                                                <p class="mt-0.5 text-xs text-pink font-medium">
                                                                                    +{{ settings(\App\Settings\CurrencySettings::class)->currency_symbol }}{{ number_format($toppingPrice, 2) }}
                                                                                </p>
                                                                            @endif
                                                                        </div>
                                                                    </label>
                                                                @empty
                                                                    <div class="col-span-full py-8 text-center text-sm text-foreground-muted border-2 border-dashed border-border rounded-xl">
                                                                        {{ __('admin.ready_cake_wizard.no_toppings_found') }}
                                                                    </div>
                                                                @endforelse
                                                            </div>

                                                            @if($toppings->hasPages())
                                                                <div class="mt-6">
                                                                    {{ $toppings->links() }}
                                                                </div>
                                                            @endif
                        @endif
                         @error('cake_topping_id') <p class="mt-2 text-xs text-danger">{{ $message }}</p> @enderror
                    </div>
                @endif

                @if ($step === 5)
                    <div class="space-y-8 animate-fade-in">
                        <div>
                            <h3 class="text-lg font-semibold text-foreground">{{ __('admin.ready_cake_wizard.publish_title') }}</h3>
                            <p class="text-sm text-foreground-muted">{{ __('admin.ready_cake_wizard.publish_subtitle') }}</p>
                        </div>

                        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                            <div>
                                <label class="mb-1.5 block text-sm font-medium text-foreground">{{ __('admin.ready_cake_wizard.name_label') }}</label>
                                <input wire:model="name" type="text" class="input-base" placeholder="{{ __('admin.ready_cake_wizard.name_placeholder') }}" />
                                @error('name') <p class="mt-1 text-xs text-danger">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="mb-1.5 block text-sm font-medium text-foreground">{{ __('admin.ready_cake_wizard.price_label', ['currency' => settings(\App\Settings\CurrencySettings::class)->currency_symbol]) }}</label>
                                <input wire:model.blur="price" type="number" step="0.01" min="0" class="input-base" placeholder="{{ __('admin.ready_cake_wizard.price_placeholder') }}" />
                                @error('price') <p class="mt-1 text-xs text-danger">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div class="flex flex-col gap-4 border-t border-border pt-6">
                            <label class="flex items-center gap-3 text-sm text-foreground">
                                <input wire:model="is_active" type="checkbox"
                                    class="h-5 w-5 rounded border-border text-primary focus:ring-ring" />
                                <div>
                                    <p class="font-medium">{{ __('admin.ready_cake_wizard.active_label') }}</p>
                                    <p class="text-xs text-foreground-muted">{{ __('admin.ready_cake_wizard.active_hint') }}</p>
                                </div>
                            </label>
                            <label class="flex items-center gap-3 text-sm text-foreground">
                                <input wire:model="is_customizable" type="checkbox"
                                    class="h-5 w-5 rounded border-border text-primary focus:ring-ring" />
                                <div>
                                    <p class="font-medium">{{ __('admin.ready_cake_wizard.customizable_label') }}</p>
                                    <p class="text-xs text-foreground-muted">{{ __('admin.ready_cake_wizard.customizable_hint') }}</p>
                                </div>
                            </label>
                        </div>
                    </div>
                @endif

                <div class="mt-8 flex items-center justify-between border-t border-border pt-6">
                    <button type="button" wire:click="previousStep" @disabled($step === 1)
                        class="btn-base border border-border bg-surface text-foreground hover:bg-surface-alt {{ $step === 1 ? 'opacity-50 cursor-not-allowed' : '' }}">
                        {{ __('admin.ready_cake_wizard.back') }}
                    </button>

                    @if ($step < 5)
                        <button type="button" wire:click="nextStep"
                            class="btn-base bg-primary text-primary-foreground hover:bg-primary-hover px-8">
                            {{ __('admin.ready_cake_wizard.next_step') }}
                        </button>
                    @else
                        <button type="button" wire:click="save"
                            class="btn-base bg-primary text-primary-foreground hover:bg-primary-hover px-8" wire:loading.attr="disabled">
                            <span wire:loading.remove>{{ __('admin.ready_cake_wizard.publish_ready_cake') }}</span>
                            <span wire:loading class="flex items-center gap-2 text-espresso">
                                <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                    </path>
                                </svg>
                            </span>
                        </button>
                    @endif
                </div>
            </div>
        </div>
        
        {{-- RIGHT COLUMN: Sticky Preview & Summary --}}
        <div class="lg:col-span-5 xl:col-span-4">
            <div class="sticky top-24 space-y-6">
                <!-- Live Visualizer -->
                <div class="card-base overflow-hidden border border-border bg-gradient-to-br from-surface to-surface-alt/50 relative">
                     <div class="absolute inset-0 bg-checkered opacity-[0.25] mix-blend-multiply"></div>
                     <div class="relative p-6 pt-10">
                         <h3 class="absolute top-4 start-0 end-0 text-center font-display text-xs font-bold uppercase tracking-wider text-primary">{{ __('admin.ready_cake_wizard.live_preview') }}</h3>
                         
                         <div class="aspect-square w-full rounded-2xl flex items-center justify-center bg-transparent mt-2">
                             @if ($shape)
                                 @php
                                    // Dynamic preview mode depending on step mapping logic
                                    $pMode = 'final';
                                    if ($step === 1)
                                        $pMode = 'shape';
                                    if ($step === 2)
                                        $pMode = 'flavor';
                                    if ($step === 3)
                                        $pMode = 'color';
                                    if ($step === 4)
                                        $pMode = 'toppings';
                                 @endphp
                                 <x-cake-visual 
                                     class="w-full h-full drop-shadow-xl"
                                     :shape="$shape" 
                                     :flavor-layer="$flavorLayer"
                                     :topping-layers="$toppingLayers" 
                                     :color="$color" 
                                     :mode="$pMode" 
                                 />
                             @else
                                 <div class="text-center text-foreground-muted flex flex-col items-center">
                                     <svg class="h-16 w-16 opacity-30 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                     </svg>
                                     <p class="text-sm font-medium">{{ __('admin.ready_cake_wizard.select_shape_preview') }}</p>
                                 </div>
                             @endif
                         </div>
                     </div>
                </div>

                <!-- Attributes Summary Card -->
                <div class="card-base p-5">
                    <h3 class="font-display text-lg font-semibold text-foreground mb-4">{{ __('admin.ready_cake_wizard.summary') }}</h3>
                    
                    <div class="space-y-3.5 text-sm">
                        <div class="flex justify-between items-center pb-3 border-b border-border">
                            <span class="text-foreground-muted">{{ __('admin.ready_cake_wizard.summary_name') }}</span>
                            <span class="font-medium text-foreground text-end w-1/2 truncate">{{ $name ?: __('admin.ready_cake_wizard.summary_untitled') }}</span>
                        </div>
                        <div class="flex justify-between items-center pb-3 border-b border-border">
                            <span class="text-foreground-muted">{{ __('admin.ready_cake_wizard.summary_shape') }}</span>
                            <span class="font-medium text-foreground text-end w-1/2 truncate">{{ $shape?->name ?? '—' }}</span>
                        </div>
                        <div class="flex justify-between items-center pb-3 border-b border-border">
                            <span class="text-foreground-muted">{{ __('admin.ready_cake_wizard.summary_flavor') }}</span>
                            <span class="font-medium text-foreground text-end w-1/2 truncate">{{ $selectedFlavor?->name ?? '—' }}</span>
                        </div>
                        <div class="flex justify-between items-center pb-3 border-b border-border">
                            <span class="text-foreground-muted">{{ __('admin.ready_cake_wizard.summary_color') }}</span>
                            <span class="font-medium text-foreground flex items-center gap-1.5 justify-end">
                                @if($color || $custom_hex !== '#ffffff')
                                    <span class="h-3 w-3 rounded-full shadow-sm border border-border" style="background-color: {{ $color ? $color->hex_code : $custom_hex }}"></span>
                                @endif
                                {{ $color?->name ? $color->name : ($custom_hex !== '#ffffff' ? __('admin.ready_cake_wizard.custom_color') : __('admin.ready_cake_wizard.summary_none')) }}
                            </span>
                        </div>
                        <div class="flex justify-between items-center pb-3 border-b border-border">
                            <span class="text-foreground-muted">{{ __('admin.ready_cake_wizard.summary_topping') }}</span>
                            <span class="font-medium text-foreground text-end w-1/2 truncate">{{ $selectedTopping?->name ?? __('admin.ready_cake_wizard.summary_none') }}</span>
                        </div>
                        <div class="flex justify-between items-center pt-1">
                            <span class="font-display font-medium text-foreground">{{ __('admin.ready_cake_wizard.summary_final_price') }}</span>
                            <span class="font-display text-xl font-bold text-primary">{{ settings(\App\Settings\CurrencySettings::class)->currency_symbol }}{{ number_format($price ?: 0, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
