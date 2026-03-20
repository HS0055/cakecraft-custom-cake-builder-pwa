<div>
    @push('seo')
        @php
            $generalSettings = settings(\App\Settings\GeneralSettings::class);
            $brandingSettings = settings(\App\Settings\BrandingSettings::class);

            $websiteSchema = [
                '@context' => 'https://schema.org',
                '@type' => 'WebSite',
                'name' => $generalSettings->store_name,
                'url' => url('/'),
                'potentialAction' => [
                    '@type' => 'SearchAction',
                    'target' => route('front.shop') . '?search={search_term_string}',
                    'query-input' => 'required name=search_term_string',
                ]
            ];

            $localBusinessSchema = [
                '@context' => 'https://schema.org',
                '@type' => 'LocalBusiness',
                'name' => $generalSettings->store_name,
                'url' => url('/'),
                'priceRange' => '$$',
            ];

            if ($brandingSettings->logo_url) {
                $localBusinessSchema['logo'] = $brandingSettings->logo_url;
            }
            if ($generalSettings->store_email) {
                $localBusinessSchema['email'] = $generalSettings->store_email;
            }
            if ($generalSettings->store_phone) {
                $localBusinessSchema['telephone'] = $generalSettings->store_phone;
            }
            if ($generalSettings->store_address) {
                $localBusinessSchema['address'] = $generalSettings->store_address;
            }
        @endphp
        <script type="application/ld+json">
                                        {!! json_encode($websiteSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
                                    </script>
        <script type="application/ld+json">
                                        {!! json_encode($localBusinessSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
                                    </script>
    @endpush

    <livewire:front.slider />

    <livewire:front.shop-by-shape />

    <x-front.cta-banner />

    {{-- ═══════════════════════════════════════════════════════
    FEATURED CAKES SHOWCASE
    ═══════════════════════════════════════════════════════ --}}
    <section class="py-10 md:py-14">
        <div class="front-container">
            {{-- Section Header --}}
            <div class="flex items-center justify-between pb-4 mb-6 border-b border-gray-200">
                <h2 class="font-display text-xl md:text-2xl font-bold text-gray-900">
                    {{ __('front.home.sweet_collection') }}
                </h2>
                <a href="{{ route('front.shop') }}" wire:navigate
                    class="text-sm font-medium text-primary hover:text-primary-hover transition-colors duration-150">
                    {{ __('front.home.view_all_cakes') }}
                </a>
            </div>

            @if($featuredCakes->count())
                {{-- Product Grid --}}
                <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-4 md:gap-6">
                    @foreach($featuredCakes as $cake)
                        <x-front.cake-card :cake="$cake" wire="addToCart({{ $cake->id }})" wire:key="cake-{{ $cake->id }}" />
                    @endforeach
                </div>
            @else
                <x-front.empty-state :title="__('front.shop.coming_soon')"
                    :message="__('front.shop.empty_state_message')" />
            @endif
        </div>
    </section>
</div>