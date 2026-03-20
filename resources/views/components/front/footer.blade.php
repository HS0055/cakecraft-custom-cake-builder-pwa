@php
    $general = settings(\App\Settings\GeneralSettings::class);
    $branding = settings(\App\Settings\BrandingSettings::class);
    $appearance = settings(\App\Settings\AppearanceSettings::class);
    $social = settings(\App\Settings\SocialMediaSettings::class);
    $pages = \App\Models\Page::where('is_active', true)->get();
@endphp

<footer class="text-white mt-auto" style="background-color: {{ $appearance->primary_color ?? '#F43F5E' }}">
    <div class="front-container py-10 md:py-14">
        <div class="grid grid-cols-1 md:grid-cols-5 gap-8 md:gap-8">
            {{-- Brand Column --}}
            <div class="md:col-span-2 pe-0 md:pe-8">
                <div class="flex items-center gap-2.5 mb-5">
                    @if($branding->logo_url)
                        <img src="{{ $branding->logo_url }}" alt="{{ $general->store_name }}"
                            class="h-15 object-contain brightness-0 invert">
                    @else
                        <img src="{{ asset('images/logo.png') }}" alt="{{ $general->store_name }}"
                            class="h-15 object-contain brightness-0 invert">
                    @endif
                </div>

                <div class="text-sm text-white/70 space-y-2.5 mb-6">
                    @if($general->store_address)
                        <div class="flex items-start gap-2.5">
                            <svg class="w-4 h-4 shrink-0 mt-0.5 text-white/80" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" />
                            </svg>
                            <span>{{ $general->store_address }}</span>
                        </div>
                    @endif
                    @if($general->store_phone)
                        <div class="flex items-start gap-2.5">
                            <svg class="w-4 h-4 shrink-0 mt-0.5 text-white/80" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 0 0 2.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 0 1-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 0 0-1.091-.852H4.5A2.25 2.25 0 0 0 2.25 4.5v2.25Z" />
                            </svg>
                            <span>{{ $general->store_phone }}</span>
                        </div>
                    @endif
                    @if($general->store_email)
                        <div class="flex items-start gap-2.5">
                            <svg class="w-4 h-4 shrink-0 mt-0.5 text-white/80" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" />
                            </svg>
                            <span>{{ $general->store_email }}</span>
                        </div>
                    @endif
                </div>

                {{-- Social Media Icons --}}
                <div class="flex flex-wrap gap-2">
                    @if($social->facebook_url)
                        <a href="{{ $social->facebook_url }}" target="_blank" rel="noopener noreferrer"
                            class="p-2 rounded-lg text-white/70 hover:text-white hover:bg-white/10 transition-all duration-200"
                            aria-label="Facebook">
                            <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24">
                                <path
                                    d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.469h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.469h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z" />
                            </svg>
                        </a>
                    @endif
                    @if($social->instagram_url)
                        <a href="{{ $social->instagram_url }}" target="_blank" rel="noopener noreferrer"
                            class="p-2 rounded-lg text-white/70 hover:text-white hover:bg-white/10 transition-all duration-200"
                            aria-label="Instagram">
                            <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24">
                                <path
                                    d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z" />
                            </svg>
                        </a>
                    @endif
                    @if($social->twitter_url)
                        <a href="{{ $social->twitter_url }}" target="_blank" rel="noopener noreferrer"
                            class="p-2 rounded-lg text-white/70 hover:text-white hover:bg-white/10 transition-all duration-200"
                            aria-label="X/Twitter">
                            <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24">
                                <path
                                    d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z" />
                            </svg>
                        </a>
                    @endif
                    @if($social->tiktok_url)
                        <a href="{{ $social->tiktok_url }}" target="_blank" rel="noopener noreferrer"
                            class="p-2 rounded-lg text-white/70 hover:text-white hover:bg-white/10 transition-all duration-200"
                            aria-label="TikTok">
                            <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24">
                                <path
                                    d="M12.525.02c1.31-.02 2.61-.01 3.91-.02.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.62-.93-.01 2.92.01 5.84-.02 8.75-.08 1.4-.54 2.79-1.35 3.94-1.31 1.92-3.58 3.17-5.91 3.21-1.43.08-2.86-.31-4.08-1.03-2.02-1.19-3.44-3.37-3.65-5.71-.02-.5-.03-1-.01-1.49.18-1.9 1.12-3.72 2.58-4.96 1.66-1.44 3.98-2.13 6.15-1.72.02 1.48-.04 2.96-.04 4.44-.99-.32-2.15-.23-3.02.37-.63.41-1.11 1.04-1.36 1.75-.21.51-.15 1.07-.14 1.61.24 1.64 1.82 3.02 3.5 2.87 1.12-.01 2.19-.66 2.77-1.61.19-.33.4-.67.41-1.06.1-1.79.06-3.57.07-5.36.01-4.03-.01-8.05.02-12.07z" />
                            </svg>
                        </a>
                    @endif
                    @if($social->whatsapp_number)
                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $social->whatsapp_number) }}" target="_blank"
                            rel="noopener noreferrer"
                            class="p-2 rounded-lg text-white/70 hover:text-white hover:bg-white/10 transition-all duration-200"
                            aria-label="WhatsApp">
                            <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24">
                                <path
                                    d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413H20.46z" />
                            </svg>
                        </a>
                    @endif
                </div>
            </div>

            {{-- Quick Links --}}
            <div>
                <h4 class="text-xs font-semibold text-white uppercase tracking-wider mb-4">
                    {{ __('front.footer.explore') }}
                </h4>
                <ul class="space-y-2.5">
                    <li>
                        <a href="{{ route('front.home') }}" wire:navigate
                            class="text-sm font-medium text-white/70 hover:text-white transition-colors duration-150">
                            {{ __('front.footer.home') }}
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('front.shop') }}" wire:navigate
                            class="text-sm font-medium text-white/70 hover:text-white transition-colors duration-150">
                            {{ __('front.footer.ready_cakes') }}
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('front.cake-builder') }}" wire:navigate
                            class="text-sm font-medium text-white/70 hover:text-white transition-colors duration-150">
                            {{ __('front.footer.build_your_cake') }}
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('front.cart') }}" wire:navigate
                            class="text-sm font-medium text-white/70 hover:text-white transition-colors duration-150">
                            {{ __('front.footer.cart') }}
                        </a>
                    </li>
                </ul>
            </div>

            {{-- Info & Pages Links --}}
            <div>
                <h4 class="text-xs font-semibold text-white uppercase tracking-wider mb-4">
                    {{ __('front.footer.information') }}
                </h4>
                <ul class="space-y-2.5">
                    <li>
                        <a href="{{ route('front.faqs') }}" wire:navigate
                            class="text-sm font-medium text-white/70 hover:text-white transition-colors duration-150">
                            {{ __('front.footer.faqs') }}
                        </a>
                    </li>
                    @foreach($pages as $page)
                        <li>
                            <a href="{{ route('front.pages.show', $page) }}" wire:navigate
                                class="text-sm font-medium text-white/70 hover:text-white transition-colors duration-150">
                                {{ $page->title }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>

            {{-- Newsletter Subscription --}}
            <div class="md:col-span-1">
                @livewire('front::newsletter')
            </div>
        </div>

        {{-- Bottom Bar --}}
        <div class="mt-10 pt-6 border-t border-white/20 flex flex-col md:flex-row items-center justify-between gap-4">
            <p class="text-sm text-white/60">
                &copy; {{ date('Y') }} {{ $general->store_name }}. {{ __('front.footer.all_rights_reserved') }}
            </p>

            {{-- Payment Providers --}}
            <div class="!flex items-center gap-2">
                <div class="bg-white px-2.5 py-1.5 rounded-lg">
                    <img src="{{ asset('images/Stripe.png') }}" alt="Pay with Stripe" class="h-5 w-auto object-contain">
                </div>
                <div class="bg-white px-2.5 py-1.5 rounded-lg">
                    <img src="{{ asset('images/PayPal.png') }}" alt="Pay with PayPal" class="h-5 w-auto object-contain">
                </div>
            </div>
        </div>
    </div>
</footer>