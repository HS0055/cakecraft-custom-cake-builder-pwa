<div>
    {{-- Header --}}
    <section class="border-b border-gray-200">
        <div class="front-container py-4 md:py-8">
            {{-- Breadcrumb (hidden on mobile to save space) --}}
            <div class="hidden md:flex items-center gap-2 text-sm text-gray-400 mb-2">
                <a href="{{ route('front.home') }}" wire:navigate class="hover:text-primary transition-colors">{{ __('front.breadcrumb.home') }}</a>
                <svg class="w-3.5 h-3.5 rtl:rotate-180" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5"/></svg>
                <span class="text-gray-600">{{ __('front.breadcrumb.cake_builder') }}</span>
            </div>
            <h1 class="font-display text-lg md:text-3xl font-bold text-gray-900 mb-3 md:mb-6">{{ __('front.cake_builder.build_your_dream_cake') }}</h1>

            {{-- Step Indicator --}}
            <x-front.step-indicator :steps="$stepLabels" :current="$step" />
        </div>
    </section>

    <div class="front-container py-4 md:py-12 pb-24 lg:pb-12">
        <div class="max-w-7xl mx-auto relative">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-4 md:gap-8 lg:gap-12 relative items-start">
                
                {{-- LEFT COLUMN: Steps --}}
                <div class="order-2 lg:order-1 lg:col-span-7 xl:col-span-8 space-y-4 md:space-y-8">

                     {{-- ═══ STEP 1: Shape ═══ --}}
                    @if($step === 1)
                        <div class="animate-fade-in">
                            <h2 class="text-base md:text-xl font-semibold text-gray-900 mb-1 md:mb-2">{{ __('front.cake_builder.choose_your_shape') }}</h2>
                            <p class="text-gray-400 text-xs md:text-sm mb-4 md:mb-6">{{ __('front.cake_builder.shape_foundation') }}</p>

                            <div class="grid grid-cols-3 sm:grid-cols-3 gap-2 md:gap-4">
                                @foreach($this->shapes as $shape)
                                    <button
                                        wire:click="selectShape({{ $shape->id }})"
                                        wire:key="shape-{{ $shape->id }}"
                                        @class([
                                            'relative rounded-xl p-2 md:p-4 text-center transition-all duration-200 cursor-pointer group',
                                            'bg-white border-2 border-primary scale-[1.02]' => $shapeId === $shape->id,
                                            'bg-white border border-gray-200 hover:border-primary/30' => $shapeId !== $shape->id,
                                        ])
                                    >
                                        @if($shapeId === $shape->id)
                                            <div class="absolute top-1.5 end-1.5 md:top-3 md:end-3 w-4 h-4 md:w-5 md:h-5 bg-primary rounded-full flex items-center justify-center">
                                                <svg class="w-2.5 h-2.5 md:w-3 md:h-3 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                                </svg>
                                            </div>
                                        @endif

                                        @php $thumb = $shape->getFirstMediaUrl('thumbnail'); @endphp
                                        <div class="w-12 h-12 md:w-16 md:h-16 mx-auto mb-2 md:mb-3 rounded-lg bg-primary/5 flex items-center justify-center overflow-hidden">
                                            @if($thumb)
                                                <img src="{{ $thumb }}" alt="{{ $shape->name }}" class="w-full h-full object-contain p-1 md:p-2">
                                            @else
                                                <span class="text-2xl md:text-3xl">🎂</span>
                                            @endif
                                        </div>

                                        <h3 class="font-semibold text-gray-900 text-xs md:text-base mb-0.5 md:mb-1 line-clamp-1">{{ $shape->name }}</h3>
                                        <x-front.price :amount="$shape->base_price" class="text-[10px] md:text-xs text-primary" />
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- ═══ STEP 2: Flavor ═══ --}}
                    @if($step === 2)
                        <div class="animate-fade-in">
                            <h2 class="text-base md:text-xl font-semibold text-gray-900 mb-1 md:mb-2">{{ __('front.cake_builder.pick_your_flavor') }}</h2>
                            <p class="text-gray-400 text-xs md:text-sm mb-4 md:mb-6">{{ __('front.cake_builder.flavor_question', ['shape' => $this->selectedShape?->name ?? __('front.product.shape_name_fallback')]) }}</p>

                            <div class="grid grid-cols-3 sm:grid-cols-3 gap-2 md:gap-4">
                                @foreach($this->flavors as $flavor)
                                    <button
                                        wire:click="selectFlavor({{ $flavor->id }})"
                                        wire:key="flavor-{{ $flavor->id }}"
                                        @class([
                                            'relative rounded-xl p-2 md:p-4 text-center transition-all duration-200 cursor-pointer',
                                            'bg-white border-2 border-primary scale-[1.02]' => $flavorId === $flavor->id,
                                            'bg-white border border-gray-200 hover:border-primary/30' => $flavorId !== $flavor->id,
                                        ])
                                    >
                                        @if($flavorId === $flavor->id)
                                            <div class="absolute top-1.5 end-1.5 md:top-3 md:end-3 w-4 h-4 md:w-5 md:h-5 bg-primary rounded-full flex items-center justify-center">
                                                <svg class="w-2.5 h-2.5 md:w-3 md:h-3 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                                </svg>
                                            </div>
                                        @endif

                                        @php $flavorImg = $flavor->getFirstMediaUrl('thumbnail'); @endphp
                                        <div class="w-10 h-10 md:w-14 md:h-14 mx-auto mb-2 md:mb-3 rounded-lg bg-primary/5 flex items-center justify-center overflow-hidden">
                                            @if($flavorImg)
                                                <img src="{{ $flavorImg }}" alt="{{ $flavor->name }}" class="w-full h-full object-contain p-1 md:p-1.5">
                                            @else
                                                <span class="text-xl md:text-2xl">🍰</span>
                                            @endif
                                        </div>

                                        <h3 class="font-semibold text-gray-900 text-xs md:text-sm line-clamp-1">{{ $flavor->name }}</h3>
                                    </button>
                                @endforeach
                            </div>

                            @if($this->flavors->isEmpty())
                                <x-front.empty-state
                                    :title="__('front.cake_builder.no_flavors_title')"
                                    :message="__('front.cake_builder.no_flavors_message')"
                                />
                            @endif
                        </div>
                    @endif

                    {{-- ═══ STEP 3: Color ═══ --}}
                    @if($step === 3)
                        <div class="animate-fade-in">
                            <h2 class="text-base md:text-xl font-semibold text-gray-900 mb-1 md:mb-2">{{ __('front.cake_builder.choose_your_color') }}</h2>
                            <p class="text-gray-400 text-xs md:text-sm mb-4 md:mb-6">{{ __('front.cake_builder.color_subtitle') }}</p>

                            <div class="grid grid-cols-4 sm:grid-cols-5 md:flex md:flex-wrap gap-2 md:gap-3">
                                {{-- No Color Option --}}
                                <button
                                    wire:click="selectColor(null)"
                                    @class([
                                        'group flex flex-col items-center gap-1.5 md:gap-2 p-2 md:p-3 rounded-xl transition-all duration-200 cursor-pointer',
                                        'bg-primary/5 border-2 border-primary' => $colorId === null && !$isCustomColor && $colorStepHandled,
                                        'bg-white border border-gray-200 hover:border-primary/30' => !($colorId === null && !$isCustomColor && $colorStepHandled),
                                    ])
                                >
                                    <div class="w-10 h-10 md:w-12 md:h-12 rounded-full border-2 border-dashed border-gray-300 flex items-center justify-center text-gray-300 {{ $colorId === null && !$isCustomColor && $colorStepHandled ? 'border-primary text-primary bg-primary/5' : '' }}">
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </div>
                                    <span @class([
                                        'text-[10px] md:text-xs font-medium line-clamp-1',
                                        'text-primary' => $colorId === null && !$isCustomColor && $colorStepHandled,
                                        'text-gray-400' => !($colorId === null && !$isCustomColor && $colorStepHandled),
                                    ])>
                                        {{ __('front.cake_builder.no_color') }}
                                    </span>
                                </button>

                                {{-- Custom Color Picker --}}
                                <div
                                    wire:click="enableCustomColor"
                                    @class([
                                        'group flex flex-col items-center gap-1.5 md:gap-2 p-2 md:p-3 rounded-xl transition-all duration-200 cursor-pointer',
                                        'bg-primary/5 border-2 border-primary' => $isCustomColor,
                                        'bg-white border border-gray-200 hover:border-primary/30' => !$isCustomColor,
                                    ])
                                >
                                    <div class="relative w-10 h-10 md:w-12 md:h-12 rounded-full border-4 overflow-hidden {{ $isCustomColor ? 'border-primary' : 'border-gray-100' }}" style="background-color: {{ $customHex }}">
                                        <input type="color" wire:model.live.debounce.300ms="customHex" class="absolute inset-[-50%] w-[200%] h-[200%] cursor-pointer opacity-0" />
                                        @if($isCustomColor)
                                            <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                                                <svg class="w-5 h-5 text-white mix-blend-difference" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                                </svg>
                                            </div>
                                        @endif
                                    </div>
                                    <span @class([
                                        'text-[10px] md:text-xs font-medium line-clamp-1',
                                        'text-primary' => $isCustomColor,
                                        'text-gray-400' => !$isCustomColor,
                                    ])>
                                        {{ __('front.cake_builder.custom_color') }}
                                    </span>
                                </div>

                                @foreach($this->colors as $color)
                                    <button
                                        wire:click="selectColor({{ $color->id }})"
                                        wire:key="color-{{ $color->id }}"
                                        @class([
                                            'group flex flex-col items-center gap-1.5 md:gap-2 p-2 md:p-3 rounded-xl transition-all duration-200 cursor-pointer',
                                            'bg-primary/5 border-2 border-primary' => $colorId === $color->id,
                                            'bg-white border border-gray-200 hover:border-primary/30' => $colorId !== $color->id,
                                        ])
                                    >
                                        <div
                                            class="w-10 h-10 md:w-12 md:h-12 rounded-full border-4 transition-transform duration-200 group-hover:scale-110 {{ $colorId === $color->id ? 'border-primary scale-110 shadow-lg' : 'border-gray-100' }}"
                                            style="background-color: {{ $color->hex_code }}"
                                        ></div>
                                        <span @class([
                                            'text-[10px] md:text-xs font-medium line-clamp-1',
                                            'text-primary' => $colorId === $color->id,
                                            'text-gray-400' => $colorId !== $color->id,
                                        ])>
                                            {{ $color->name }}
                                        </span>
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- ═══ STEP 4: Toppings ═══ --}}
                    @if($step === 4)
                        <div class="animate-fade-in">
                            <h2 class="text-base md:text-xl font-semibold text-gray-900 mb-1 md:mb-2">{{ __('front.cake_builder.add_toppings') }}</h2>
                            <p class="text-gray-400 text-xs md:text-sm mb-4 md:mb-6">{{ __('front.cake_builder.toppings_subtitle') }}</p>

                            {{-- Category Tabs --}}
                            @if($this->toppingCategories->count())
                                <div class="flex items-center gap-1.5 md:gap-2 overflow-x-auto pb-2 mb-4 md:mb-6 -mx-1 px-1 no-scrollbar">
                                    <button
                                        wire:click="selectCategory(null)"
                                        @class([
                                            'px-3 py-1.5 rounded-lg text-xs font-medium transition-all duration-200 cursor-pointer border whitespace-nowrap shrink-0',
                                            'bg-primary text-white border-primary' => !$selectedCategoryId,
                                            'bg-white text-gray-600 border-gray-200 hover:border-primary/30' => $selectedCategoryId,
                                        ])
                                    >
                                        {{ __('front.cake_builder.all') }}
                                    </button>
                                    @foreach($this->toppingCategories as $cat)
                                        <button
                                            wire:click="selectCategory({{ $cat->id }})"
                                            wire:key="cat-{{ $cat->id }}"
                                            @class([
                                                'px-3 py-1.5 rounded-lg text-xs font-medium transition-all duration-200 cursor-pointer border whitespace-nowrap shrink-0',
                                                'bg-primary text-white border-primary' => $selectedCategoryId === $cat->id,
                                                'bg-white text-gray-600 border-gray-200 hover:border-primary/30' => $selectedCategoryId !== $cat->id,
                                            ])
                                        >
                                            {{ $cat->name }}
                                        </button>
                                    @endforeach
                                </div>
                            @endif

                            {{-- Skip Topping Option --}}
                            <div class="mb-3 md:mb-4">
                                <button
                                    wire:click="selectTopping(null)"
                                    @class([
                                        'inline-flex items-center gap-2 px-3 md:px-4 py-1.5 md:py-2 rounded-lg text-xs md:text-sm transition-all duration-200 cursor-pointer',
                                        'bg-primary/10 text-primary font-medium' => !$toppingId,
                                        'text-gray-400 hover:text-gray-600' => $toppingId,
                                    ])
                                >
                                    <svg class="w-3.5 h-3.5 md:w-4 md:h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 0 0 5.636 5.636m12.728 12.728A9 9 0 0 1 5.636 5.636m12.728 12.728L5.636 5.636" />
                                    </svg>
                                    {{ __('front.cake_builder.no_topping') }}
                                </button>
                            </div>

                            <div class="grid grid-cols-3 md:grid-cols-3 gap-2 md:gap-4">
                                @foreach($this->toppings as $topping)
                                    @php
                                        $toppingPivot = $topping->shapes->where('id', $shapeId)->first()?->pivot;
                                        $toppingPrice = $toppingPivot?->price ?? 0;
                                    @endphp
                                    <button
                                        wire:click="selectTopping({{ $topping->id }})"
                                        wire:key="topping-{{ $topping->id }}"
                                        @class([
                                            'relative rounded-xl p-2 md:p-3 text-center transition-all duration-200 cursor-pointer',
                                            'bg-white border-2 border-primary scale-[1.02]' => $toppingId === $topping->id,
                                            'bg-white border border-gray-200 hover:border-primary/30' => $toppingId !== $topping->id,
                                        ])
                                    >
                                        @if($toppingId === $topping->id)
                                            <div class="absolute top-1 end-1 md:top-2 md:end-2 w-3.5 h-3.5 md:w-4 md:h-4 bg-primary rounded-full flex items-center justify-center">
                                                <svg class="w-2 h-2 md:w-2.5 md:h-2.5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                                </svg>
                                            </div>
                                        @endif

                                        @php $toppingImg = $topping->getFirstMediaUrl('thumbnail'); @endphp
                                        <div class="w-8 h-8 md:w-10 md:h-10 mx-auto mb-1.5 md:mb-2 rounded-lg bg-primary/5 flex items-center justify-center overflow-hidden">
                                            @if($toppingImg)
                                                <img src="{{ $toppingImg }}" alt="{{ $topping->name }}" class="w-full h-full object-contain p-0.5 md:p-1">
                                            @else
                                                <span class="text-base md:text-xl">🍬</span>
                                            @endif
                                        </div>

                                        <p class="text-[10px] md:text-xs font-semibold text-gray-900 mb-0.5 md:mb-1 line-clamp-1">{{ $topping->name }}</p>
                                        @if($toppingPrice > 0)
                                            <span class="text-[9px] md:text-[10px] text-primary font-medium">+<x-front.price :amount="$toppingPrice" class="text-[9px] md:text-[10px]" /></span>
                                        @else
                                            <span class="text-[9px] md:text-[10px] text-gray-400">{{ __('front.cake_builder.free') }}</span>
                                        @endif
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- ═══ STEP 5: Review ═══ --}}
                    @if($step === 5)
                        <div class="animate-fade-in">
                            <h2 class="text-base md:text-xl font-semibold text-gray-900 mb-1 md:mb-2">{{ __('front.cake_builder.review_your_creation') }}</h2>
                            <p class="text-gray-400 text-xs md:text-sm mb-4 md:mb-6">{{ __('front.cake_builder.review_subtitle') }}</p>

                            <div class="rounded-xl border border-gray-200 p-4 md:p-6 mb-4">
                                <div class="space-y-3 md:space-y-4 mb-4 md:mb-6">
                                    <div class="flex items-center justify-between py-1.5 md:py-2 border-b border-gray-100">
                                        <div class="flex items-center gap-2 md:gap-3">
                                            <span class="text-[10px] md:text-xs text-gray-400 uppercase tracking-wider w-14 md:w-16">{{ __('front.cake_builder.shape') }}</span>
                                            <span class="font-semibold text-gray-900 text-sm md:text-base">{{ $this->selectedShape?->name ?? '—' }}</span>
                                        </div>
                                        <x-front.price :amount="$this->selectedShape?->base_price ?? 0" class="text-xs md:text-sm text-gray-600" />
                                    </div>

                                    <div class="flex items-center justify-between py-1.5 md:py-2 border-b border-gray-100">
                                        <div class="flex items-center gap-2 md:gap-3">
                                            <span class="text-[10px] md:text-xs text-gray-400 uppercase tracking-wider w-14 md:w-16">{{ __('front.cake_builder.flavor') }}</span>
                                            <span class="font-semibold text-gray-900 text-sm md:text-base">{{ $this->selectedFlavor?->name ?? '—' }}</span>
                                        </div>
                                    </div>

                                    <div class="flex items-center justify-between py-1.5 md:py-2 border-b border-gray-100">
                                        <div class="flex items-center gap-2 md:gap-3">
                                            <span class="text-[10px] md:text-xs text-gray-400 uppercase tracking-wider w-14 md:w-16">{{ __('front.cake_builder.color') }}</span>
                                            <span class="font-semibold text-gray-900 text-sm md:text-base flex items-center gap-2">
                                                @if($this->selectedColor)
                                                    <div class="w-3 h-3 rounded-full border border-gray-200" style="background-color: {{ $this->selectedColor->hex_code }}"></div>
                                                @endif
                                                {{ $this->selectedColor?->name ?? '—' }}
                                            </span>
                                        </div>
                                    </div>

                                    <div class="flex items-center justify-between py-1.5 md:py-2">
                                        <div class="flex items-center gap-2 md:gap-3">
                                            <span class="text-[10px] md:text-xs text-gray-400 uppercase tracking-wider w-14 md:w-16">{{ __('front.cake_builder.topping') }}</span>
                                            <span class="font-semibold text-gray-900 text-sm md:text-base">{{ $this->selectedTopping?->name ?? __('front.cake_builder.none') }}</span>
                                        </div>
                                        @if($this->selectedTopping)
                                            @php
                                                $tpPivot = $this->selectedTopping->shapes->where('id', $shapeId)->first()?->pivot;
                                            @endphp
                                            @if($tpPivot && $tpPivot->price > 0)
                                                <span class="text-xs md:text-sm text-gray-400">+<x-front.price :amount="$tpPivot->price" class="text-xs md:text-sm" /></span>
                                            @endif
                                        @endif
                                    </div>
                                </div>

                                {{-- Total & Add --}}
                                <div class="p-3 md:p-4 rounded-xl bg-primary/5 flex items-center justify-between mb-3 md:mb-4">
                                    <span class="font-semibold text-gray-900 text-sm md:text-base">{{ __('front.cake_builder.total') }}</span>
                                    <x-front.price :amount="$this->totalPrice" class="text-xl md:text-2xl text-primary" />
                                </div>

                                <x-front.btn wire:click="addToCart" variant="primary" size="lg" class="w-full">
                                    {{ __('front.cake_builder.add_to_cart') }}
                                </x-front.btn>
                            </div>
                        </div>
                    @endif


                    {{-- ═══ Desktop Navigation Buttons ═══ --}}
                    <div class="hidden lg:flex items-center justify-between mt-8 pt-6 border-t border-gray-200">
                        @if($step > 1)
                            <x-front.btn wire:click="prevStep" variant="secondary" size="md">
                                {{ __('front.cake_builder.back') }}
                            </x-front.btn>
                        @else
                            <div></div>
                        @endif

                        @if($step < $totalSteps)
                            @php
                                $nextDisabled = ($step === 1 && !$shapeId) ||
                                    ($step === 2 && !$flavorId) ||
                                    ($step === 3 && !$colorStepHandled);
                            @endphp
                            <button
                                wire:click="nextStep"
                                class="inline-flex items-center gap-2 px-6 py-3 rounded-xl font-semibold text-sm transition-all duration-200 bg-primary text-white hover:opacity-90 disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer"
                                {{ $nextDisabled ? 'disabled' : '' }}
                            >
                                {{ __('front.cake_builder.next') }}
                                <svg class="w-4 h-4 rtl:rotate-180" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                                </svg>
                            </button>
                        @endif
                    </div>
                </div>


                {{-- RIGHT COLUMN: Preview --}}
                <div class="order-1 lg:order-2 lg:col-span-5 xl:col-span-4 z-40 relative">
                    <div class="lg:sticky lg:top-24 space-y-4">
                        {{-- Mobile: Collapsible compact preview --}}
                        <div x-data="{ expanded: false }" class="lg:hidden">
                            <button @click="expanded = !expanded" 
                                class="w-full flex items-center justify-between rounded-xl border border-gray-200 p-3 transition-colors"
                                :class="expanded ? 'rounded-b-none border-b-0' : ''">
                                <div class="flex items-center gap-3">
                                    {{-- Mini preview --}}
                                    <div class="w-12 h-12 rounded-lg bg-gray-50 border border-gray-100 flex items-center justify-center overflow-hidden shrink-0">
                                        @if($this->visualShape)
                                            <x-cake-visual 
                                                class="w-full h-full"
                                                :shape="$this->visualShape" 
                                                :flavorLayer="$this->visualFlavorLayer"
                                                :toppingLayers="$this->visualToppingLayers"
                                                :color="$this->selectedColor"
                                                :mode="$this->previewMode" 
                                            />
                                        @else
                                            <svg class="w-6 h-6 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8.25v-1.5m0 1.5c-1.355 0-2.697.056-4.024.166C6.845 8.51 6 9.473 6 10.608v2.513m6-4.871c1.355 0 2.697.056 4.024.166C17.155 8.51 18 9.473 18 10.608v2.513M15 8.25v-1.5m-6 1.5v-1.5m12 9.75-1.5.75a3.354 3.354 0 0 1-3 0 3.354 3.354 0 0 0-3 0 3.354 3.354 0 0 1-3 0 3.354 3.354 0 0 0-3 0 3.354 3.354 0 0 1-3 0L3 16.5m15-3.379a48.474 48.474 0 0 0-6-.371c-2.032 0-4.034.126-6 .371m12 0c.39.049.777.102 1.163.16 1.07.16 1.837 1.094 1.837 2.175v5.169c0 .621-.504 1.125-1.125 1.125H4.125A1.125 1.125 0 0 1 3 20.625v-5.17c0-1.08.768-2.014 1.837-2.174A47.78 47.78 0 0 1 6 13.12" />
                                            </svg>
                                        @endif
                                    </div>
                                    <div class="text-start">
                                        <p class="text-sm font-semibold text-gray-900">{{ __('front.cake_builder.live_preview') }}</p>
                                        @if($this->totalPrice > 0)
                                            <x-front.price :amount="$this->totalPrice" class="text-xs text-primary font-medium" />
                                        @else
                                            <p class="text-xs text-gray-400">{{ __('front.cake_builder.tap_to_expand') }}</p>
                                        @endif
                                    </div>
                                </div>
                                <svg class="w-5 h-5 text-gray-400 transition-transform duration-200" :class="expanded ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                                </svg>
                            </button>
                            <div x-show="expanded" x-collapse class="rounded-b-xl border border-t-0 border-gray-200 p-3 bg-white">
                                <div class="aspect-square w-full max-w-[280px] mx-auto bg-gray-50 rounded-xl flex items-center justify-center p-3 border border-gray-100">
                                    @if($this->visualShape)
                                        <x-cake-visual 
                                            class="w-full h-full"
                                            :shape="$this->visualShape" 
                                            :flavorLayer="$this->visualFlavorLayer"
                                            :toppingLayers="$this->visualToppingLayers"
                                            :color="$this->selectedColor"
                                            :mode="$this->previewMode" 
                                        />
                                    @else
                                        <div class="text-center text-gray-400">
                                            <svg class="w-10 h-10 mx-auto mb-2 opacity-40" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8.25v-1.5m0 1.5c-1.355 0-2.697.056-4.024.166C6.845 8.51 6 9.473 6 10.608v2.513m6-4.871c1.355 0 2.697.056 4.024.166C17.155 8.51 18 9.473 18 10.608v2.513M15 8.25v-1.5m-6 1.5v-1.5m12 9.75-1.5.75a3.354 3.354 0 0 1-3 0 3.354 3.354 0 0 0-3 0 3.354 3.354 0 0 1-3 0 3.354 3.354 0 0 0-3 0 3.354 3.354 0 0 1-3 0L3 16.5m15-3.379a48.474 48.474 0 0 0-6-.371c-2.032 0-4.034.126-6 .371m12 0c.39.049.777.102 1.163.16 1.07.16 1.837 1.094 1.837 2.175v5.169c0 .621-.504 1.125-1.125 1.125H4.125A1.125 1.125 0 0 1 3 20.625v-5.17c0-1.08.768-2.014 1.837-2.174A47.78 47.78 0 0 1 6 13.12" />
                                            </svg>
                                            <p class="text-xs">{{ __('front.cake_builder.start_building_preview') }}</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- Desktop: Full preview panel --}}
                        <div class="hidden lg:block rounded-xl border border-gray-200 p-6 relative overflow-hidden">
                            <h3 class="text-lg font-semibold text-center text-gray-900 mb-4">{{ __('front.cake_builder.live_preview') }}</h3>
                            
                            {{-- Visual Preview --}}
                            <div class="aspect-square w-full bg-gray-50 rounded-xl mb-4 flex items-center justify-center p-4 border border-gray-100">
                                @if($this->visualShape)
                                    <x-cake-visual 
                                        class="w-full h-full"
                                        :shape="$this->visualShape" 
                                        :flavorLayer="$this->visualFlavorLayer"
                                        :toppingLayers="$this->visualToppingLayers"
                                        :color="$this->selectedColor"
                                        :mode="$this->previewMode" 
                                    />
                                @else
                                    <div class="text-center text-gray-400">
                                        <svg class="w-12 h-12 mx-auto mb-2 opacity-40" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8.25v-1.5m0 1.5c-1.355 0-2.697.056-4.024.166C6.845 8.51 6 9.473 6 10.608v2.513m6-4.871c1.355 0 2.697.056 4.024.166C17.155 8.51 18 9.473 18 10.608v2.513M15 8.25v-1.5m-6 1.5v-1.5m12 9.75-1.5.75a3.354 3.354 0 0 1-3 0 3.354 3.354 0 0 0-3 0 3.354 3.354 0 0 1-3 0 3.354 3.354 0 0 0-3 0 3.354 3.354 0 0 1-3 0L3 16.5m15-3.379a48.474 48.474 0 0 0-6-.371c-2.032 0-4.034.126-6 .371m12 0c.39.049.777.102 1.163.16 1.07.16 1.837 1.094 1.837 2.175v5.169c0 .621-.504 1.125-1.125 1.125H4.125A1.125 1.125 0 0 1 3 20.625v-5.17c0-1.08.768-2.014 1.837-2.174A47.78 47.78 0 0 1 6 13.12" />
                                        </svg>
                                        <p class="text-xs">{{ __('front.cake_builder.start_building_preview') }}</p>
                                    </div>
                                @endif
                            </div>

                            @if($this->totalPrice > 0)
                                <div class="text-center animate-fade-in">
                                    <p class="text-xs text-gray-400 uppercase tracking-wider mb-1">{{ __('front.cake_builder.estimated_total') }}</p>
                                    <x-front.price :amount="$this->totalPrice" class="text-2xl text-primary" />
                                </div>
                            @endif
                        </div>

                         {{-- Current Selection Pills (desktop only) --}}
                         <div class="hidden lg:flex flex-wrap gap-2 justify-center">
                            @if($this->selectedShape)
                                <span class="px-3 py-1 bg-white border border-gray-200 rounded-lg text-xs font-medium text-gray-700 shadow-sm">
                                    {{ $this->selectedShape->name }}
                                </span>
                            @endif
                            @if($this->selectedFlavor)
                                <span class="px-3 py-1 bg-white border border-gray-200 rounded-lg text-xs font-medium text-gray-700 shadow-sm">
                                    {{ $this->selectedFlavor->name }}
                                </span>
                            @endif
                            @if($this->selectedColor)
                                <span class="px-3 py-1 bg-white border border-gray-200 rounded-lg text-xs font-medium text-gray-700 shadow-sm flex items-center gap-1">
                                    <span class="w-2 h-2 rounded-full" style="background-color: {{ $this->selectedColor->hex_code }}"></span>
                                    {{ $this->selectedColor->name }}
                                </span>
                            @endif
                         </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- ═══ Mobile Builder Bar (replaces bottom nav) ═══ --}}
    <div class="md:hidden fixed bottom-0 start-0 w-full z-50 px-4 pb-safe pt-2 pointer-events-none">
        <nav class="pointer-events-auto relative mx-auto max-w-sm flex items-center justify-between px-2 py-2 bg-white border border-gray-200 shadow-lg rounded-2xl text-gray-700 mb-2">
            {{-- Back --}}
            @if($step > 1)
                <button wire:click="prevStep" class="flex flex-col items-center justify-center w-16 h-12 group relative rounded-xl transition-colors duration-150 hover:bg-primary/5 cursor-pointer">
                    <div class="text-gray-500 group-hover:text-primary transition-colors duration-150">
                        <svg class="w-5 h-5 rtl:rotate-180" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                        </svg>
                    </div>
                    <span class="text-[9px] font-semibold mt-0.5 text-gray-500 transition-colors">{{ __('front.cake_builder.back') }}</span>
                </button>
            @else
                <div class="w-16 h-12"></div>
            @endif

            {{-- Center: Step info + price --}}
            <div class="flex flex-col items-center justify-center h-12">
                <span class="text-[10px] font-semibold text-gray-500">{{ __('front.cake_builder.step_of', ['current' => $step, 'total' => $totalSteps]) }}</span>
                @if($this->totalPrice > 0)
                    <x-front.price :amount="$this->totalPrice" class="text-xs font-bold text-primary" />
                @endif
            </div>

            {{-- Next / Add to Cart --}}
            @if($step < $totalSteps)
                @php
                    $nextDisabled = ($step === 1 && !$shapeId) ||
                        ($step === 2 && !$flavorId) ||
                        ($step === 3 && !$colorStepHandled);
                @endphp
                <button
                    wire:click="nextStep"
                    class="flex flex-col items-center justify-center w-16 h-12 group relative rounded-xl transition-colors duration-150 cursor-pointer {{ $nextDisabled ? 'opacity-30 cursor-not-allowed' : 'hover:bg-primary/5' }}"
                    {{ $nextDisabled ? 'disabled' : '' }}
                >
                    <div class="{{ $nextDisabled ? 'text-gray-300' : 'text-primary' }} transition-colors duration-150">
                        <svg class="w-5 h-5 rtl:rotate-180" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                        </svg>
                    </div>
                    <span class="text-[9px] font-semibold mt-0.5 {{ $nextDisabled ? 'text-gray-300' : 'text-primary' }} transition-colors">{{ __('front.cake_builder.next') }}</span>
                </button>
            @elseif($step === $totalSteps)
                <button wire:click="addToCart" class="flex flex-col items-center justify-center w-16 h-12 group relative rounded-xl transition-colors duration-150 hover:bg-primary/5 cursor-pointer">
                    <div class="text-primary transition-colors duration-150">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 0 0-16.536-1.84M7.5 14.25 5.106 5.272M6 20.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Zm12.75 0a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z" />
                        </svg>
                    </div>
                    <span class="text-[9px] font-semibold mt-0.5 text-primary transition-colors">{{ __('front.cake_builder.add') }}</span>
                </button>
            @endif
        </nav>
    </div>

    <style>
        .pb-safe {
            padding-bottom: env(safe-area-inset-bottom, 1rem);
        }
    </style>
</div>
