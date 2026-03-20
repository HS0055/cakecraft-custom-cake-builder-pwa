<div>
    @push('seo')
        @php
            $brandingSettings = settings(\App\Settings\BrandingSettings::class);
            $generalSettings = settings(\App\Settings\GeneralSettings::class);

            $productSchema = [
                '@context' => 'https://schema.org',
                '@type' => 'Product',
                'name' => $readyCake->name,
                'description' => $readyCake->description ?? 'Premium handcrafted cake',
                'sku' => 'RC-' . $readyCake->id,
                'brand' => [
                    '@type' => 'Brand',
                    'name' => $generalSettings->store_name,
                ],
                'offers' => [
                    '@type' => 'Offer',
                    'priceCurrency' => 'USD',
                    'price' => number_format($readyCake->price, 2, '.', ''),
                    'availability' => 'https://schema.org/InStock',
                    'url' => request()->url(),
                ]
            ];

            if ($image = $readyCake->getFirstMediaUrl('preview')) {
                $productSchema['image'] = [$image];
            }
        @endphp
        <script type="application/ld+json">
                                                                                        {!! json_encode($productSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
                                                                                    </script>
    @endpush

    {{-- Page Header --}}
    <section class="border-b border-gray-200">
        <div class="front-container py-6 md:py-8">
            <div class="flex items-center gap-2 text-sm text-gray-400 mb-2">
                <a href="{{ route('front.home') }}" wire:navigate
                    class="hover:text-primary transition-colors">{{ __('front.breadcrumb.home') }}</a>
                <svg class="w-3.5 h-3.5 rtl:rotate-180" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                    stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                </svg>
                <a href="{{ route('front.shop') }}" wire:navigate
                    class="hover:text-primary transition-colors">{{ __('front.breadcrumb.ready_cakes') }}</a>
                <svg class="w-3.5 h-3.5 rtl:rotate-180" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                    stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                </svg>
                <span class="text-gray-600 truncate max-w-[200px]">{{ $readyCake->name }}</span>
            </div>
            <h1 class="font-display text-2xl md:text-3xl font-bold text-gray-900">{{ $readyCake->name }}</h1>
        </div>
    </section>

    {{-- Content --}}
    <div class="front-container py-8 md:py-12">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-14 items-start">
            {{-- Visual Column --}}
            <div>
                <div class="aspect-square w-full rounded-2xl overflow-hidden bg-gray-50 border border-gray-200">
                    @if($readyCake->getFirstMediaUrl('preview'))
                        <img src="{{ $readyCake->getFirstMediaUrl('preview') }}" alt="{{ $readyCake->name }}"
                            class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex items-center justify-center p-8">
                            <x-cake-visual class="w-full h-full" :shape="$readyCake->cakeShape"
                                :color="$readyCake->cakeColor" :toppingLayers="$this->shapeToppingLayers" mode="final" />
                        </div>
                    @endif
                </div>
            </div>

            {{-- Details Column --}}
            <div class="flex flex-col gap-6">
                {{-- Badges --}}
                <div class="flex items-center gap-2 flex-wrap">
                    <x-front.badge :label="$readyCake->cakeShape?->name" color="peach" />
                    @if($readyCake->cakeFlavor?->name)
                        <x-front.badge :label="$readyCake->cakeFlavor->name" color="blue" />
                    @endif
                    @if($readyCake->is_customizable)
                        <span
                            class="inline-flex items-center gap-1 px-3 py-1 text-xs font-semibold text-primary bg-primary/10 rounded-full">
                            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M9.53 16.122a3 3 0 0 0-5.78 1.128 2.25 2.25 0 0 1-2.4 2.245 4.5 4.5 0 0 0 8.4-2.245c0-.399-.078-.78-.22-1.128Zm0 0a15.998 15.998 0 0 0 3.388-1.62m-5.043-.025a15.994 15.994 0 0 1 1.622-3.395m3.42 3.42a15.995 15.995 0 0 0 4.764-4.648l3.876-5.814a1.151 1.151 0 0 0-1.597-1.597L14.146 6.32a15.996 15.996 0 0 0-4.649 4.763m3.42 3.42a6.776 6.776 0 0 0-3.42-3.42" />
                            </svg>
                            {{ __('front.shop.customizable') }}
                        </span>
                    @endif
                </div>

                {{-- Price --}}
                <x-front.price :amount="$readyCake->price" class="text-3xl font-bold text-primary" />

                {{-- Description --}}
                @if($readyCake->description)
                    <p class="text-gray-500 leading-relaxed">{{ $readyCake->description }}</p>
                @endif

                {{-- Cake Specs --}}
                <div class="rounded-xl border border-gray-200 p-5 md:p-6">
                    <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wider mb-4">
                        {{ __('front.shop.cake_details') }}
                    </h3>
                    <div class="grid grid-cols-2 gap-y-4 gap-x-6">
                        <div class="flex items-center gap-3">
                            <div
                                class="w-9 h-9 rounded-lg bg-primary/5 flex items-center justify-center shrink-0 overflow-hidden">
                                @if($readyCake->cakeShape?->getFirstMediaUrl('thumbnail'))
                                    <img src="{{ $readyCake->cakeShape->getFirstMediaUrl('thumbnail') }}"
                                        alt="{{ $readyCake->cakeShape->name }}" class="w-full h-full object-cover">
                                @else
                                    <svg class="w-4.5 h-4.5 text-primary" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor" stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M21 7.5l-2.25-1.313M21 7.5v2.25m0-2.25l-2.25 1.313M3 7.5l2.25-1.313M3 7.5l2.25 1.313M3 7.5v2.25m9 3l2.25-1.313M12 12.75l-2.25-1.313M12 12.75V15m0 6.75l2.25-1.313M12 21.75V19.5m0 2.25l-2.25-1.313m0-16.875L12 2.25l2.25 1.313M21 14.25v2.25l-2.25 1.313m-13.5 0L3 16.5v-2.25" />
                                    </svg>
                                @endif
                            </div>
                            <div>
                                <span class="block text-xs text-gray-400">{{ __('front.shop.shape') }}</span>
                                <span
                                    class="text-sm font-medium text-gray-900">{{ $readyCake->cakeShape?->name ?? __('front.product.empty_property') }}</span>
                            </div>
                        </div>

                        <div class="flex items-center gap-3">
                            <div
                                class="w-9 h-9 rounded-lg bg-primary/5 flex items-center justify-center shrink-0 overflow-hidden">
                                @if($readyCake->cakeFlavor?->getFirstMediaUrl('thumbnail'))
                                    <img src="{{ $readyCake->cakeFlavor->getFirstMediaUrl('thumbnail') }}"
                                        alt="{{ $readyCake->cakeFlavor->name }}" class="w-full h-full object-cover">
                                @else
                                    <svg class="w-4.5 h-4.5 text-primary" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor" stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M12 8.25v-1.5m0 1.5c-1.355 0-2.697.056-4.024.166C6.845 8.51 6 9.473 6 10.608v2.513m6-4.871c1.355 0 2.697.056 4.024.166C17.155 8.51 18 9.473 18 10.608v2.513M15 8.25v-1.5m-6 1.5v-1.5m12 9.75-1.5.75a3.354 3.354 0 0 1-3 0 3.354 3.354 0 0 0-3 0 3.354 3.354 0 0 1-3 0 3.354 3.354 0 0 0-3 0 3.354 3.354 0 0 1-3 0L3 16.5m15-3.379a48.474 48.474 0 0 0-6-.371c-2.032 0-4.034.126-6 .371m12 0c.39.049.777.102 1.163.16 1.07.16 1.837 1.094 1.837 2.175v5.169c0 .621-.504 1.125-1.125 1.125H4.125A1.125 1.125 0 0 1 3 20.625v-5.17c0-1.08.768-2.014 1.837-2.174A47.78 47.78 0 0 1 6 13.12M12.265 3.11a.375.375 0 1 1-.53 0L12 2.845l.265.265Z" />
                                    </svg>
                                @endif
                            </div>
                            <div>
                                <span class="block text-xs text-gray-400">{{ __('front.shop.flavor') }}</span>
                                <span
                                    class="text-sm font-medium text-gray-900">{{ $readyCake->cakeFlavor?->name ?? __('front.product.empty_property') }}</span>
                            </div>
                        </div>

                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-lg bg-primary/5 flex items-center justify-center shrink-0">
                                @if($readyCake->cakeColor?->hex_code)
                                    <span class="w-5 h-5 rounded-full border-2 border-white shadow-sm"
                                        style="background-color: {{ $readyCake->cakeColor->hex_code }}"></span>
                                @else
                                    <svg class="w-4.5 h-4.5 text-primary" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor" stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M4.098 19.902a3.75 3.75 0 0 0 5.304 0l6.401-6.402M6.75 21A3.75 3.75 0 0 1 3 17.25V4.125C3 3.504 3.504 3 4.125 3h5.25c.621 0 1.125.504 1.125 1.125v4.072M6.75 21a3.75 3.75 0 0 0 3.75-3.75V8.197M6.75 21h13.125c.621 0 1.125-.504 1.125-1.125v-5.25c0-.621-.504-1.125-1.125-1.125h-4.072M10.5 8.197l2.88-2.88c.438-.439 1.15-.439 1.59 0l3.712 3.713c.44.44.44 1.152 0 1.59l-2.879 2.88M6.75 17.25h.008v.008H6.75v-.008Z" />
                                    </svg>
                                @endif
                            </div>
                            <div>
                                <span class="block text-xs text-gray-400">{{ __('front.shop.color') }}</span>
                                <span
                                    class="text-sm font-medium text-gray-900">{{ $readyCake->cakeColor?->name ?? __('front.product.empty_property') }}</span>
                            </div>
                        </div>

                        @if($readyCake->cakeTopping)
                            <div class="flex items-center gap-3">
                                <div
                                    class="w-9 h-9 rounded-lg bg-primary/5 flex items-center justify-center shrink-0 overflow-hidden">
                                    @if($readyCake->cakeTopping->getFirstMediaUrl('thumbnail'))
                                        <img src="{{ $readyCake->cakeTopping->getFirstMediaUrl('thumbnail') }}"
                                            alt="{{ $readyCake->cakeTopping->name }}" class="w-full h-full object-cover">
                                    @else
                                        <svg class="w-4.5 h-4.5 text-primary" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor" stroke-width="1.5">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09ZM18.259 8.715 18 9.75l-.259-1.035a3.375 3.375 0 0 0-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 0 0 2.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 0 0 2.455 2.456L21.75 6l-1.036.259a3.375 3.375 0 0 0-2.455 2.456Z" />
                                        </svg>
                                    @endif
                                </div>
                                <div>
                                    <span class="block text-xs text-gray-400">{{ __('front.shop.topping') }}</span>
                                    <span
                                        class="text-sm font-medium text-gray-900">{{ $readyCake->cakeTopping->name }}</span>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="flex flex-col gap-3 pt-2">
                    {{-- Quantity & Add to Cart Row --}}
                    <div class="flex flex-row items-center gap-3 w-full">
                        {{-- Quantity Selector --}}
                        <div class="flex items-center shrink-0">
                            <x-front.quantity-control :value="$quantity" wireIncrement="incrementQuantity"
                                wireDecrement="decrementQuantity" size="lg" />
                        </div>
                        <x-front.btn wire:click="addToCart" variant="primary" size="lg"
                            class="flex-1 justify-center whitespace-nowrap">
                            <span wire:loading.remove wire:target="addToCart" class="!flex items-center gap-2">
                                <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                    stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 0 0-16.536-1.84M7.5 14.25 5.106 5.272M6 20.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Zm12.75 0a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z" />
                                </svg>
                                {{ __('front.shop.add_to_cart') }}
                            </span>
                            <span wire:loading class="flex items-center gap-2 text-espresso">
                                <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                        stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                    </path>
                                </svg>
                            </span>
                        </x-front.btn>
                    </div>

                    {{-- Customize Row --}}
                    @if($readyCake->is_customizable)
                        <x-front.btn href="{{ route('front.cake-builder', ['ready_cake' => $readyCake->id]) }}"
                            variant="outline" size="lg" class="w-full justify-center whitespace-nowrap mt-1" wire:navigate>
                            <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M9.53 16.122a3 3 0 0 0-5.78 1.128 2.25 2.25 0 0 1-2.4 2.245 4.5 4.5 0 0 0 8.4-2.245c0-.399-.078-.78-.22-1.128Zm0 0a15.998 15.998 0 0 0 3.388-1.62m-5.043-.025a15.994 15.994 0 0 1 1.622-3.395m3.42 3.42a15.995 15.995 0 0 0 4.764-4.648l3.876-5.814a1.151 1.151 0 0 0-1.597-1.597L14.146 6.32a15.996 15.996 0 0 0-4.649 4.763m3.42 3.42a6.776 6.776 0 0 0-3.42-3.42" />
                            </svg>
                            {{ __('front.shop.customize_design') }}
                        </x-front.btn>
                    @endif
                </div>

                {{-- Continue Shopping --}}
                <a href="{{ route('front.shop') }}" wire:navigate
                    class="inline-flex items-center gap-1.5 text-sm text-gray-400 hover:text-primary transition-colors mt-2">
                    <svg class="w-4 h-4 rtl:rotate-180" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                        stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                    </svg>
                    {{ __('front.shop.continue_shopping') }}
                </a>
            </div>
        </div>
    </div>
</div>